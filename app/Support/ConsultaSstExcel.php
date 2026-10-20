<?php

namespace App\Support;

use App\Models\ConceptoMedico;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Libro de Excel de Consulta SST:
 * - Hoja "Estadísticas": indicadores y distribuciones (concepto, tipo, mes, énfasis,
 *   restricciones, servicio, cargo y médico) con barras de datos.
 * - Hoja "Conceptos": el listado filtrado con los datos del certificado (sin la historia clínica).
 *
 * Usa XlsxLigero (PHP puro), por lo que no depende de librerías de Composer ni de extensiones
 * de PHP adicionales: funciona en cualquier hosting. Los textos se escriben siempre como texto:
 * un valor que empiece por "=" nunca se interpreta como fórmula.
 */
class ConsultaSstExcel
{
    private const MARCA = '2E3A75';
    private const SUAVE = 'EEF0FB';
    private const LINEA = 'D5DAEA';
    private const CLASES = [
        'ok'   => ['Apto', 'EAF7F0', '2E9E6B'],
        'warn' => ['Con restricciones', 'FBF3E4', '92611A'],
        'bad'  => ['No apto', 'FBEBE9', 'C4453B'],
    ];

    private XlsxLigero $libro;

    public function __construct(private array $filtros, private ?User $usuario)
    {
        $this->libro = new XlsxLigero();
        $this->libro->propiedades('Consulta SST · Conceptos médicos ocupacionales',
            trim(($usuario->name ?? '') . ' ' . ($usuario->apellido1 ?? '')) ?: 'CABI');
    }

    // ───────────────────────── Hoja de estadísticas ─────────────────────────

    /** @param Collection<int,ConceptoMedico> $vigentes */
    public function estadisticas(Collection $vigentes, int $anulados): void
    {
        $h = $this->libro->hoja('Estadísticas');
        $h->sinCuadricula()->ajustarAncho();
        foreach (['A' => 50, 'B' => 12, 'C' => 11, 'D' => 34] as $col => $ancho) {
            $h->ancho($col, $ancho);
        }

        $fila = $this->encabezado($h, 'Estadísticas de conceptos médicos ocupacionales', 'D');
        $total = $vigentes->count();
        $porClase = $vigentes->countBy(fn (ConceptoMedico $c) => $c->concepto_clase ?: 'sin');
        $hoy = today();
        $conRestricciones = $vigentes->filter(fn ($c) => $c->restricciones_temporalidad === 'indefinido'
            || ($c->restricciones_hasta && $c->restricciones_hasta->gte($hoy)))->count();
        $pacientes = $vigentes->map(fn ($c) => $c->user_id ?: 'id:' . $c->identificacion)->unique()->count();

        $fila = $this->tabla($h, $fila, 'Indicadores generales', ['Indicador', 'Cantidad', '%'], [
            ['Conceptos vigentes', $total, $total ? 1.0 : 0.0],
            ['Aptos', $porClase['ok'] ?? 0, $this->pct($porClase['ok'] ?? 0, $total), 'ok'],
            ['Con restricciones', $porClase['warn'] ?? 0, $this->pct($porClase['warn'] ?? 0, $total), 'warn'],
            ['No aptos', $porClase['bad'] ?? 0, $this->pct($porClase['bad'] ?? 0, $total), 'bad'],
            ['Sin concepto registrado', $porClase['sin'] ?? 0, $this->pct($porClase['sin'] ?? 0, $total)],
            ['Pacientes atendidos (únicos)', $pacientes, null],
            ['Con restricciones vigentes a hoy', $conRestricciones, $this->pct($conRestricciones, $total)],
            ['Conceptos anulados (no incluidos en los totales)', $anulados, null],
        ], false);

        // Concepto emitido (en el orden del catálogo)
        $porConcepto = $vigentes->countBy('concepto_resultado');
        $filas = [];
        foreach (ConceptoMedico::CONCEPTOS_GRUPOS as $ops) {
            foreach ($ops as $clave => $op) {
                if ($n = $porConcepto[$clave] ?? 0) {
                    $filas[] = [$op[0], $n, $this->pct($n, $total), $op[2]];
                }
            }
        }
        if ($n = $vigentes->filter(fn ($c) => !isset(ConceptoMedico::CONCEPTOS[$c->concepto_resultado]))->count()) {
            $filas[] = ['Sin concepto registrado', $n, $this->pct($n, $total)];
        }
        $fila = $this->tabla($h, $fila, 'Por concepto emitido', ['Concepto emitido', 'Cantidad', '%'], $filas);

        // Tipo de evaluación
        $porTipo = $vigentes->countBy('tipo_atencion');
        $filas = [];
        foreach (ConceptoMedico::TIPOS as $clave => $etiqueta) {
            if ($n = $porTipo[$clave] ?? 0) {
                $filas[] = [$etiqueta, $n, $this->pct($n, $total)];
            }
        }
        $fila = $this->tabla($h, $fila, 'Por tipo de evaluación', ['Tipo de evaluación', 'Cantidad', '%'], $filas);

        // Por mes (orden cronológico)
        $porMes = $vigentes->filter(fn ($c) => $c->fecha_atencion)
            ->groupBy(fn ($c) => $c->fecha_atencion->format('Y-m'))->sortKeys()
            ->map(fn ($g, $mes) => [ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d', $mes . '-01')->locale('es')->translatedFormat('F Y')),
                $g->count(), $this->pct($g->count(), $total)])->values()->all();
        $fila = $this->tabla($h, $fila, 'Por mes de atención', ['Mes', 'Cantidad', '%'], $porMes);

        // Énfasis y concepto por énfasis
        $porEnfasis = $vigentes->flatMap(fn ($c) => (array) ($c->enfasis ?? []))->countBy();
        $filas = [];
        foreach (ConceptoMedico::ENFASIS as $clave => $etiqueta) {
            if ($n = $porEnfasis[$clave] ?? 0) {
                $filas[] = [$etiqueta, $n, $this->pct($n, $total)];
            }
        }
        $fila = $this->tabla($h, $fila, 'Por énfasis de la evaluación', ['Énfasis', 'Cantidad', '% del total'], $filas, false);

        $porConceptoEnfasis = $vigentes->flatMap(fn ($c) => array_map(fn ($e) => $e[0] . ': ' . $e[1] . '|' . $e[2], $c->conceptos_enfasis_lista))->countBy();
        $fila = $this->tabla($h, $fila, 'Concepto por énfasis', ['Concepto', 'Cantidad', '% del total'],
            $porConceptoEnfasis->sortDesc()->map(function ($n, $k) use ($total) {
                [$texto, $clase] = explode('|', $k);
                return [$texto, $n, $this->pct($n, $total), $clase];
            })->values()->all(), false);

        // Restricciones por temporalidad
        $porTemp = $vigentes->filter(fn ($c) => $c->restricciones_temporalidad)->countBy('restricciones_temporalidad');
        $filas = [];
        foreach (ConceptoMedico::TEMPORALIDADES as $clave => $etiqueta) {
            if ($n = $porTemp[(string) $clave] ?? 0) {
                $filas[] = [$etiqueta, $n, $this->pct($n, $total)];
            }
        }
        $fila = $this->tabla($h, $fila, 'Restricciones por temporalidad', ['Temporalidad', 'Cantidad', '% del total'], $filas);

        // Servicio, cargo y médico (los más frecuentes)
        foreach (['servicio' => ['Por servicio (15 más frecuentes)', 'Servicio', 15], 'cargo_inicio' => ['Por cargo (15 más frecuentes)', 'Cargo', 15],
                  'medico' => ['Por médico', 'Médico', 30]] as $campo => [$titulo, $col, $max]) {
            $grupos = $vigentes->countBy(fn ($c) => trim((string) $c->{$campo}) ?: 'Sin registrar')->sortDesc()->take($max);
            $fila = $this->tabla($h, $fila, $titulo, [$col, 'Cantidad', '%'],
                $grupos->map(fn ($n, $k) => [$k, $n, $this->pct($n, $total)])->values()->all(), false);
        }
    }

    // ───────────────────────── Hoja del listado ─────────────────────────

    public function listado(Builder $query): void
    {
        $h = $this->libro->hoja('Conceptos');
        $h->horizontal()->ajustarAncho();

        $cols = [
            ['N.º', 9], ['Fecha', 11], ['Hora', 9], ['Paciente', 30], ['Identificación', 16], ['Edad', 8], ['Género', 11],
            ['Cargo', 24], ['Servicio', 22], ['EPS', 18], ['AFP', 16], ['ARL', 16], ['Tipo de evaluación', 15], ['Énfasis', 24],
            ['Concepto emitido', 30], ['Resultado', 16], ['Concepto por énfasis', 32], ['Recomendaciones médicas', 45],
            ['Restricciones médicas', 40], ['Temporalidad', 14], ['Restricciones vigentes hasta', 14], ['Recomendaciones SST', 45],
            ['Observaciones del concepto', 35], ['Médico', 24], ['Registro médico', 16], ['Estado', 10],
            ['Motivo de anulación', 30], ['Correcciones', 12],
        ];
        $ultima = XlsxLigero::letraColumna(count($cols));
        $cab = $this->encabezado($h, 'Listado de conceptos médicos ocupacionales', $ultima);

        foreach ($cols as $i => [$titulo, $ancho]) {
            $col = XlsxLigero::letraColumna($i + 1);
            $h->valor($col . $cab, $titulo, $this->estiloCabecera());
            $h->ancho($col, $ancho);
        }
        $h->alto($cab, 30);

        // Estilos por tipo de columna: texto, centrado, fecha, número de certificado
        $base = ['border' => 'all', 'borderColor' => self::LINEA, 'v' => 'top', 'wrap' => true];
        $centradas = ['A' => ['fmt' => '000000'], 'B' => ['fmt' => 'dd/mm/yyyy'], 'C' => [], 'F' => [], 'U' => ['fmt' => 'dd/mm/yyyy'], 'Z' => [], 'AB' => []];
        $generos = ['F' => 'Femenino', 'M' => 'Masculino', 'O' => 'Otro'];

        $fila = $cab + 1;
        $query->chunk(500, function ($conceptos) use ($h, &$fila, $generos, $base, $centradas) {
            foreach ($conceptos as $c) {
                $paciente = $c->paciente_nombre ?: ($c->user ? trim($c->user->name . ' ' . ($c->user->apellido1 ?? '') . ' ' . ($c->user->apellido2 ?? '')) : '');
                $valores = [
                    (int) $c->id, $c->fecha_atencion, $c->hora_atencion ? substr((string) $c->hora_atencion, 0, 5) : null,
                    $paciente, (string) $c->identificacion, $c->edad !== null ? (int) $c->edad : null, $generos[$c->genero ?? ''] ?? $c->genero,
                    $c->cargo_inicio, $c->servicio, $c->eps, $c->afp, $c->arl, $c->tipo_label, $c->enfasis_label,
                    $c->concepto_label, self::CLASES[$c->concepto_clase][0] ?? '',
                    implode(' · ', array_map(fn ($e) => $e[0] . ': ' . $e[1], $c->conceptos_enfasis_lista)),
                    $c->recomendaciones, $c->restricciones, $c->temporalidad_label, $c->restricciones_hasta,
                    $c->sst, $c->observaciones_concepto, $c->medico, $c->registro,
                    $c->anulado ? 'Anulado' : 'Vigente', $c->motivo_anulacion, (int) ($c->correcciones_count ?? 0),
                ];
                $color = self::CLASES[$c->concepto_clase] ?? null;
                foreach ($valores as $i => $v) {
                    $col = XlsxLigero::letraColumna($i + 1);
                    $e = $base;
                    if (isset($centradas[$col])) {
                        $e = $centradas[$col] + ['h' => 'center'] + $e;
                    }
                    if ($color && ($col === 'O' || $col === 'P')) {
                        $e += ['fill' => $color[1], 'color' => $color[2], 'bold' => $col === 'P'];
                    }
                    if ($c->anulado) {
                        $e = ['color' => '9AA0B5'] + $e;
                        if ($col === 'Z') {
                            $e = ['color' => 'C4453B', 'bold' => true] + $e;
                        }
                    }
                    $h->valor($col . $fila, $v, $e);
                }
                $fila++;
            }
        });

        $fin = $fila - 1;
        if ($fin === $cab) {
            $h->valor('A' . ($cab + 1), 'No hay conceptos con los filtros aplicados.', ['italic' => true, 'color' => '9AA0B5']);
        }
        $h->autofiltro("A{$cab}:{$ultima}" . max($fin, $cab));
        $h->congelar('E' . ($cab + 1));   // encabezado y paciente siempre visibles
        $h->repetirFilas($cab, $cab);
    }

    public function contenido(): string
    {
        return $this->libro->contenido();
    }

    // ───────────────────────── Ayudas ─────────────────────────

    /** Título institucional, fecha de generación y filtros. Devuelve la siguiente fila libre. */
    private function encabezado(XlsxLigeroHoja $h, string $titulo, string $ultimaCol): int
    {
        $banda = ['fill' => self::MARCA, 'v' => 'center', 'indent' => 1];
        $h->estilo("A1:{$ultimaCol}2", $banda);
        $h->valor('A1', 'HOSPITAL UNIVERSITARIO DEL VALLE "EVARISTO GARCÍA" E.S.E · Salud Ocupacional', $banda + ['bold' => true, 'size' => 9, 'color' => 'C9CFEA']);
        $h->valor('A2', 'Consulta SST · ' . $titulo, $banda + ['bold' => true, 'size' => 15, 'color' => 'FFFFFF']);
        $quien = trim(($this->usuario->name ?? '') . ' ' . ($this->usuario->apellido1 ?? ''));
        $h->valor('A3', 'Generado el ' . now()->format('d/m/Y h:i A') . ($quien ? ' por ' . $quien : ''), ['italic' => true, 'color' => '7C82A0']);
        $h->combinar("A1:{$ultimaCol}1")->combinar("A2:{$ultimaCol}2")->combinar("A3:{$ultimaCol}3");
        $h->alto(1, 18)->alto(2, 28);

        $fila = 4;
        foreach ($this->filtros as $etiqueta => $valor) {
            $h->valor("A{$fila}", $etiqueta . ': ' . $valor, ['color' => '4A4F66']);
            $h->combinar("A{$fila}:{$ultimaCol}{$fila}");
            $fila++;
        }
        return $fila + 1;
    }

    /**
     * Tabla de una sección (título + cabecera + filas [etiqueta, cantidad, %, clase?]) con una
     * barra de datos en la columna D. Devuelve la siguiente fila libre.
     */
    private function tabla(XlsxLigeroHoja $h, int $fila, string $titulo, array $cabecera, array $filas, bool $conTotal = true): int
    {
        $h->estilo("A{$fila}:D{$fila}", ['border' => 'bottom', 'borderStyle' => 'medium', 'borderColor' => self::MARCA]);
        $h->valor("A{$fila}", mb_strtoupper($titulo), ['bold' => true, 'color' => self::MARCA, 'border' => 'bottom', 'borderStyle' => 'medium', 'borderColor' => self::MARCA]);
        $fila++;

        foreach (array_merge($cabecera, ['Distribución']) as $i => $t) {
            $h->valor(XlsxLigero::letraColumna($i + 1) . $fila, $t, $this->estiloCabecera());
        }
        $fila++;

        if (!$filas) {
            $h->valor("A{$fila}", 'Sin datos en el periodo filtrado.', ['italic' => true, 'color' => '9AA0B5']);
            return $fila + 2;
        }

        $desde = $fila;
        $suma = 0;
        foreach ($filas as $i => $f) {
            [$etiqueta, $n, $pct] = $f;
            $clase = self::CLASES[$f[3] ?? ''] ?? null;
            $fondo = $i % 2 ? ['fill' => 'F7F8FC'] : [];
            $borde = ['border' => 'bottom', 'borderColor' => self::LINEA];
            $h->valor("A{$fila}", (string) $etiqueta, $borde + $fondo + ['wrap' => true, 'v' => 'center']
                + ($clase ? ['color' => $clase[2], 'bold' => true] : []));
            $h->valor("B{$fila}", (int) $n, $borde + $fondo + ['h' => 'center', 'v' => 'center', 'bold' => true]);
            $h->valor("C{$fila}", $pct, $borde + $fondo + ['h' => 'center', 'v' => 'center', 'fmt' => '0.0%']);
            // Barra de datos proporcional al % (el número se oculta con el formato ;;;)
            $h->valor("D{$fila}", $pct, $borde + $fondo + ['fmt' => ';;;']);
            $suma += (int) $n;
            $fila++;
        }
        $hasta = $fila - 1;
        $h->barrasDatos("D{$desde}:D{$hasta}", self::MARCA);

        if ($conTotal) {
            $t = ['bold' => true, 'fill' => self::SUAVE];
            $h->valor("A{$fila}", 'Total', $t);
            $h->valor("B{$fila}", $suma, $t + ['h' => 'center']);
            $h->estilo("C{$fila}:D{$fila}", $t);
            $fila++;
        }

        return $fila + 1;
    }

    private function estiloCabecera(): array
    {
        return ['bold' => true, 'color' => 'FFFFFF', 'fill' => self::MARCA, 'h' => 'center', 'v' => 'center', 'wrap' => true];
    }

    private function pct(int $n, int $total): float
    {
        return $total ? $n / $total : 0.0;
    }
}

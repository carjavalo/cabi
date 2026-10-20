<?php

namespace App\Support;

use App\Models\ConceptoMedico;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Libro de Excel de Consulta SST:
 * - Hoja "Estadísticas": indicadores y distribuciones (concepto, tipo, mes, énfasis,
 *   restricciones, servicio, cargo y médico) con gráficos.
 * - Hoja "Conceptos": el listado filtrado con los datos del certificado (sin la historia clínica).
 *
 * Todos los textos se escriben como texto explícito: un valor que empiece por "=" nunca se
 * interpreta como fórmula.
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

    private Spreadsheet $libro;
    private int $graficos = 0;
    /** Primera fila libre para el siguiente gráfico (los gráficos se apilan sin encimarse). */
    private int $filaGraficoLibre = 1;

    public function __construct(private array $filtros, private ?User $usuario)
    {
        $this->libro = new Spreadsheet();
        $this->libro->removeSheetByIndex(0);
        $this->libro->getProperties()
            ->setCreator(trim(($usuario->name ?? '') . ' ' . ($usuario->apellido1 ?? '')) ?: 'CABI')
            ->setTitle('Consulta SST · Conceptos médicos ocupacionales')
            ->setCompany('Hospital Universitario del Valle "Evaristo García" E.S.E');
        $this->libro->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
    }

    // ───────────────────────── Hoja de estadísticas ─────────────────────────

    /** @param Collection<int,ConceptoMedico> $vigentes */
    public function estadisticas(Collection $vigentes, int $anulados): void
    {
        $h = $this->libro->createSheet();
        $h->setTitle('Estadísticas');
        $h->getSheetView()->setZoomScale(100);
        $h->setShowGridlines(false);
        foreach (['A' => 46, 'B' => 13, 'C' => 11, 'D' => 3] as $col => $ancho) {
            $h->getColumnDimension($col)->setWidth($ancho);
        }

        $fila = $this->encabezado($h, 'Estadísticas de conceptos médicos ocupacionales', 'M');
        $total = $vigentes->count();
        $clase = fn (ConceptoMedico $c) => $c->concepto_clase ?: 'sin';
        $porClase = $vigentes->countBy($clase);
        $hoy = today();
        $conRestricciones = $vigentes->filter(fn ($c) => $c->restricciones_temporalidad === 'indefinido'
            || ($c->restricciones_hasta && $c->restricciones_hasta->gte($hoy)));

        // Indicadores generales (filas 2 a 4 de la tabla: Aptos, Con restricciones, No aptos → gráfico de torta)
        $inicio = $fila;
        $fila = $this->tabla($h, $fila, 'Indicadores generales', ['Indicador', 'Cantidad', '%'], [
            ['Conceptos vigentes', $total, $total ? 1 : 0],
            ['Aptos', $porClase['ok'] ?? 0, $this->pct($porClase['ok'] ?? 0, $total)],
            ['Con restricciones', $porClase['warn'] ?? 0, $this->pct($porClase['warn'] ?? 0, $total)],
            ['No aptos', $porClase['bad'] ?? 0, $this->pct($porClase['bad'] ?? 0, $total)],
            ['Sin concepto registrado', $porClase['sin'] ?? 0, $this->pct($porClase['sin'] ?? 0, $total)],
            ['Pacientes atendidos (únicos)', $vigentes->map(fn ($c) => $c->user_id ?: 'id:' . $c->identificacion)->unique()->count(), null],
            ['Con restricciones vigentes a hoy', $conRestricciones->count(), $this->pct($conRestricciones->count(), $total)],
            ['Conceptos anulados (no incluidos)', $anulados, null],
        ], false);
        if ($total) {
            $this->graficoTorta($h, 'Resultado', 'Resultado del concepto', $inicio + 3, 3, $inicio);
        }

        // Concepto emitido
        $porConcepto = $vigentes->countBy('concepto_resultado');
        $filas = [];
        foreach (ConceptoMedico::CONCEPTOS as $clave => $etiqueta) {
            if ($n = $porConcepto[$clave] ?? 0) {
                $filas[] = [$etiqueta, $n, $this->pct($n, $total)];
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
        $inicio = $fila;
        $fila = $this->tabla($h, $fila, 'Por tipo de evaluación', ['Tipo de evaluación', 'Cantidad', '%'], $filas);
        $this->graficoBarras($h, 'Tipo', 'Conceptos por tipo de evaluación', $inicio + 2, count($filas), $inicio);

        // Por mes (orden cronológico)
        $porMes = $vigentes->filter(fn ($c) => $c->fecha_atencion)
            ->groupBy(fn ($c) => $c->fecha_atencion->format('Y-m'))->sortKeys()
            ->map(fn ($g, $mes) => [ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d', $mes . '-01')->locale('es')->translatedFormat('F Y')),
                $g->count(), $this->pct($g->count(), $total)])->values()->all();
        $inicio = $fila;
        $fila = $this->tabla($h, $fila, 'Por mes de atención', ['Mes', 'Cantidad', '%'], $porMes);
        $this->graficoBarras($h, 'Mes', 'Conceptos por mes', $inicio + 2, count($porMes), $inicio);

        // Énfasis y concepto por énfasis
        $porEnfasis = $vigentes->flatMap(fn ($c) => (array) ($c->enfasis ?? []))->countBy();
        $filas = [];
        foreach (ConceptoMedico::ENFASIS as $clave => $etiqueta) {
            if ($n = $porEnfasis[$clave] ?? 0) {
                $filas[] = [$etiqueta, $n, $this->pct($n, $total)];
            }
        }
        $fila = $this->tabla($h, $fila, 'Por énfasis de la evaluación', ['Énfasis', 'Cantidad', '% del total'], $filas);

        $porConceptoEnfasis = $vigentes->flatMap(fn ($c) => array_map(fn ($e) => $e[0] . ': ' . $e[1], $c->conceptos_enfasis_lista))->countBy();
        $fila = $this->tabla($h, $fila, 'Concepto por énfasis', ['Concepto', 'Cantidad', '% del total'],
            $porConceptoEnfasis->sortDesc()->map(fn ($n, $k) => [$k, $n, $this->pct($n, $total)])->values()->all());

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
                $grupos->map(fn ($n, $k) => [$k, $n, $this->pct($n, $total)])->values()->all());
        }

        $h->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $h->setSelectedCell('A1');
    }

    // ───────────────────────── Hoja del listado ─────────────────────────

    public function listado(Builder $query): void
    {
        $h = $this->libro->createSheet();
        $h->setTitle('Conceptos');

        $cols = [
            ['N.º', 9], ['Fecha', 11], ['Hora', 7], ['Paciente', 30], ['Identificación', 15], ['Edad', 6], ['Género', 9],
            ['Cargo', 24], ['Servicio', 22], ['EPS', 18], ['AFP', 16], ['ARL', 16], ['Tipo de evaluación', 15], ['Énfasis', 24],
            ['Concepto emitido', 30], ['Resultado', 16], ['Concepto por énfasis', 32], ['Recomendaciones médicas', 45],
            ['Restricciones médicas', 40], ['Temporalidad', 14], ['Restricciones vigentes hasta', 14], ['Recomendaciones SST', 45],
            ['Observaciones del concepto', 35], ['Médico', 24], ['Registro médico', 16], ['Estado', 10],
            ['Motivo de anulación', 30], ['Correcciones', 11],
        ];
        $ultima = Coordinate::stringFromColumnIndex(count($cols));
        $fila = $this->encabezado($h, 'Listado de conceptos médicos ocupacionales', $ultima);

        $cab = $fila;
        foreach ($cols as $i => [$titulo, $ancho]) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $this->texto($h, $col . $cab, $titulo);
            $h->getColumnDimension($col)->setWidth($ancho);
        }
        $this->estiloCabecera($h, "A{$cab}:{$ultima}{$cab}");
        $h->getRowDimension($cab)->setRowHeight(30);

        $fila = $cab + 1;
        $generos = ['F' => 'Femenino', 'M' => 'Masculino', 'O' => 'Otro'];
        $query->chunk(500, function ($conceptos) use ($h, &$fila, $generos, $ultima) {
            foreach ($conceptos as $c) {
                $paciente = $c->paciente_nombre ?: ($c->user ? trim($c->user->name . ' ' . ($c->user->apellido1 ?? '') . ' ' . ($c->user->apellido2 ?? '')) : '');
                $valores = [
                    (int) $c->id,
                    $c->fecha_atencion ? ExcelDate::PHPToExcel($c->fecha_atencion) : null,
                    $c->hora_atencion ? substr((string) $c->hora_atencion, 0, 5) : null,
                    $paciente, (string) $c->identificacion, $c->edad !== null ? (int) $c->edad : null, $generos[$c->genero ?? ''] ?? $c->genero,
                    $c->cargo_inicio, $c->servicio, $c->eps, $c->afp, $c->arl, $c->tipo_label, $c->enfasis_label,
                    $c->concepto_label, self::CLASES[$c->concepto_clase][0] ?? '',
                    implode(' · ', array_map(fn ($e) => $e[0] . ': ' . $e[1], $c->conceptos_enfasis_lista)),
                    $c->recomendaciones, $c->restricciones, $c->temporalidad_label,
                    $c->restricciones_hasta ? ExcelDate::PHPToExcel($c->restricciones_hasta) : null,
                    $c->sst, $c->observaciones_concepto, $c->medico, $c->registro,
                    $c->anulado ? 'Anulado' : 'Vigente', $c->motivo_anulacion, (int) ($c->correcciones_count ?? 0),
                ];
                foreach ($valores as $i => $v) {
                    $celda = Coordinate::stringFromColumnIndex($i + 1) . $fila;
                    if (is_int($v) || is_float($v)) {
                        $h->setCellValue($celda, $v);
                    } elseif ($v !== null && $v !== '') {
                        $this->texto($h, $celda, mb_substr((string) $v, 0, 32000));
                    }
                }
                // Color del resultado y filas anuladas en gris
                if ($color = self::CLASES[$c->concepto_clase] ?? null) {
                    $h->getStyle("O{$fila}:P{$fila}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color[1]);
                    $h->getStyle("O{$fila}:P{$fila}")->getFont()->getColor()->setRGB($color[2]);
                }
                if ($c->anulado) {
                    $h->getStyle("A{$fila}:{$ultima}{$fila}")->getFont()->getColor()->setRGB('9AA0B5');
                    $h->getStyle("Z{$fila}")->getFont()->setBold(true)->getColor()->setRGB('C4453B');
                }
                $fila++;
            }
        });

        $fin = max($fila - 1, $cab);
        if ($fin > $cab) {
            $rango = "A" . ($cab + 1) . ":{$ultima}{$fin}";
            $h->getStyle($rango)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            $h->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::LINEA);
            $h->getStyle("B" . ($cab + 1) . ":B{$fin}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $h->getStyle("U" . ($cab + 1) . ":U{$fin}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $h->getStyle("A" . ($cab + 1) . ":A{$fin}")->getNumberFormat()->setFormatCode('000000');
            foreach (['A', 'B', 'C', 'F', 'Z', 'AB'] as $col) {
                $h->getStyle("{$col}" . ($cab + 1) . ":{$col}{$fin}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        } else {
            $this->texto($h, 'A' . ($cab + 1), 'No hay conceptos con los filtros aplicados.');
        }

        $h->setAutoFilter("A{$cab}:{$ultima}{$fin}");
        $h->freezePane('E' . ($cab + 1));   // encabezado y paciente siempre visibles
        $h->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd($cab, $cab);
        $h->setSelectedCell('A' . ($cab + 1));
    }

    public function guardar(string $destino): void
    {
        $this->libro->setActiveSheetIndex(0);
        $writer = new Xlsx($this->libro);
        $writer->setIncludeCharts(true);
        $writer->save($destino);
    }

    // ───────────────────────── Ayudas ─────────────────────────

    /** Título institucional, fecha de generación y filtros. Devuelve la siguiente fila libre. */
    private function encabezado(Worksheet $h, string $titulo, string $ultimaCol): int
    {
        $this->texto($h, 'A1', 'HOSPITAL UNIVERSITARIO DEL VALLE "EVARISTO GARCÍA" E.S.E · Salud Ocupacional');
        $this->texto($h, 'A2', 'Consulta SST · ' . $titulo);
        $quien = trim(($this->usuario->name ?? '') . ' ' . ($this->usuario->apellido1 ?? ''));
        $this->texto($h, 'A3', 'Generado el ' . now()->format('d/m/Y h:i A') . ($quien ? ' por ' . $quien : ''));
        $h->mergeCells("A1:{$ultimaCol}1");
        $h->mergeCells("A2:{$ultimaCol}2");
        $h->mergeCells("A3:{$ultimaCol}3");
        $h->getStyle("A1:{$ultimaCol}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::MARCA);
        $h->getStyle('A1')->getFont()->setSize(9)->setBold(true)->getColor()->setRGB('C9CFEA');
        $h->getStyle('A2')->getFont()->setSize(15)->setBold(true)->getColor()->setRGB('FFFFFF');
        $h->getStyle('A3')->getFont()->setItalic(true)->getColor()->setRGB('7C82A0');
        $h->getRowDimension(1)->setRowHeight(18);
        $h->getRowDimension(2)->setRowHeight(26);
        $h->getStyle("A1:A2")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);

        $fila = 4;
        foreach ($this->filtros as $etiqueta => $valor) {
            $this->texto($h, "A{$fila}", $etiqueta . ': ' . $valor);
            $h->getStyle("A{$fila}")->getFont()->getColor()->setRGB('4A4F66');
            $fila++;
        }
        return $fila + 1;
    }

    /** Tabla de una sección (título + cabecera + filas). Devuelve la siguiente fila libre. */
    private function tabla(Worksheet $h, int $fila, string $titulo, array $cabecera, array $filas, bool $conTotal = true): int
    {
        $this->texto($h, "A{$fila}", mb_strtoupper($titulo));
        $h->mergeCells("A{$fila}:C{$fila}");
        $h->getStyle("A{$fila}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB(self::MARCA);
        $h->getStyle("A{$fila}:C{$fila}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::MARCA);
        $fila++;

        foreach ($cabecera as $i => $t) {
            $this->texto($h, Coordinate::stringFromColumnIndex($i + 1) . $fila, $t);
        }
        $this->estiloCabecera($h, "A{$fila}:C{$fila}");
        $fila++;

        if (!$filas) {
            $this->texto($h, "A{$fila}", 'Sin datos en el periodo filtrado.');
            $h->getStyle("A{$fila}")->getFont()->setItalic(true)->getColor()->setRGB('9AA0B5');
            return $fila + 2;
        }

        $desde = $fila;
        foreach ($filas as $i => [$etiqueta, $n, $pct]) {
            $this->texto($h, "A{$fila}", (string) $etiqueta);
            $h->setCellValue("B{$fila}", (int) $n);
            if ($pct !== null) {
                $h->setCellValue("C{$fila}", $pct);
            }
            if ($i % 2) {
                $h->getStyle("A{$fila}:C{$fila}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F8FC');
            }
            $fila++;
        }
        $hasta = $fila - 1;
        if ($conTotal) {
            $this->texto($h, "A{$fila}", 'Total');
            $h->setCellValue("B{$fila}", "=SUM(B{$desde}:B{$hasta})");
            $h->getStyle("A{$fila}:C{$fila}")->getFont()->setBold(true);
            $h->getStyle("A{$fila}:C{$fila}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::SUAVE);
            $fila++;
        }
        $h->getStyle("C{$desde}:C" . ($fila - 1))->getNumberFormat()->setFormatCode('0.0%');
        $h->getStyle("B{$desde}:C" . ($fila - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $h->getStyle("A{$desde}:C" . ($fila - 1))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::LINEA);
        $h->getStyle("A{$desde}:A" . ($fila - 1))->getAlignment()->setWrapText(true);

        return $fila + 1;
    }

    private function estiloCabecera(Worksheet $h, string $rango): void
    {
        $e = $h->getStyle($rango);
        $e->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::MARCA);
        $e->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $e->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
    }

    /** Torta con las filas Aptos / Con restricciones / No aptos de los indicadores. */
    private function graficoTorta(Worksheet $h, string $nombre, string $titulo, int $filaInicio, int $filas, int $filaGrafico): void
    {
        $hoja = "'" . $h->getTitle() . "'";
        $rango = fn (string $c) => "{$hoja}!\${$c}\${$filaInicio}:\${$c}\$" . ($filaInicio + $filas - 1);
        $serie = new DataSeries(
            DataSeries::TYPE_PIECHART, null, [0],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $rango('A'), null, $filas)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $rango('B'), null, $filas, [], null, ['2E9E6B', 'C98A1E', 'C4453B'])]
        );
        $layout = new Layout();
        $layout->setShowPercent(true)->setShowVal(false);
        $this->ponerGrafico($h, $nombre, $titulo, new PlotArea($layout, [$serie]), $filaGrafico, 16, true);
    }

    /** Columnas con la cantidad de una sección (etiquetas en A, cantidades en B). */
    private function graficoBarras(Worksheet $h, string $nombre, string $titulo, int $filaInicio, int $filas, int $filaGrafico): void
    {
        if ($filas < 1) {
            return;
        }
        $hoja = "'" . $h->getTitle() . "'";
        $rango = fn (string $c) => "{$hoja}!\${$c}\${$filaInicio}:\${$c}\$" . ($filaInicio + $filas - 1);
        $serie = new DataSeries(
            DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, [0],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$B\$" . ($filaInicio - 1), null, 1)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $rango('A'), null, $filas)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $rango('B'), null, $filas, [], null, self::MARCA)]
        );
        $serie->setPlotDirection(DataSeries::DIRECTION_COL);
        $layout = new Layout();
        $layout->setShowVal(true);
        $this->ponerGrafico($h, $nombre, $titulo, new PlotArea($layout, [$serie]), $filaGrafico, 16, false);
    }

    /** Ubica el gráfico en las columnas F:M, desde la fila de su sección o debajo del anterior (sin encimarse). */
    private function ponerGrafico(Worksheet $h, string $nombre, string $titulo, PlotArea $area, int $fila, int $alto, bool $leyenda): void
    {
        $fila = max($fila, $this->filaGraficoLibre);
        $grafico = new Chart('grafico' . (++$this->graficos) . $nombre, new Title($titulo),
            $leyenda ? new Legend(Legend::POSITION_RIGHT, null, false) : null, $area);
        $grafico->setTopLeftPosition('F' . $fila);
        $grafico->setBottomRightPosition('M' . ($fila + $alto));
        $h->addChart($grafico);
        $this->filaGraficoLibre = $fila + $alto + 2;
    }

    private function texto(Worksheet $h, string $celda, string $valor): void
    {
        $h->setCellValueExplicit($celda, $valor, DataType::TYPE_STRING);
    }

    private function pct(int $n, int $total): float
    {
        return $total ? $n / $total : 0.0;
    }
}

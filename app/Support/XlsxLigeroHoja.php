<?php

namespace App\Support;

/** Una hoja del libro. */
class XlsxLigeroHoja
{
    public array $celdas = [];       // [fila][columna] => [tipo, valor, estilo]
    public array $anchos = [];
    public array $altos = [];
    public array $combinadas = [];
    public array $barras = [];
    public ?string $congelar = null;
    public ?string $autofiltro = null;
    public ?array $repetirFilas = null;
    public bool $cuadricula = true;
    public bool $horizontal = false;
    public bool $ajustarAncho = false;

    public function __construct(private XlsxLigero $libro, public string $nombre)
    {
    }

    /** Escribe un valor: string → texto (nunca fórmula), int/float → número, DateTimeInterface → fecha. */
    public function valor(string $celda, $valor, array $estilo = []): self
    {
        if ($valor === null || $valor === '') {
            if ($estilo) {
                $this->poner($celda, 'v', null, $estilo);
            }
            return $this;
        }
        if ($valor instanceof \DateTimeInterface) {
            $estilo += ['fmt' => 'dd/mm/yyyy'];
            return $this->poner($celda, 'n', XlsxLigero::fechaExcel($valor), $estilo);
        }
        if (is_int($valor) || is_float($valor)) {
            return $this->poner($celda, 'n', $valor, $estilo);
        }
        return $this->poner($celda, 's', mb_substr((string) $valor, 0, 32000), $estilo);
    }

    /** Aplica un estilo a todas las celdas de un rango (crea las vacías). */
    public function estilo(string $rango, array $estilo): self
    {
        [$c1, $f1, $c2, $f2] = $this->limites($rango);
        $idx = $this->libro->estilo($estilo);
        for ($f = $f1; $f <= $f2; $f++) {
            for ($c = $c1; $c <= $c2; $c++) {
                if (isset($this->celdas[$f][$c])) {
                    $this->celdas[$f][$c][2] = $idx;
                } else {
                    $this->celdas[$f][$c] = ['v', null, $idx];
                }
            }
        }
        return $this;
    }

    public function ancho(string $col, float $ancho): self { $this->anchos[XlsxLigero::numeroColumna($col)] = $ancho; return $this; }
    public function alto(int $fila, float $alto): self { $this->altos[$fila] = $alto; return $this; }
    public function combinar(string $rango): self { $this->combinadas[] = $rango; return $this; }
    public function congelar(string $celda): self { $this->congelar = $celda; return $this; }
    public function autofiltro(string $rango): self { $this->autofiltro = $rango; return $this; }
    public function repetirFilas(int $desde, int $hasta): self { $this->repetirFilas = [$desde, $hasta]; return $this; }
    public function sinCuadricula(): self { $this->cuadricula = false; return $this; }
    public function horizontal(): self { $this->horizontal = true; return $this; }
    public function ajustarAncho(): self { $this->ajustarAncho = true; return $this; }

    /** Barras de datos de Excel (formato condicional) sobre un rango numérico. */
    public function barrasDatos(string $rango, string $color, bool $desdeCero = true): self
    {
        $this->barras[] = [$rango, $color, $desdeCero];
        return $this;
    }

    public function xml(bool $seleccionada): string
    {
        ksort($this->celdas);
        $maxCol = 1;
        $filas = '';
        $todas = array_unique(array_merge(array_keys($this->celdas), array_keys($this->altos)));
        sort($todas);
        foreach ($todas as $f) {
            $cols = $this->celdas[$f] ?? [];
            ksort($cols);
            $xml = '';
            foreach ($cols as $c => [$tipo, $v, $s]) {
                $maxCol = max($maxCol, $c);
                $ref = XlsxLigero::letraColumna($c) . $f;
                $st = $s ? ' s="' . $s . '"' : '';
                if ($tipo === 's') {
                    $xml .= '<c r="' . $ref . '"' . $st . ' t="inlineStr"><is><t xml:space="preserve">' . XlsxLigero::esc($v) . '</t></is></c>';
                } elseif ($tipo === 'n') {
                    $xml .= '<c r="' . $ref . '"' . $st . '><v>' . (is_float($v) ? rtrim(rtrim(sprintf('%.10F', $v), '0'), '.') : $v) . '</v></c>';
                } else {
                    $xml .= '<c r="' . $ref . '"' . $st . '/>';
                }
            }
            $alto = isset($this->altos[$f]) ? ' ht="' . $this->altos[$f] . '" customHeight="1"' : '';
            $filas .= '<row r="' . $f . '"' . $alto . '>' . $xml . '</row>';
        }
        $ultimaFila = $todas ? max($todas) : 1;
        $dim = 'A1:' . XlsxLigero::letraColumna(max($maxCol, $this->anchos ? max(array_keys($this->anchos)) : 1)) . $ultimaFila;

        $vista = '<sheetView workbookViewId="0"' . ($seleccionada ? ' tabSelected="1"' : '') . (!$this->cuadricula ? ' showGridLines="0"' : '') . '>';
        if ($this->congelar) {
            preg_match('/([A-Z]+)(\d+)/', $this->congelar, $m);
            $x = XlsxLigero::numeroColumna($m[1]) - 1;
            $y = (int) $m[2] - 1;
            $panel = $x && $y ? 'bottomRight' : ($y ? 'bottomLeft' : 'topRight');
            $vista .= '<pane' . ($x ? ' xSplit="' . $x . '"' : '') . ($y ? ' ySplit="' . $y . '"' : '')
                . ' topLeftCell="' . $this->congelar . '" activePane="' . $panel . '" state="frozen"/>'
                . '<selection pane="' . $panel . '" activeCell="' . $this->congelar . '" sqref="' . $this->congelar . '"/>';
        }
        $vista .= '</sheetView>';

        $cols = '';
        if ($this->anchos) {
            ksort($this->anchos);
            foreach ($this->anchos as $c => $a) {
                $cols .= '<col min="' . $c . '" max="' . $c . '" width="' . $a . '" customWidth="1"/>';
            }
            $cols = '<cols>' . $cols . '</cols>';
        }

        $cf = '';
        foreach ($this->barras as $i => [$rango, $color, $desdeCero]) {
            $cf .= '<conditionalFormatting sqref="' . $rango . '"><cfRule type="dataBar" priority="' . ($i + 1) . '"><dataBar>'
                . ($desdeCero ? '<cfvo type="num" val="0"/>' : '<cfvo type="min"/>') . '<cfvo type="max"/>'
                . '<color rgb="FF' . $color . '"/></dataBar></cfRule></conditionalFormatting>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . ($this->ajustarAncho ? '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>' : '')
            . '<dimension ref="' . $dim . '"/>'
            . '<sheetViews>' . $vista . '</sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . $cols
            . '<sheetData>' . $filas . '</sheetData>'
            . ($this->autofiltro ? '<autoFilter ref="' . $this->autofiltro . '"/>' : '')
            . ($this->combinadas ? '<mergeCells count="' . count($this->combinadas) . '">'
                . implode('', array_map(fn ($r) => '<mergeCell ref="' . $r . '"/>', $this->combinadas)) . '</mergeCells>' : '')
            . $cf
            . '<pageMargins left="0.5" right="0.5" top="0.6" bottom="0.6" header="0.3" footer="0.3"/>'
            . '<pageSetup paperSize="1"' . ($this->horizontal ? ' orientation="landscape"' : ' orientation="portrait"')
            . ($this->ajustarAncho ? ' fitToWidth="1" fitToHeight="0"' : '') . '/>'
            . '</worksheet>';
    }

    private function poner(string $celda, string $tipo, $valor, array $estilo): self
    {
        preg_match('/^([A-Z]+)(\d+)$/', $celda, $m);
        $this->celdas[(int) $m[2]][XlsxLigero::numeroColumna($m[1])] = [$tipo, $valor, $estilo ? $this->libro->estilo($estilo) : 0];
        return $this;
    }

    private function limites(string $rango): array
    {
        [$a, $b] = array_pad(explode(':', $rango), 2, null);
        $b ??= $a;
        preg_match('/^([A-Z]+)(\d+)$/', $a, $m1);
        preg_match('/^([A-Z]+)(\d+)$/', $b, $m2);
        return [XlsxLigero::numeroColumna($m1[1]), (int) $m1[2], XlsxLigero::numeroColumna($m2[1]), (int) $m2[2]];
    }
}

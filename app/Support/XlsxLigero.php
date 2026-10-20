<?php

namespace App\Support;

/**
 * Generador mínimo de archivos Excel (.xlsx) en PHP puro.
 *
 * No depende de librerías de Composer ni de las extensiones zip, gd o xmlwriter, para que
 * funcione en cualquier hosting (cPanel sin terminal). Soporta varias hojas, textos, números,
 * fechas, estilos (fuente, relleno, bordes, alineación, formato numérico), anchos de columna,
 * alto de filas, celdas combinadas, paneles inmovilizados, autofiltro, barras de datos
 * (formato condicional) y configuración de impresión.
 *
 * Uso:
 *   $x = new XlsxLigero();
 *   $h = $x->hoja('Datos');
 *   $h->valor('A1', 'Hola', ['bold' => true]);
 *   file_put_contents('a.xlsx', $x->contenido());
 */
class XlsxLigero
{
    /** @var XlsxLigeroHoja[] */
    private array $hojas = [];
    private array $estilos = [];
    private array $fuentes = [];
    private array $rellenos = [];
    private array $bordes = [];
    private array $formatos = [];
    private array $propiedades = ['titulo' => '', 'autor' => ''];

    public function __construct()
    {
        $this->fuentes[] = $this->xmlFuente([]);
        $this->rellenos[] = '<fill><patternFill patternType="none"/></fill>';
        $this->rellenos[] = '<fill><patternFill patternType="gray125"/></fill>';
        $this->bordes[] = '<border><left/><right/><top/><bottom/><diagonal/></border>';
        $this->estilos['[]'] = [0, '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'];
    }

    public function propiedades(string $titulo, string $autor): void
    {
        $this->propiedades = ['titulo' => $titulo, 'autor' => $autor];
    }

    public function hoja(string $nombre): XlsxLigeroHoja
    {
        // Nombre válido para Excel: máximo 31 caracteres, sin \ / ? * [ ] :
        $nombre = mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/u', ' ', $nombre), 0, 31);
        return $this->hojas[] = new XlsxLigeroHoja($this, $nombre);
    }

    /**
     * Índice de estilo de celda (se reutiliza si ya existe uno igual).
     * Claves: bold, italic, size, color, fill, border (all|bottom), borderStyle (thin|medium),
     *         borderColor, h (left|center|right), v (top|center|bottom), wrap, indent, fmt.
     */
    public function estilo(array $e): int
    {
        ksort($e);
        $clave = json_encode($e);
        if (isset($this->estilos[$clave])) {
            return $this->estilos[$clave][0];
        }

        $fuente = $this->indice($this->fuentes, $this->xmlFuente($e));
        $relleno = 0;
        if (!empty($e['fill'])) {
            $relleno = $this->indice($this->rellenos, '<fill><patternFill patternType="solid"><fgColor rgb="FF' . $e['fill'] . '"/><bgColor indexed="64"/></patternFill></fill>');
        }
        $borde = 0;
        if (!empty($e['border'])) {
            $lado = '<%1$s style="' . ($e['borderStyle'] ?? 'thin') . '"><color rgb="FF' . ($e['borderColor'] ?? 'D5DAEA') . '"/></%1$s>';
            $l = fn ($n) => sprintf($lado, $n);
            $xml = $e['border'] === 'all'
                ? '<border>' . $l('left') . $l('right') . $l('top') . $l('bottom') . '<diagonal/></border>'
                : '<border><left/><right/><top/>' . $l('bottom') . '<diagonal/></border>';
            $borde = $this->indice($this->bordes, $xml);
        }
        $fmt = 0;
        if (!empty($e['fmt'])) {
            $fmt = ['0' => 1, '0.00' => 2, '0%' => 9, '0.00%' => 10][$e['fmt']] ?? null;
            if ($fmt === null) {
                $fmt = $this->formatos[$e['fmt']] ??= 164 + count($this->formatos);
            }
        }

        $alin = '';
        if (!empty($e['h']) || !empty($e['v']) || !empty($e['wrap']) || !empty($e['indent'])) {
            $alin = '<alignment' . (!empty($e['h']) ? ' horizontal="' . $e['h'] . '"' : '')
                . (!empty($e['v']) ? ' vertical="' . $e['v'] . '"' : '')
                . (!empty($e['wrap']) ? ' wrapText="1"' : '')
                . (!empty($e['indent']) ? ' indent="' . (int) $e['indent'] . '"' : '') . '/>';
        }
        $xf = '<xf numFmtId="' . $fmt . '" fontId="' . $fuente . '" fillId="' . $relleno . '" borderId="' . $borde . '" xfId="0"'
            . ($fmt ? ' applyNumberFormat="1"' : '') . ($fuente ? ' applyFont="1"' : '') . ($relleno ? ' applyFill="1"' : '')
            . ($borde ? ' applyBorder="1"' : '') . ($alin ? ' applyAlignment="1">' . $alin . '</xf>' : '/>');

        $indice = count($this->estilos);
        $this->estilos[$clave] = [$indice, $xf];
        return $indice;
    }

    /** Contenido binario del archivo .xlsx. */
    public function contenido(): string
    {
        if (!$this->hojas) {
            $this->hoja('Hoja1');
        }

        $zip = new XlsxLigeroZip();
        $n = count($this->hojas);
        $tipos = '';
        $rels = '';
        $hojasXml = '';
        $nombres = '';
        foreach ($this->hojas as $i => $h) {
            $k = $i + 1;
            $tipos .= '<Override PartName="/xl/worksheets/sheet' . $k . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $rels .= '<Relationship Id="rId' . $k . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $k . '.xml"/>';
            $hojasXml .= '<sheet name="' . self::esc($h->nombre) . '" sheetId="' . $k . '" r:id="rId' . $k . '"/>';
            $ref = "'" . str_replace("'", "''", $h->nombre) . "'!";
            if ($h->autofiltro) {
                $nombres .= '<definedName name="_xlnm._FilterDatabase" localSheetId="' . $i . '" hidden="1">' . self::esc($ref . self::absoluto($h->autofiltro)) . '</definedName>';
            }
            if ($h->repetirFilas) {
                $nombres .= '<definedName name="_xlnm.Print_Titles" localSheetId="' . $i . '">' . self::esc($ref . '$' . $h->repetirFilas[0] . ':$' . $h->repetirFilas[1]) . '</definedName>';
            }
            $zip->agregar('xl/worksheets/sheet' . $k . '.xml', $h->xml($i === 0));
        }

        $zip->agregar('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $tipos
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>');
        $zip->agregar('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>');
        $ahora = gmdate('Y-m-d\TH:i:s\Z');
        $zip->agregar('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>' . self::esc($this->propiedades['titulo']) . '</dc:title><dc:creator>' . self::esc($this->propiedades['autor']) . '</dc:creator>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $ahora . '</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">' . $ahora . '</dcterms:modified>'
            . '</cp:coreProperties>');
        $zip->agregar('docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Microsoft Excel</Application></Properties>');
        $zip->agregar('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<bookViews><workbookView activeTab="0"/></bookViews><sheets>' . $hojasXml . '</sheets>'
            . ($nombres ? '<definedNames>' . $nombres . '</definedNames>' : '') . '</workbook>');
        $zip->agregar('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $rels
            . '<Relationship Id="rId' . ($n + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');
        $zip->agregar('xl/styles.xml', $this->xmlEstilos());

        return $zip->contenido();
    }

    // ─────────────────────────── Internos ───────────────────────────

    private function xmlEstilos(): string
    {
        $fmts = '';
        foreach ($this->formatos as $codigo => $id) {
            $fmts .= '<numFmt numFmtId="' . $id . '" formatCode="' . self::esc($codigo) . '"/>';
        }
        $xfs = implode('', array_column($this->estilos, 1));

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . ($fmts ? '<numFmts count="' . count($this->formatos) . '">' . $fmts . '</numFmts>' : '')
            . '<fonts count="' . count($this->fuentes) . '">' . implode('', $this->fuentes) . '</fonts>'
            . '<fills count="' . count($this->rellenos) . '">' . implode('', $this->rellenos) . '</fills>'
            . '<borders count="' . count($this->bordes) . '">' . implode('', $this->bordes) . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="' . count($this->estilos) . '">' . $xfs . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/><tableStyles count="0"/>'
            . '</styleSheet>';
    }

    private function xmlFuente(array $e): string
    {
        return '<font>' . (!empty($e['bold']) ? '<b/>' : '') . (!empty($e['italic']) ? '<i/>' : '')
            . '<sz val="' . ($e['size'] ?? 10) . '"/><color rgb="FF' . ($e['color'] ?? '1F2440') . '"/>'
            . '<name val="Calibri"/><family val="2"/></font>';
    }

    private function indice(array &$lista, string $xml): int
    {
        $i = array_search($xml, $lista, true);
        if ($i === false) {
            $lista[] = $xml;
            $i = count($lista) - 1;
        }
        return $i;
    }

    public static function esc(string $t): string
    {
        // Quita caracteres de control que no admite XML (excepto tab y saltos de línea)
        $t = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $t) ?? '';
        return htmlspecialchars($t, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    public static function absoluto(string $rango): string
    {
        return preg_replace('/([A-Z]+)(\d+)/', '\$$1\$$2', $rango);
    }

    /** "A" → 1, "AB" → 28 */
    public static function numeroColumna(string $letras): int
    {
        $n = 0;
        foreach (str_split(strtoupper($letras)) as $c) {
            $n = $n * 26 + (ord($c) - 64);
        }
        return $n;
    }

    /** 1 → "A", 28 → "AB" */
    public static function letraColumna(int $n): string
    {
        $s = '';
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $s = chr(65 + $m) . $s;
            $n = intdiv($n - 1, 26);
        }
        return $s;
    }

    /** Fecha como número de serie de Excel (días desde 1899-12-30). */
    public static function fechaExcel(\DateTimeInterface $f): float
    {
        $base = new \DateTimeImmutable('1899-12-30 00:00:00', $f->getTimezone());
        $d = \DateTimeImmutable::createFromInterface($f);
        $dias = (int) $base->diff($d->setTime(0, 0))->format('%a');
        return $dias + ((int) $d->format('H') * 3600 + (int) $d->format('i') * 60 + (int) $d->format('s')) / 86400;
    }
}

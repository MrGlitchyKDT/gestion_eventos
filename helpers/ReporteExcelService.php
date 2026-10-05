<?php
declare(strict_types=1);

/**
 * Genera libros XLSX simples sin depender de servicios o librerías externas.
 * Las listas se almacenan como texto para conservar CI, códigos y fechas.
 */
final class ReporteExcelService {
    public static function descargar(string $nombreArchivo, string $nombreHoja, array $encabezados, array $filas): void {
        $baseTemporal = tempnam(sys_get_temp_dir(), 'cert_die_xlsx_');
        if ($baseTemporal === false) {
            throw new RuntimeException('No se pudo preparar el archivo Excel temporal.');
        }
        @unlink($baseTemporal);
        $rutaExcel = $baseTemporal;
        $rutaTar = null;

        try {
            $archivos = [
                '[Content_Types].xml' => self::tiposContenido(),
                '_rels/.rels' => self::relacionesRaiz(),
                'xl/workbook.xml' => self::libro($nombreHoja),
                'xl/_rels/workbook.xml.rels' => self::relacionesLibro(),
                'xl/styles.xml' => self::estilos(),
                'xl/worksheets/sheet1.xml' => self::hoja($encabezados, $filas),
            ];

            if (class_exists(ZipArchive::class)) {
                $zip = new ZipArchive();
                if ($zip->open($rutaExcel, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                    throw new RuntimeException('No se pudo crear el archivo Excel.');
                }
                foreach ($archivos as $ruta => $contenido) $zip->addFromString($ruta, $contenido);
                $zip->close();
            } elseif (class_exists(PharData::class)) {
                // XAMPP no siempre habilita ZipArchive. PharData crea el mismo
                // contenedor ZIP/Open XML requerido por los archivos .xlsx.
                $rutaTar = $baseTemporal . '.tar';
                $archivoTar = new PharData($rutaTar);
                foreach ($archivos as $ruta => $contenido) $archivoTar->addFromString($ruta, $contenido);
                $archivoXlsx = $archivoTar->convertToData(Phar::ZIP, Phar::NONE, '.xlsx');
                $rutaExcel = $archivoXlsx->getPath();
                unset($archivoXlsx, $archivoTar);
            } else {
                throw new RuntimeException('PHP no dispone de una extensión para crear archivos Excel.');
            }

            $archivoSeguro = preg_replace('/[^A-Za-z0-9_-]+/', '_', $nombreArchivo) ?: 'reporte';
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $archivoSeguro . '_' . date('Ymd_His') . '.xlsx"');
            header('Content-Length: ' . (string)filesize($rutaExcel));
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            readfile($rutaExcel);
        } finally {
            @unlink($rutaExcel);
            if ($rutaTar) @unlink($rutaTar);
        }
        exit();
    }

    private static function hoja(array $encabezados, array $filas): string {
        $filasExcel = [self::fila(1, $encabezados, true)];
        $anchos = array_map(static fn($valor): int => min(45, max(12, mb_strlen((string)$valor) + 3)), $encabezados);

        foreach ($filas as $indice => $fila) {
            $valores = array_values($fila);
            foreach ($valores as $columna => $valor) {
                $longitud = mb_strlen((string)$valor) + 3;
                $anchos[$columna] = min(45, max($anchos[$columna] ?? 12, $longitud));
            }
            $filasExcel[] = self::fila($indice + 2, $valores);
        }

        $columnas = '';
        foreach ($anchos as $indice => $ancho) {
            $columna = $indice + 1;
            $columnas .= '<col min="' . $columna . '" max="' . $columna . '" width="' . $ancho . '" customWidth="1"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>' . $columnas . '</cols><sheetData>' . implode('', $filasExcel) . '</sheetData>'
            . '<autoFilter ref="A1:' . self::letraColumna(max(1, count($encabezados))) . '1"/>'
            . '</worksheet>';
    }

    private static function fila(int $numero, array $valores, bool $encabezado = false): string {
        $celdas = '';
        foreach ($valores as $indice => $valor) {
            $referencia = self::letraColumna($indice + 1) . $numero;
            $texto = self::textoSeguro($valor);
            $estilo = $encabezado ? ' s="1"' : '';
            $celdas .= '<c r="' . $referencia . '" t="inlineStr"' . $estilo . '><is><t xml:space="preserve">' . $texto . '</t></is></c>';
        }
        return '<row r="' . $numero . '">' . $celdas . '</row>';
    }

    private static function textoSeguro(mixed $valor): string {
        $texto = trim((string)$valor);
        // Evita que Excel interprete datos recibidos como fórmulas.
        if ($texto !== '' && in_array($texto[0], ['=', '+', '-', '@'], true)) $texto = "'" . $texto;
        return htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function letraColumna(int $numero): string {
        $letra = '';
        while ($numero > 0) {
            $resto = ($numero - 1) % 26;
            $letra = chr(65 + $resto) . $letra;
            $numero = intdiv($numero - 1, 26);
        }
        return $letra;
    }

    private static function tiposContenido(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private static function relacionesRaiz(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function libro(string $nombreHoja): string {
        $nombreSeguro = htmlspecialchars(mb_substr($nombreHoja, 0, 31), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $nombreSeguro . '" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private static function relacionesLibro(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function estilos(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1A428A"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '</styleSheet>';
    }
}

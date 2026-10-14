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
        $filasExcel = [self::fila(1, $encabezados)];
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

        $ultimaColumna = self::letraColumna(max(1, count($encabezados)));
        $ultimaFila = max(1, count($filas) + 1);
        $rango = 'A1:' . $ultimaColumna . $ultimaFila;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="' . $rango . '"/><sheetViews><sheetView workbookViewId="0"/></sheetViews><sheetFormatPr defaultRowHeight="15"/>'
            . '<cols>' . $columnas . '</cols><sheetData>' . implode('', $filasExcel) . '</sheetData>'
            . '<autoFilter ref="' . $rango . '"/>'
            . '</worksheet>';
    }

    private static function fila(int $numero, array $valores): string {
        $celdas = '';
        foreach ($valores as $indice => $valor) {
            $referencia = self::letraColumna($indice + 1) . $numero;
            $texto = self::textoSeguro($valor);
            $celdas .= '<c r="' . $referencia . '" t="inlineStr"><is><t xml:space="preserve">' . $texto . '</t></is></c>';
        }
        return '<row r="' . $numero . '">' . $celdas . '</row>';
    }

    private static function textoSeguro(mixed $valor): string {
        $texto = trim((string)$valor);
        // Evita que Excel interprete datos recibidos como fórmulas.
        if ($texto !== '' && in_array($texto[0], ['=', '+', '-', '@'], true)) $texto = "'" . $texto;
        // XML 1.0 no admite caracteres de control, que hacen que Excel intente
        // reparar el libro cuando provienen de campos de texto libres.
        $texto = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $texto) ?? '';
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
            . '<workbookPr/><bookViews><workbookView workbookViewId="0"/></bookViews>'
            . '<sheets><sheet name="' . $nombreSeguro . '" sheetId="1" r:id="rId1"/></sheets><calcPr calcId="0"/></workbook>';
    }

    private static function relacionesLibro(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }
}

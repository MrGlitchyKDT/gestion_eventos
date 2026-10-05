<?php
declare(strict_types=1);

// Verifica el contenedor ZIP que utiliza la exportación XLSX cuando ZipArchive
// no está disponible en la instalación local de PHP.
$base = tempnam(sys_get_temp_dir(), 'cert_die_excel_test_');
if ($base === false) throw new RuntimeException('No se pudo crear el archivo temporal.');
@unlink($base);
$tar = $base . '.tar';

try {
    $archivoTar = new PharData($tar);
    $archivoTar->addFromString('xl/worksheets/sheet1.xml', '<worksheet/>');
    $archivoXlsx = $archivoTar->convertToData(Phar::ZIP, Phar::NONE, '.xlsx');
    $rutaXlsx = $archivoXlsx->getPath();
    if (!is_file($rutaXlsx) || filesize($rutaXlsx) === 0) {
        throw new RuntimeException('No se pudo generar el contenedor XLSX.');
    }
    $archivoZip = new PharData($rutaXlsx);
    if (!isset($archivoZip['xl/worksheets/sheet1.xml'])) {
        throw new RuntimeException('El contenedor XLSX no contiene la hoja de cálculo.');
    }
    echo "Contenedor XLSX generado correctamente.\n";
} finally {
    @unlink($rutaXlsx ?? '');
    @unlink($tar);
}

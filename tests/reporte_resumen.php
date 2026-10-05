<?php
declare(strict_types=1);

require_once __DIR__ . '/../controllers/ReporteController.php';

$reporte = new ReporteController();
$filtros = $reporte->normalizarFiltrosResumen([
    'mes' => '2026-09',
    'resumen_areas_present' => '1',
    'resumen_areas' => [1, 2],
    'resumen_tipos_present' => '1',
    'resumen_tipos' => [1, 2],
]);

$areas = $reporte->resumenPorAreas($filtros);
$tipos = $reporte->resumenPorTipos($filtros);
if (!is_array($areas) || !is_array($tipos)) {
    throw new RuntimeException('Los resúmenes deben devolver listas.');
}

echo 'Resumen por áreas: ' . count($areas) . PHP_EOL;
echo 'Resumen por tipos: ' . count($tipos) . PHP_EOL;

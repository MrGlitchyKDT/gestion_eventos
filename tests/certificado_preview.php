<?php
require_once __DIR__ . '/../models/PlantillaCertificado.php';
require_once __DIR__ . '/../helpers/CertificadoPdfService.php';

$plantilla = Database::getConnection()->query(
    "SELECT p.*, e.titulo AS evento_titulo, e.horas_academicas, e.fecha_inicio, e.fecha_fin
     FROM plantillas_certificado p
     INNER JOIN eventos e ON e.id_evento = p.id_evento
     WHERE p.activa = 1
     ORDER BY p.id_plantilla DESC LIMIT 1"
)->fetch();

if (!$plantilla) {
    throw new RuntimeException('No existe una plantilla activa para probar la vista previa.');
}

$pdf = (new CertificadoPdfService())->generarVistaPrevia($plantilla, array_merge($plantilla, [
    'nombres' => 'Nombre',
    'apellidos' => 'de prueba',
    'ci' => '12345678',
    'rol_expositor' => '',
    'codigo_unico' => 'VISTA-PREVIA',
    'codigo_qr' => 'https://example.test/verificar/VISTA-PREVIA',
]));

if (!str_starts_with($pdf, '%PDF') || strlen($pdf) < 100) {
    throw new RuntimeException('La vista previa no devolvió un PDF válido.');
}

echo "Vista previa PDF generada en memoria correctamente.\n";

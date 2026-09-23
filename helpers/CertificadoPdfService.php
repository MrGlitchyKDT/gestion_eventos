<?php
require_once __DIR__ . '/../vendor/autoload.php';

class CertificadoPdfService {
    public function generar(array $plantilla, array $certificado): string {
        $origen = __DIR__ . '/../storage/plantillas_certificado/' . basename($plantilla['ruta_archivo_pdf']);
        if (!is_file($origen)) throw new Exception('La plantilla configurada no está disponible en el almacenamiento.');
        $pdf = new \setasign\Fpdi\Fpdi();
        $paginas = $pdf->setSourceFile($origen);
        $pagina = min(max(1, (int)$plantilla['pagina']), $paginas);
        $tpl = $pdf->importPage($pagina);
        $tamano = $pdf->getTemplateSize($tpl);
        $pdf->AddPage($tamano['orientation'], [$tamano['width'], $tamano['height']]);
        $pdf->useTemplate($tpl);
        $config = json_decode($plantilla['configuracion_campos'], true) ?: [];
        $lienzo = $config['_lienzo'] ?? ['ancho' => 297, 'alto' => 210];
        $escalaX = $tamano['width'] / max(1, (float)($lienzo['ancho'] ?? 297));
        $escalaY = $tamano['height'] / max(1, (float)($lienzo['alto'] ?? 210));
        $valores = [
            'nombre' => trim($certificado['nombres'] . ' ' . $certificado['apellidos']),
            'ci' => 'C.I. ' . $certificado['ci'],
            'evento' => $certificado['evento_titulo'],
            'horas' => $certificado['horas_academicas'] . ' horas académicas',
            'fechas' => 'Del ' . date('d/m/Y', strtotime($certificado['fecha_inicio'])) . ' al ' . date('d/m/Y', strtotime($certificado['fecha_fin'])),
            'codigo' => $certificado['codigo_unico'],
            'validacion' => 'Verifique: ' . ($certificado['codigo_qr'] ?? '')
        ];
        foreach ($valores as $clave => $texto) {
            $campo = $config[$clave] ?? null;
            if (!is_array($campo) || empty($campo['activo'])) continue;
            $pdf->SetFont('Arial', $campo['negrita'] ?? false ? 'B' : '', max(6, min(36, (float)($campo['tamano'] ?? 10))));
            $pdf->SetTextColor(20, 55, 95);
            $anchoConfigurado = (float)($campo['ancho'] ?? 100) * $escalaX;
            $alineacion = $campo['alineacion'] ?? 'C';
            $x = (float)($campo['x'] ?? 15) * $escalaX;
            // Las configuraciones antiguas usaban el borde de la caja. Las nuevas,
            // identificadas con ancla=centro, usan el punto arrastrado como ancla.
            $anclaCentro = ($campo['ancla'] ?? '') === 'centro';
            if ($alineacion === 'C') $x += $anclaCentro ? 0 : $anchoConfigurado / 2;
            elseif ($alineacion === 'R') $x += $anclaCentro ? 0 : $anchoConfigurado;
            $anchoCampo = min($anchoConfigurado, max(1, $x * 2), max(1, ($tamano['width'] - $x) * 2));
            $xCaja = $alineacion === 'C' ? $x - ($anchoCampo / 2) : ($alineacion === 'R' ? $x - $anchoCampo : $x);
            $y = ((float)($campo['y'] ?? 15) * $escalaY) - 3.5;
            $pdf->SetXY(max(0, $xCaja), max(0, $y));
            $pdf->Cell($anchoCampo, 7, $this->texto($texto), 0, 0, $alineacion);
        }
        $qr = $config['qr'] ?? null;
        if (is_array($qr) && !empty($qr['activo']) && !empty($certificado['codigo_qr'])) {
            $this->insertarQr($pdf, $certificado['codigo_qr'], $qr, $escalaX, $escalaY);
        }
        $dir = __DIR__ . '/../storage/certificados/'; if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ruta = basename($certificado['codigo_unico']) . '.pdf';
        $pdf->Output('F', $dir . $ruta);
        return $ruta;
    }
    private function texto(string $texto): string { return iconv('UTF-8', 'windows-1252//TRANSLIT', $texto) ?: $texto; }

    private function insertarQr(\FPDF $pdf, string $contenido, array $campo, float $escalaX, float $escalaY): void {
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&format=png&data=' . urlencode($contenido);
        $imagen = @file_get_contents($url);
        if ($imagen === false) return; // El folio y la URL de validación permanecen impresos como respaldo.
        $temporal = tempnam(sys_get_temp_dir(), 'uab-qr-') . '.png';
        try {
            file_put_contents($temporal, $imagen);
            $lado = (float)($campo['tamano'] ?? 28) * min($escalaX, $escalaY);
            $x = ((float)($campo['x'] ?? 240) * $escalaX) - ($lado / 2);
            $y = ((float)($campo['y'] ?? 165) * $escalaY) - ($lado / 2);
            $pdf->Image($temporal, max(0, $x), max(0, $y), $lado, $lado, 'PNG');
        } finally { if (is_file($temporal)) @unlink($temporal); }
    }
}

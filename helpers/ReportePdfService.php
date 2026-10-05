<?php
require_once __DIR__ . '/../vendor/autoload.php';

/** Genera listados administrativos imprimibles sin depender de servicios externos. */
class ReportePdfService {
    private function texto(string $valor): string {
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $valor) ?: $valor;
    }

    private function encabezado(FPDF $pdf, string $titulo, string $subtitulo, array $columnas): void {
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(28, 70, 140);
        $pdf->Cell(0, 8, $this->texto($titulo), 0, 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(70, 70, 70);
        $pdf->Cell(0, 6, $this->texto($subtitulo), 0, 1);
        $pdf->Ln(2);
        $pdf->SetFillColor(33, 74, 145);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($columnas as [$texto, $ancho]) $pdf->Cell($ancho, 7, $this->texto($texto), 0, 0, 'C', true);
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
    }

    public function descargarAsistencia(array $evento, array $sesion, array $filas): void {
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(9, 9, 9);
        $pdf->SetAutoPageBreak(true, 12);
        $columnas = [['Participante', 59], ['CI', 28], ['Inscripción', 26], ['Asistencia', 28], ['Observación', 68], ['Registro', 40]];
        $titulo = 'Lista de asistencia por sesión';
        $subtitulo = $evento['titulo'] . ' — ' . $sesion['titulo'] . ' — ' . date('d/m/Y', strtotime($sesion['fecha']));
        $pdf->AddPage();
        $this->encabezado($pdf, $titulo, $subtitulo, $columnas);
        foreach ($filas as $fila) {
            if ($pdf->GetY() > 184) { $pdf->AddPage(); $this->encabezado($pdf, $titulo, $subtitulo, $columnas); }
            $pdf->Cell(59, 6, $this->texto(trim($fila['nombres'] . ' ' . $fila['apellidos'])), 1);
            $pdf->Cell(28, 6, $this->texto($fila['ci']), 1);
            $pdf->Cell(26, 6, $this->texto($fila['estado_inscripcion']), 1, 0, 'C');
            $pdf->Cell(28, 6, $this->texto($fila['estado_asistencia']), 1, 0, 'C');
            $pdf->Cell(68, 6, $this->texto($fila['observacion'] ?: '—'), 1);
            $pdf->Cell(40, 6, $this->texto($fila['fecha_registro'] ? date('d/m/Y H:i', strtotime($fila['fecha_registro'])) : '—'), 1, 1, 'C');
        }
        $pdf->Output('D', 'asistencia_sesion_' . (int)$sesion['id_sesion'] . '.pdf');
        exit();
    }

    public function descargarCertificados(array $evento, array $filas, string $nombreCertificado): void {
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(9, 9, 9);
        $pdf->SetAutoPageBreak(true, 12);
        $columnas = [['Certificado', 47], ['Titular', 56], ['CI', 27], ['Tipo', 31], ['Folio', 48], ['Emisión', 30], ['Estado', 28]];
        $titulo = 'Listado de certificaciones';
        $subtitulo = $evento['titulo'] . ' — ' . $nombreCertificado;
        $pdf->AddPage();
        $this->encabezado($pdf, $titulo, $subtitulo, $columnas);
        foreach ($filas as $fila) {
            if ($pdf->GetY() > 184) { $pdf->AddPage(); $this->encabezado($pdf, $titulo, $subtitulo, $columnas); }
            $pdf->Cell(47, 6, $this->texto($fila['nombre_plantilla']), 1);
            $pdf->Cell(56, 6, $this->texto(trim($fila['nombres'] . ' ' . $fila['apellidos'])), 1);
            $pdf->Cell(27, 6, $this->texto($fila['ci']), 1);
            $pdf->Cell(31, 6, $this->texto($fila['tipo_participacion']), 1, 0, 'C');
            $pdf->Cell(48, 6, $this->texto($fila['codigo_unico']), 1);
            $pdf->Cell(30, 6, $this->texto(date('d/m/Y', strtotime($fila['fecha_emision']))), 1, 0, 'C');
            $pdf->Cell(28, 6, $this->texto($fila['estado']), 1, 1, 'C');
        }
        $pdf->Output('D', 'certificaciones_evento_' . (int)$evento['id_evento'] . '.pdf');
        exit();
    }
}

<?php
// helpers/QrHelper.php
require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\Data\Byte;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrHelper {
    /**
     * RF-54: Genera la URL pública de verificación del certificado
     */
    public static function generarUrlVerificacion(string $codigoUnico): string {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Obtener la ruta base del proyecto
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = str_replace('\\', '/', dirname($script));
        $baseDir = rtrim($baseDir, '/');

        return "{$protocolo}://{$host}{$baseDir}/index.php?action=verificar_certificado&codigo=" . urlencode($codigoUnico);
    }

    /** URL pública de la ficha desde la cual una persona puede inscribirse al evento. */
    public static function generarUrlInscripcionEvento(int $idEvento): string {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = rtrim(str_replace('\\', '/', dirname($script)), '/');
        return "{$protocolo}://{$host}{$baseDir}/index.php?action=detalle_evento&id=" . $idEvento;
    }

    /** Retorna una ruta local que sirve el QR como SVG, sin depender de servicios externos. */
    public static function generarImagenQr(string $urlContenido, int $dimension = 150): string {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        return $script . '?action=generar_qr&contenido=' . rawurlencode($urlContenido) . '&d=' . max(64, min(1200, $dimension));
    }

    /** Genera el SVG para mostrar un QR en el navegador. */
    public static function generarSvg(string $contenido): string {
        self::validarContenido($contenido);
        $opciones = new QROptions([
            'outputBase64' => false,
            'quietzoneSize' => 4,
        ]);
        return (new QRCode($opciones))->render($contenido);
    }

    /** Dibuja módulos QR directamente sobre un documento FPDF/FPDI sin archivos ni red. */
    public static function dibujarEnPdf(\FPDF $pdf, string $contenido, float $x, float $y, float $lado): void {
        self::validarContenido($contenido);
        $opciones = new QROptions(['quietzoneSize' => 4]);
        $matriz = (new QRCode($opciones))->addSegment(new Byte($contenido))->getQRMatrix();
        $modulos = $matriz->getMatrix();
        $cantidad = max(1, $matriz->getSize());
        $paso = $lado / $cantidad;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($x, $y, $lado, $lado, 'F');
        $pdf->SetFillColor(0, 0, 0);
        foreach ($modulos as $fila => $valores) {
            foreach ($valores as $columna => $tipoModulo) {
                if ($matriz->isDark($tipoModulo)) {
                    $pdf->Rect($x + ($columna * $paso), $y + ($fila * $paso), $paso, $paso, 'F');
                }
            }
        }
    }

    private static function validarContenido(string $contenido): void {
        if ($contenido === '' || mb_strlen($contenido) > 2048 || preg_match('/[\x00-\x1F\x7F]/', $contenido)) {
            throw new InvalidArgumentException('El contenido para generar el código QR no es válido.');
        }
    }
}

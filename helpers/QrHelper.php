<?php
// helpers/QrHelper.php

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

    /**
     * Retorna la URL de renderizado dinámico del código QR (utilizable directamente en <img>)
     */
    public static function generarImagenQr(string $urlContenido, int $dimension = 150): string {
        return "https://api.qrserver.com/v1/create-qr-code/?size={$dimension}x{$dimension}&data=" . urlencode($urlContenido);
    }
}

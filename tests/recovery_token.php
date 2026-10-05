<?php
/** Verifica generación, consumo único y reversión de un token asistido. */
require_once __DIR__ . '/../models/Usuario.php';

$db = Database::getConnection();
$idUsuario = (int)$db->query('SELECT id_usuario FROM usuarios WHERE activo = 1 ORDER BY id_usuario LIMIT 1')->fetchColumn();
if ($idUsuario <= 0) {
    throw new RuntimeException('No hay usuarios activos para realizar la prueba.');
}

$db->beginTransaction();
try {
    $usuarios = new Usuario();
    $recuperacion = $usuarios->crearTokenRecuperacion($idUsuario);
    if (!$recuperacion || !$usuarios->validarToken($recuperacion['token'])) {
        throw new RuntimeException('El token generado no pudo validarse.');
    }
    if (!$usuarios->reestablecerPasswordConToken($recuperacion['token'], 'PruebaSegura123')) {
        throw new RuntimeException('El token no pudo consumirse.');
    }
    if ($usuarios->validarToken($recuperacion['token'])) {
        throw new RuntimeException('El token siguió vigente tras ser utilizado.');
    }
    $db->rollBack();
    echo "Prueba de recuperación asistida: OK\n";
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $e;
}

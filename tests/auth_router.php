<?php
// Router exclusivo de auth_http.py: no se puede ejecutar bajo Apache.
if (PHP_SAPI !== 'cli-server' || getenv('UAB_AUTH_TEST_MODE') !== '1') {
    http_response_code(404);
    exit();
}
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/__health') {
    exit('auth-test-ready');
}
$acciones = ['login', 'do_login', 'registro', 'do_registro', 'logout', 'catalogo',
    'admin_dashboard', 'participante_dashboard', 'expositor_eventos'];
if (!in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ['/', '/index.php'], true)
    || (isset($_GET['action']) && !in_array($_GET['action'], $acciones, true))) {
    http_response_code(404);
    exit();
}
$statePath = getenv('UAB_AUTH_TEST_STATE');
if (!$statePath || !is_file($statePath)) {
    http_response_code(500);
    exit('Faltan los fixtures temporales del runner.');
}
require_once __DIR__ . '/../config/Database.php';
$db = Database::getConnection();

// Lee solamente estructuras; las tablas temporales ocultan las permanentes
// en esta conexión. LIKE no copia datos ni claves foráneas.
$tablas = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tablas as $tabla) {
    $nombre = '`' . str_replace('`', '``', $tabla) . '`';
    // MariaDB exige nombres distintos en los dos lados de CREATE ... LIKE.
    $copia = '`auth_test_' . bin2hex(random_bytes(8)) . '`';
    $db->exec("CREATE TEMPORARY TABLE $copia LIKE $nombre");
    $db->exec("CREATE TEMPORARY TABLE $nombre LIKE $copia");
    $db->exec("DROP TEMPORARY TABLE $copia");
}
// Una transacción READ ONLY permite DML temporal e impide modificar tablas reales.
$db->exec('SET SESSION TRANSACTION READ ONLY');
$db->beginTransaction();
$state = json_decode(file_get_contents($statePath), true, 512, JSON_THROW_ON_ERROR);
$insert = $db->prepare('INSERT INTO roles (id_rol, nombre) VALUES (?, ?)');
foreach ($state['roles'] as $rol) {
    $insert->execute([$rol['id_rol'], $rol['nombre']]);
}
$columnas = ['id_usuario', 'ci', 'nombres', 'apellidos', 'correo', 'telefono', 'password_hash', 'id_rol', 'activo'];
$insert = $db->prepare('INSERT INTO usuarios (' . implode(', ', $columnas) . ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($state['usuarios'] as $usuario) {
    $insert->execute(array_map(static fn($campo) => $usuario[$campo], $columnas));
}
// Conserva únicamente fixtures sintéticos entre requests, fuera de public.
register_shutdown_function(static function () use ($db, $statePath, $columnas): void {
    $state = [
        'roles' => $db->query('SELECT id_rol, nombre FROM roles ORDER BY id_rol')->fetchAll(),
        'usuarios' => $db->query('SELECT ' . implode(', ', $columnas) . ' FROM usuarios ORDER BY id_usuario')->fetchAll(),
    ];
    file_put_contents($statePath, json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
    if ($db->inTransaction()) {
        $db->rollBack();
    }
});
require __DIR__ . '/../public/index.php';

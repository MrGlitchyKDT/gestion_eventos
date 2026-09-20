<?php
// helpers/AuthHelper.php

class AuthHelper {
    private const INICIOS_POR_ROL = [
        'ADMINISTRADOR' => 'admin_dashboard',
        'EXPOSITOR' => 'expositor_eventos',
        'PARTICIPANTE' => 'participante_dashboard',
    ];

    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            session_set_cookie_params([
                'httponly' => true,
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function estaAutenticado(): bool {
        self::initSession();
        return !empty($_SESSION['autenticado'])
            && !empty($_SESSION['usuario_id'])
            && (int)($_SESSION['usuario']['id_usuario'] ?? 0) === (int)$_SESSION['usuario_id']
            && self::obtenerRutaInicio($_SESSION['usuario']['rol_nombre'] ?? '') !== null;
    }

    public static function obtenerUsuario(): ?array {
        self::initSession();
        return self::estaAutenticado() ? $_SESSION['usuario'] : null;
    }

    public static function obtenerRol(): ?string {
        self::initSession();
        return self::estaAutenticado() ? $_SESSION['usuario']['rol_nombre'] : null;
    }

    public static function obtenerRutaInicio(?string $rol = null): ?string {
        self::initSession();
        $rol = strtoupper(trim($rol ?? ($_SESSION['usuario']['rol_nombre'] ?? '')));
        return self::INICIOS_POR_ROL[$rol] ?? null;
    }

    public static function redirigirInicio(): void {
        $ruta = self::estaAutenticado() ? self::obtenerRutaInicio() : 'login';
        header('Location: index.php?action=' . $ruta, true, 303);
        exit();
    }

    /** Mantiene los permisos vigentes cuando administración cambia el rol o desactiva la cuenta. */
    public static function verificarSesionActual(): void {
        self::initSession();
        if (empty($_SESSION['usuario_id']) || empty($_SESSION['autenticado'])) {
            return;
        }

        require_once __DIR__ . '/../models/Usuario.php';
        $usuario = (new Usuario())->obtenerPorId((int)$_SESSION['usuario_id']);
        if (!$usuario || (int)$usuario['activo'] !== 1 || self::obtenerRutaInicio($usuario['rol_nombre']) === null) {
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['error'] = 'Su cuenta ya no tiene acceso. Contacte a la administración.';
            header('Location: index.php?action=login', true, 303);
            exit();
        }

        if ((int)($_SESSION['usuario']['id_rol'] ?? 0) !== (int)$usuario['id_rol']
            || ($_SESSION['usuario']['rol_nombre'] ?? '') !== strtoupper(trim($usuario['rol_nombre']))) {
            session_regenerate_id(true);
            unset($_SESSION['csrf_token']);
        }
        self::guardarUsuario($usuario);
    }

    public static function tokenCsrf(): string {
        self::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validarCsrf($token): bool {
        self::initSession();
        return is_string($token) && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Valida si el usuario en sesión tiene uno de los roles permitidos (RF-06)
     */
    public static function requerirRol(array $rolesPermitidos): void {
        self::initSession();
        if (!self::estaAutenticado()) {
            header('Location: index.php?action=login');
            exit();
        }

        $rolActual = self::obtenerRol();
        if (!in_array($rolActual, $rolesPermitidos, true)) {
            http_response_code(403);
            die("Acceso denegado: No cuenta con los privilegios requeridos para esta sección.");
        }
    }

    /**
     * Asienta la sesión con regeneración de ID para mitigar Session Fixation
     */
    public static function autenticar(array $usuario): void {
        self::initSession();
        if ((int)($usuario['activo'] ?? 0) !== 1
            || self::obtenerRutaInicio($usuario['rol_nombre'] ?? '') === null) {
            throw new InvalidArgumentException('La cuenta no tiene un rol de acceso válido.');
        }
        session_regenerate_id(true);
        $_SESSION = [];
        self::guardarUsuario($usuario);
    }

    private static function guardarUsuario(array $usuario): void {
        $_SESSION['usuario_id']   = (int)$usuario['id_usuario'];
        $_SESSION['autenticado']  = true;
        $_SESSION['usuario']      = [
            'id_usuario' => (int)$usuario['id_usuario'],
            'ci'         => $usuario['ci'],
            'nombres'    => $usuario['nombres'],
            'apellidos'  => $usuario['apellidos'],
            'correo'     => $usuario['correo'],
            'id_rol'     => (int)$usuario['id_rol'],
            'rol_nombre' => strtoupper(trim($usuario['rol_nombre']))
        ];
    }

    public static function cerrarSesion(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}

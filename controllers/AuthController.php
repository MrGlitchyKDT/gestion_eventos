<?php
// controllers/AuthController.php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class AuthController {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * RF-02: Procesa autenticación
     */
    public function doLogin(): void {
        AuthHelper::initSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }

        if (!AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $this->errorAcceso('La sesión del formulario expiró. Intente ingresar nuevamente.', 'login');
        }

        $correo   = strtolower(trim($this->campo('correo')));
        $password = $this->campo('password');
        $_SESSION['login_correo'] = $correo;

        if ($correo === '' || $password === '') {
            $this->errorAcceso('Todos los campos son requeridos.', 'login');
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 150) {
            $this->errorAcceso('El formato de correo no es válido.', 'login');
        }

        try {
            $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
        } catch (PDOException $e) {
            error_log('Error al consultar el acceso: ' . $e->getMessage());
            $this->errorAcceso('No fue posible iniciar sesión. Intente nuevamente.', 'login');
        }

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            $this->errorAcceso('Credenciales de acceso incorrectas.', 'login');
        }

        if ((int)$usuario['activo'] !== 1) {
            $this->errorAcceso('Su cuenta está inactiva. Contacte a la administración.', 'login');
        }

        if (AuthHelper::obtenerRutaInicio($usuario['rol_nombre']) === null) {
            $this->errorAcceso('Su cuenta no tiene un rol de acceso válido. Contacte a la administración.', 'login');
        }

        AuthHelper::autenticar($usuario);

        AuthHelper::redirigirInicio();
    }

    /**
     * RF-01: Procesa el registro de nuevos participantes
     */
    public function doRegistro(): void {
        AuthHelper::initSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=registro');
            exit();
        }

        if (!AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $this->errorAcceso('La sesión del formulario expiró. Intente registrarse nuevamente.', 'registro');
        }

        $ci        = trim($this->campo('ci'));
        $nombres   = trim($this->campo('nombres'));
        $apellidos = trim($this->campo('apellidos'));
        $correo    = strtolower(trim($this->campo('correo')));
        $telefono  = trim($this->campo('telefono'));
        $password  = $this->campo('password');

        if (empty($ci) || empty($nombres) || empty($apellidos) || empty($correo) || empty($password)) {
            $this->errorAcceso('Debe llenar todos los campos obligatorios.', 'registro');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 150) {
            $this->errorAcceso('El formato de correo no es válido.', 'registro');
        }
        if (mb_strlen($ci) > 25 || mb_strlen($nombres) > 100
            || mb_strlen($apellidos) > 100 || mb_strlen($telefono) > 30) {
            $this->errorAcceso('Revise la longitud de sus datos personales.', 'registro');
        }

        if (mb_strlen($password) < 6) {
            $this->errorAcceso('La contraseña debe tener al menos 6 caracteres.', 'registro');
        }
        if (strlen($password) > 72) {
            $this->errorAcceso('La contraseña es demasiado larga. Use una más corta.', 'registro');
        }
        if ($password !== $this->campo('password_confirm')) {
            $this->errorAcceso('Las contraseñas no coinciden.', 'registro');
        }

        try {
            if ($this->usuarioModel->existeCorreoOCI($correo, $ci)) {
                $this->errorAcceso('El número de CI o el correo ya se encuentran registrados.', 'registro');
            }

            $resultado = $this->usuarioModel->registrar([
                'ci'        => $ci,
                'nombres'   => $nombres,
                'apellidos' => $apellidos,
                'correo'    => $correo,
                'telefono'  => $telefono,
                'password'  => $password,
            ]);
        } catch (PDOException $e) {
            error_log('Error al registrar participante: ' . $e->getMessage());
            $this->errorAcceso($e->getCode() === '23000'
                ? 'El número de CI o el correo ya se encuentran registrados.'
                : 'No fue posible crear la cuenta. Intente nuevamente.', 'registro');
        } catch (RuntimeException $e) {
            error_log('Error de configuración del registro: ' . $e->getMessage());
            $this->errorAcceso('El registro no está disponible. Contacte a la administración.', 'registro');
        }

        if ($resultado) {
            $_SESSION['success'] = "Registro completado con éxito. Puede iniciar sesión.";
            header('Location: index.php?action=login', true, 303);
        } else {
            $this->errorAcceso('Ocurrió un error al crear la cuenta.', 'registro');
        }
        exit();
    }

    /**
     * RF-03: Cierre de sesión seguro
     */
    public function logout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            http_response_code(405);
            exit('Utilice el botón Cerrar Sesión para salir de su cuenta.');
        }
        if (!AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('La sesión del formulario expiró. Recargue la página e intente nuevamente.');
        }
        AuthHelper::cerrarSesion();
        header('Location: index.php?action=login', true, 303);
        exit();
    }

    private function campo(string $nombre): string {
        return is_string($_POST[$nombre] ?? null) ? $_POST[$nombre] : '';
    }

    private function errorAcceso(string $mensaje, string $accion): void {
        $_SESSION['error'] = $mensaje;
        header('Location: index.php?action=' . $accion, true, 303);
        exit();
    }

    /**
     * RF-04: Generar token de recuperación
     */
    public function doRecuperar(): void {
        AuthHelper::initSession();
        $correo = trim($_POST['correo'] ?? '');

        if (!empty($correo)) {
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->usuarioModel->guardarTokenRecuperacion($correo, $token, $expira);
            // Simulación de envío: en producción se enviaría por correo electrónico
            $_SESSION['success'] = "Si el correo coincide con nuestros registros, recibirá las instrucciones para reestablecer su acceso.";
        }
        header('Location: index.php?action=recuperar');
        exit();
    }
}

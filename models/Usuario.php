<?php
// models/Usuario.php
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * RF-02: Obtiene usuario con su rol para autenticación
     */
    public function obtenerPorCorreo(string $correo): ?array {
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.correo = :correo 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => trim($correo)]);
        $res = $stmt->fetch();
        if (!$res) {
            return null;
        }
        $res['perfil_incompleto'] = $this->perfilIncompleto($res);
        return $res;
    }

    public function obtenerPorId(int $id_usuario): ?array {
        $sql = "SELECT u.id_usuario, u.ci, u.nombres, u.apellidos, u.correo, u.telefono,
                       u.foto_perfil, u.fecha_ultimo_cambio_foto, u.genero, u.fecha_nacimiento,
                       u.condicion_academica, u.institucion, u.fecha_registro, u.id_rol, u.activo,
                       r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $res = $stmt->fetch();
        if (!$res) {
            return null;
        }
        $res['perfil_incompleto'] = $this->perfilIncompleto($res);
        return $res;
    }

    /**
     * RF-01: Registro de nuevos participantes
     */
    public function registrar(array $datos): bool {
        // El registro público siempre crea participantes, independientemente del ID del catálogo.
        $idRol = $this->db->query("SELECT id_rol FROM roles WHERE UPPER(TRIM(nombre)) = 'PARTICIPANTE' LIMIT 1")->fetchColumn();
        if ($idRol === false) {
            throw new RuntimeException('El rol PARTICIPANTE no está configurado.');
        }
        $sql = "INSERT INTO usuarios (ci, nombres, apellidos, correo, telefono, password_hash, id_rol, activo)
                VALUES (:ci, :nombres, :apellidos, :correo, :telefono, :password_hash, :id_rol, 1)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ci'            => trim($datos['ci']),
            ':nombres'       => trim($datos['nombres']),
            ':apellidos'     => trim($datos['apellidos']),
            ':correo'        => strtolower(trim($datos['correo'])),
            ':telefono'      => trim($datos['telefono'] ?? ''),
            ':password_hash' => password_hash($datos['password'], PASSWORD_BCRYPT),
            ':id_rol'        => (int)$idRol
        ]);
    }

    /**
     * RF-05: Modificación de datos personales
     */
    public function actualizarPerfil(int $id_usuario, array $datos): bool {
        $nombres = mb_substr(trim((string)($datos['nombres'] ?? '')), 0, 100);
        $apellidos = mb_substr(trim((string)($datos['apellidos'] ?? '')), 0, 100);
        $telefono = mb_substr(trim((string)($datos['telefono'] ?? '')), 0, 30);
        $genero = strtoupper(trim((string)($datos['genero'] ?? '')));
        $fechaNacimiento = trim((string)($datos['fecha_nacimiento'] ?? ''));
        $condicion = strtoupper(trim((string)($datos['condicion_academica'] ?? '')));
        $institucion = mb_substr(trim((string)($datos['institucion'] ?? '')), 0, 200);

        if ($nombres === '' || $apellidos === '') {
            throw new InvalidArgumentException('Los nombres y apellidos son obligatorios.');
        }
        if ($genero !== '' && !in_array($genero, ['FEMENINO', 'MASCULINO', 'NO_BINARIO', 'PREFIERO_NO_INDICAR'], true)) {
            throw new InvalidArgumentException('El género seleccionado no es válido.');
        }
        if ($condicion !== '' && !in_array($condicion, ['ESTUDIANTE', 'PROFESIONAL'], true)) {
            throw new InvalidArgumentException('La condición académica seleccionada no es válida.');
        }
        if ($fechaNacimiento !== '') {
            $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaNacimiento);
            $erroresFecha = DateTimeImmutable::getLastErrors();
            if (!$fecha || ($erroresFecha !== false && ($erroresFecha['warning_count'] || $erroresFecha['error_count']))
                || $fecha->format('Y-m-d') !== $fechaNacimiento) {
                throw new InvalidArgumentException('La fecha de nacimiento no es válida.');
            }
            $hoy = new DateTimeImmutable('today');
            $edad = $fecha->diff($hoy)->y;
            if ($fecha > $hoy || $edad > 120) {
                throw new InvalidArgumentException('La fecha de nacimiento debe corresponder a una edad válida.');
            }
        }

        $sql = "UPDATE usuarios 
                SET nombres = :nombres, apellidos = :apellidos, telefono = :telefono,
                    genero = :genero, fecha_nacimiento = :fecha_nacimiento,
                    condicion_academica = :condicion_academica, institucion = :institucion
                WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombres'               => $nombres,
            ':apellidos'             => $apellidos,
            ':telefono'              => $telefono !== '' ? $telefono : null,
            ':genero'                => $genero !== '' ? $genero : null,
            ':fecha_nacimiento'      => $fechaNacimiento !== '' ? $fechaNacimiento : null,
            ':condicion_academica'   => $condicion !== '' ? $condicion : null,
            ':institucion'           => $institucion !== '' ? $institucion : null,
            ':id_usuario'            => $id_usuario
        ]);
    }

    /** Indica si falta alguno de los datos opcionales solicitados para análisis institucional. */
    public function perfilIncompleto(array $usuario): bool {
        return empty($usuario['foto_perfil'])
            || empty($usuario['genero'])
            || empty($usuario['fecha_nacimiento'])
            || empty($usuario['condicion_academica'])
            || trim((string)($usuario['institucion'] ?? '')) === '';
    }

    /** Guarda una foto de perfil y aplica el intervalo mínimo de 30 días entre cambios. */
    public function actualizarFotoPerfil(int $idUsuario, array $archivo): void {
        $usuario = $this->obtenerPorId($idUsuario);
        if (!$usuario) {
            throw new InvalidArgumentException('No se encontró la cuenta solicitada.');
        }
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Seleccione una imagen para su foto de perfil.');
        }
        if (($archivo['size'] ?? 0) < 1 || (int)$archivo['size'] > 2 * 1024 * 1024) {
            throw new InvalidArgumentException('La foto de perfil no puede superar 2 MB.');
        }
        if (!is_uploaded_file((string)($archivo['tmp_name'] ?? ''))) {
            throw new InvalidArgumentException('No se recibió una imagen válida.');
        }

        if (!empty($usuario['fecha_ultimo_cambio_foto'])) {
            $proximaFecha = (new DateTimeImmutable($usuario['fecha_ultimo_cambio_foto']))->modify('+30 days');
            if ($proximaFecha > new DateTimeImmutable('now')) {
                throw new InvalidArgumentException('Podrá cambiar su foto nuevamente el ' . $proximaFecha->format('d/m/Y') . '.');
            }
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string)$archivo['tmp_name']);
        $extensiones = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensiones[$mime])) {
            throw new InvalidArgumentException('La foto debe estar en formato JPG, PNG o WEBP.');
        }
        $dimensiones = @getimagesize((string)$archivo['tmp_name']);
        if (!$dimensiones || (int)$dimensiones[0] < 32 || (int)$dimensiones[1] < 32
            || (int)$dimensiones[0] > 5000 || (int)$dimensiones[1] > 5000) {
            throw new InvalidArgumentException('La imagen debe medir entre 32 y 5000 píxeles por lado.');
        }

        $directorio = __DIR__ . '/../storage/fotos_perfil';
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            throw new RuntimeException('No se pudo preparar el almacenamiento de fotos de perfil.');
        }
        $nombreArchivo = 'perfil_' . $idUsuario . '_' . bin2hex(random_bytes(16)) . '.' . $extensiones[$mime];
        $rutaDestino = $directorio . '/' . $nombreArchivo;
        if (!move_uploaded_file((string)$archivo['tmp_name'], $rutaDestino)) {
            throw new RuntimeException('No se pudo guardar la foto de perfil.');
        }

        try {
            $stmt = $this->db->prepare('UPDATE usuarios SET foto_perfil = :foto, fecha_ultimo_cambio_foto = NOW() WHERE id_usuario = :id');
            $stmt->execute([':foto' => $nombreArchivo, ':id' => $idUsuario]);
        } catch (Throwable $e) {
            @unlink($rutaDestino);
            throw $e;
        }

        $fotoAnterior = basename((string)($usuario['foto_perfil'] ?? ''));
        if ($fotoAnterior !== '' && $fotoAnterior !== $nombreArchivo) {
            $rutaAnterior = $directorio . '/' . $fotoAnterior;
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }
    }

    /** Ruta privada de la foto del propio usuario, lista para ser servida por el controlador. */
    public function rutaFotoPerfil(int $idUsuario): ?string {
        $usuario = $this->obtenerPorId($idUsuario);
        $nombre = basename((string)($usuario['foto_perfil'] ?? ''));
        if ($nombre === '') {
            return null;
        }
        $ruta = __DIR__ . '/../storage/fotos_perfil/' . $nombre;
        return is_file($ruta) ? $ruta : null;
    }

    /**
     * Crea una clave de recuperación de un solo uso para entrega asistida.
     * Sólo el hash queda almacenado; el valor original se devuelve una vez
     * para que el administrador lo entregue por el canal verificado.
     */
    public function crearTokenRecuperacion(int $idUsuario, int $minutosVigencia = 60): ?array {
        $token = bin2hex(random_bytes(32));
        $expiracion = (new DateTimeImmutable('now'))
            ->modify('+' . max(1, $minutosVigencia) . ' minutes')
            ->format('Y-m-d H:i:s');

        $sql = "UPDATE usuarios
                SET token_recuperacion = :token, token_expiracion = :expiracion
                WHERE id_usuario = :id_usuario AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':token' => hash('sha256', $token),
            ':expiracion' => $expiracion,
            ':id_usuario' => $idUsuario,
        ]);

        return $stmt->rowCount() === 1
            ? ['token' => $token, 'expiracion' => $expiracion]
            : null;
    }

    public function validarToken(string $token): ?array {
        $sql = "SELECT id_usuario FROM usuarios 
                WHERE token_recuperacion = :token 
                  AND token_expiracion > NOW() 
                  AND activo = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => hash('sha256', $token)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function reestablecerPassword(int $id_usuario, string $nuevaPassword): bool {
        $sql = "UPDATE usuarios 
                SET password_hash = :password_hash, token_recuperacion = NULL, token_expiracion = NULL 
                WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password_hash' => password_hash($nuevaPassword, PASSWORD_BCRYPT),
            ':id_usuario'    => $id_usuario
        ]);
    }

    /** Restablece la contraseña y consume el token en una única operación. */
    public function reestablecerPasswordConToken(string $token, string $nuevaPassword): bool {
        $sql = "UPDATE usuarios
                SET password_hash = :password_hash,
                    token_recuperacion = NULL,
                    token_expiracion = NULL
                WHERE token_recuperacion = :token
                  AND token_expiracion > NOW()
                  AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':password_hash' => password_hash($nuevaPassword, PASSWORD_BCRYPT),
            ':token' => hash('sha256', $token),
        ]);
        return $stmt->rowCount() === 1;
    }

    public function existeCorreoOCI(string $correo, string $ci, ?int $excluirId = null): bool {
        $sql = "SELECT id_usuario FROM usuarios WHERE (correo = :correo OR ci = :ci)";
        $params = [':correo' => $correo, ':ci' => $ci];
        
        if ($excluirId) {
            $sql .= " AND id_usuario != :id_usuario";
            $params[':id_usuario'] = $excluirId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }
}

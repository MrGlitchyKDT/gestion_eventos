-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2026 a las 15:01:28
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_eventos_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` bigint(20) UNSIGNED NOT NULL,
  `id_sesion` bigint(20) UNSIGNED NOT NULL,
  `id_inscripcion` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('PRESENTE','FALTA','ATRASO','JUSTIFICADO') NOT NULL DEFAULT 'PRESENTE',
  `id_usuario_registro` bigint(20) UNSIGNED NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id_asistencia`, `id_sesion`, `id_inscripcion`, `estado`, `id_usuario_registro`, `fecha_registro`, `observacion`) VALUES
(1, 1, 1, 'PRESENTE', 3, '2026-09-12 05:37:43', 'Confirmado vía llamada de lista'),
(2, 2, 1, 'PRESENTE', 1, '2026-09-12 05:29:57', 'Asistencia normal'),
(3, 3, 1, 'JUSTIFICADO', 1, '2026-09-12 05:29:57', 'Presentó certificado médico'),
(6, 6, 4, 'FALTA', 11, '2026-09-20 22:49:15', NULL),
(11, 9, 5, 'PRESENTE', 11, '2026-09-20 22:58:09', NULL),
(12, 8, 5, 'JUSTIFICADO', 11, '2026-09-20 22:58:11', NULL),
(13, 7, 5, 'FALTA', 11, '2026-09-20 22:58:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_actividad`
--

CREATE TABLE `auditoria_actividad` (
  `id_auditoria` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(80) NOT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria_actividad`
--

INSERT INTO `auditoria_actividad` (`id_auditoria`, `id_usuario`, `accion`, `modulo`, `ip_origen`, `detalles`, `fecha_registro`) VALUES
(1, 1, 'PUBLICAR_EVENTO', 'EVENTOS', '::1', 'Aprobación y publicación formal del evento ID #1 para inscripciones abiertas.', '2026-09-12 05:34:05'),
(2, 1, 'EMISION_CERTIFICADO', 'CERTIFICADOS', '::1', 'Emisión manual de certificado individual para participante CI 7890123.', '2026-09-12 05:34:05'),
(3, 1, 'CAMBIAR_ROL', 'USUARIOS', '::1', 'Ascenso administrativo a Expositor para el usuario ID #5', '2026-09-12 05:57:41'),
(4, 9, 'CAMBIAR_ROL', 'USUARIOS', '::1', 'Se asignó el rol ID #2 al usuario ID #10', '2026-09-15 13:34:29'),
(5, 9, 'CAMBIAR_ROL', 'USUARIOS', '127.0.0.1', 'Se asignó el rol ID #1 al usuario ID #1', '2026-09-19 21:33:46'),
(6, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #2 con código EVT-TEST-072729', '2026-09-19 22:57:30'),
(7, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #3 con código EVT-TEST-000001', '2026-09-19 23:00:19'),
(8, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #3 cambió su estado a PUBLICADO', '2026-09-19 23:00:23'),
(9, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #3 cambió su estado a BORRADOR', '2026-09-19 23:00:25'),
(10, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #3 cambió su estado a PUBLICADO', '2026-09-19 23:01:05'),
(11, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #4 con código EVT-TEST-000002', '2026-09-20 02:04:53'),
(12, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #4 cambió su estado a PUBLICADO', '2026-09-20 02:05:30'),
(13, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Rigoberto Bolaños al evento #4 como Expositor Taller', '2026-09-20 14:51:45'),
(14, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #4 con código EVT-TEST-000002', '2026-09-20 20:21:35'),
(15, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #4 con código EVT-TEST-000002', '2026-09-20 21:50:23'),
(16, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #6 para el evento #4', '2026-09-20 21:51:10'),
(17, 9, 'CAMBIAR_ROL', 'USUARIOS', '127.0.0.1', 'Se asignó el rol ID #2 al usuario ID #11', '2026-09-20 21:54:57'),
(18, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Eduardo Garcia Salinaz al evento #4 como Auxiliar', '2026-09-20 21:56:10'),
(19, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #5 con código EVT-TEST-000003', '2026-09-20 22:54:13'),
(20, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Eduardo Garcia Salinaz al evento #5 como Docente', '2026-09-20 22:54:30'),
(21, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #7 para el evento #5', '2026-09-20 22:55:21'),
(22, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #8 para el evento #5', '2026-09-20 22:55:56'),
(23, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #9 para el evento #5', '2026-09-20 22:56:31'),
(24, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #5 cambió su estado a PUBLICADO', '2026-09-20 22:56:34'),
(25, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #8 en el evento #5 (Inscripción #5)', '2026-09-20 22:56:50'),
(26, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #5 cambió su estado a BORRADOR', '2026-09-21 03:32:55'),
(27, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #5 cambió su estado a PUBLICADO', '2026-09-21 03:33:06'),
(28, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta elena.rios@eventos.edu (ID #2) establecida en estado activo = 0', '2026-09-21 03:44:25'),
(29, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta elena.rios@eventos.edu (ID #2) establecida en estado activo = 1', '2026-09-21 03:44:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `activo`) VALUES
(1, 'Tecnología e Informática', 1),
(2, 'Ciencias de la Salud', 1),
(3, 'Ciencias Económicas y Empresariales', 1),
(4, 'Derecho y Ciencias Jurídicas', 1),
(5, 'Educación e Innovación Pedagógica', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `id_certificado` bigint(20) UNSIGNED NOT NULL,
  `codigo_unico` varchar(50) NOT NULL,
  `id_inscripcion` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `tipo_participacion` varchar(50) NOT NULL DEFAULT 'PARTICIPANTE',
  `fecha_emision` timestamp NOT NULL DEFAULT current_timestamp(),
  `ruta_archivo_pdf` varchar(255) DEFAULT NULL,
  `codigo_qr` text DEFAULT NULL,
  `estado` enum('EMITIDO','ANULADO') NOT NULL DEFAULT 'EMITIDO',
  `motivo_anulacion` text DEFAULT NULL,
  `fecha_anulacion` datetime DEFAULT NULL,
  `id_usuario_anulacion` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `certificados`
--

INSERT INTO `certificados` (`id_certificado`, `codigo_unico`, `id_inscripcion`, `id_evento`, `id_usuario`, `tipo_participacion`, `fecha_emision`, `ruta_archivo_pdf`, `codigo_qr`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `id_usuario_anulacion`) VALUES
(1, 'CERT-2026-F2C5-B0B1', 1, 1, 4, 'PARTICIPANTE', '2026-09-12 05:31:52', NULL, NULL, 'EMITIDO', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_institucion`
--

CREATE TABLE `configuracion_institucion` (
  `id_configuracion` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `nombre_institucion` varchar(200) NOT NULL,
  `sigla` varchar(50) NOT NULL,
  `correo_contacto` varchar(150) NOT NULL,
  `telefono_contacto` varchar(50) DEFAULT NULL,
  `direccion_institucional` varchar(250) DEFAULT NULL,
  `texto_certificado_base` text DEFAULT NULL,
  `cargo_firmante_1` varchar(100) DEFAULT 'Rector / Director',
  `cargo_firmante_2` varchar(100) DEFAULT 'Coordinador Académico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_institucion`
--

INSERT INTO `configuracion_institucion` (`id_configuracion`, `nombre_institucion`, `sigla`, `correo_contacto`, `telefono_contacto`, `direccion_institucional`, `texto_certificado_base`, `cargo_firmante_1`, `cargo_firmante_2`) VALUES
(1, 'Universidad Central de Formación Continua', 'UCFC', 'contacto@eventos.edu', '+591 3 4620000', 'Campus Universitario Central', 'Por haber participado y cumplido satisfactoriamente las exigencias académicas del evento:', 'Rector / Director', 'Coordinador Académico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `descripcion` text NOT NULL,
  `id_tipo_evento` int(10) UNSIGNED NOT NULL,
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `modalidad` enum('PRESENCIAL','VIRTUAL','HIBRIDA') NOT NULL DEFAULT 'PRESENCIAL',
  `lugar` varchar(250) DEFAULT NULL,
  `enlace_virtual` text DEFAULT NULL,
  `cupo_maximo` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `fecha_inicio_inscripcion` datetime NOT NULL,
  `fecha_fin_inscripcion` datetime NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `emite_certificado` tinyint(1) NOT NULL DEFAULT 1,
  `horas_academicas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `porcentaje_asistencia_minimo` decimal(5,2) NOT NULL DEFAULT 80.00,
  `nota_minima_aprobacion` decimal(5,2) DEFAULT 0.00,
  `estado` enum('BORRADOR','PUBLICADO','EN_CURSO','FINALIZADO','CANCELADO') NOT NULL DEFAULT 'BORRADOR',
  `id_usuario_creador` bigint(20) UNSIGNED NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `codigo`, `titulo`, `descripcion`, `id_tipo_evento`, `id_categoria`, `modalidad`, `lugar`, `enlace_virtual`, `cupo_maximo`, `fecha_inicio_inscripcion`, `fecha_fin_inscripcion`, `fecha_inicio`, `fecha_fin`, `emite_certificado`, `horas_academicas`, `porcentaje_asistencia_minimo`, `nota_minima_aprobacion`, `estado`, `id_usuario_creador`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'EVT-2026-DEV01', 'Taller Avanzado de Desarrollo Web Seguro en PHP y MySQL', 'Capacitación integral orientada a la construcción de aplicaciones empresariales modulares, control de sesiones y prevención de vulnerabilidades.', 2, 1, 'PRESENCIAL', 'Laboratorio de Computación 3 - Edificio de Ingeniería', NULL, 30, '2026-09-01 08:00:00', '2026-09-14 23:59:59', '2026-09-15', '2026-09-17', 1, 20, 80.00, 0.00, 'PUBLICADO', 1, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(2, 'EVT-TEST-072729', 'Seminario de Arquitectura Limpia y Patrones de Diseño', 'Prueba unitaria automatizada para comprobar inserción y flujo relacional.', 4, 1, 'HIBRIDA', 'Bicentenario', 'https://meet.google.com/test-abc-def', 45, '2026-09-12 00:00:00', '2026-09-22 23:59:00', '2026-09-23', '2026-09-25', 1, 15, 80.00, 60.00, 'PUBLICADO', 1, '2026-09-12 05:27:30', '2026-09-19 22:57:30'),
(3, 'EVT-TEST-000001', 'Testeo de taller', 'jsdjfoisdjfosdjdfoisdjfoijs', 2, 4, 'PRESENCIAL', 'Auditorio central', 'https://meet/google.com', 25, '2026-09-30 15:00:00', '2026-10-08 15:00:00', '2026-10-12', '2026-10-12', 0, 60, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-19 23:00:19', '2026-09-19 23:01:05'),
(4, 'EVT-TEST-000002', 'test archivo', 'Testeo', 2, 3, 'VIRTUAL', 'Auditorio', 'https://meet/google.com', 60, '2026-09-20 15:00:00', '2026-09-21 00:00:00', '2026-09-21', '2026-09-21', 1, 20, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-20 02:04:53', '2026-09-20 21:50:23'),
(5, 'EVT-TEST-000003', 'Test sesiones', 'Texto descripcion', 1, 4, 'PRESENCIAL', 'Curso 5-A', NULL, 40, '2026-09-20 17:00:00', '2026-09-21 10:00:00', '2026-09-21', '2026-09-30', 1, 70, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-20 22:54:13', '2026-09-21 03:33:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_expositores`
--

CREATE TABLE `evento_expositores` (
  `id_evento_expositor` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `rol_expositor` varchar(100) DEFAULT 'Expositor Principal',
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_expositores`
--

INSERT INTO `evento_expositores` (`id_evento_expositor`, `id_evento`, `id_usuario`, `rol_expositor`, `fecha_asignacion`) VALUES
(1, 1, 3, 'Expositor Principal', '2026-09-12 05:01:47'),
(2, 2, 2, 'Expositor Principal', '2026-09-12 05:27:30'),
(3, 4, 10, 'Expositor Taller', '2026-09-20 14:51:45'),
(4, 4, 11, 'Auxiliar', '2026-09-20 21:56:10'),
(5, 5, 11, 'Docente', '2026-09-20 22:54:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` bigint(20) UNSIGNED NOT NULL,
  `codigo_inscripcion` varchar(35) NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('INSCRITO','CANCELADO','ASISTIO','APROBADO','REPROBADO') NOT NULL DEFAULT 'INSCRITO',
  `origen` enum('WEB_PARTICIPANTE','ADMINISTRATIVO') NOT NULL DEFAULT 'WEB_PARTICIPANTE',
  `porcentaje_asistencia` decimal(5,2) NOT NULL DEFAULT 0.00,
  `calificacion_final` decimal(5,2) DEFAULT NULL,
  `habilitado_certificado` tinyint(1) NOT NULL DEFAULT 0,
  `motivo_cancelacion` varchar(255) DEFAULT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id_inscripcion`, `codigo_inscripcion`, `id_evento`, `id_usuario`, `estado`, `origen`, `porcentaje_asistencia`, `calificacion_final`, `habilitado_certificado`, `motivo_cancelacion`, `fecha_inscripcion`) VALUES
(1, 'INS-DEV01-0001', 1, 4, 'INSCRITO', 'WEB_PARTICIPANTE', 100.00, NULL, 1, NULL, '2026-09-12 05:01:47'),
(2, 'INS-DEV01-0002', 1, 5, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-12 05:01:47'),
(3, 'INS-2026-971200A1', 2, 8, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-15 13:26:13'),
(4, 'INS-2026-A8990108', 4, 8, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-20 02:05:57'),
(5, 'INS-2026-5FFBDAB9', 5, 8, 'INSCRITO', 'ADMINISTRATIVO', 66.67, NULL, 0, NULL, '2026-09-20 22:56:50'),
(6, 'INS-2026-12ECE48B', 5, 12, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-21 03:29:56'),
(7, 'INS-2026-08870521', 2, 12, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-21 03:30:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales_evento`
--

CREATE TABLE `materiales_evento` (
  `id_material` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_usuario_subio` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo_archivo` varchar(50) DEFAULT NULL,
  `tamano_bytes` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materiales_evento`
--

INSERT INTO `materiales_evento` (`id_material`, `id_evento`, `id_usuario_subio`, `titulo`, `descripcion`, `ruta_archivo`, `tipo_archivo`, `tamano_bytes`, `fecha_publicacion`) VALUES
(1, 4, 9, 'Introducción - Simulación de Sistemas', NULL, '5a0f8b7a39b4b92a4c061dee272cdb371daf1f0b.pdf', 'PDF', 6101974, '2026-09-20 02:04:53'),
(2, 5, 9, 'Cuestionario Realidad extendida', NULL, '6b1da47852cf736ae05f53d6b13f766d7ccecfb1.docx', 'DOCX', 210079, '2026-09-20 22:54:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'ADMINISTRADOR', 'Acceso total a la gestión del sistema y reportes'),
(2, 'EXPOSITOR', 'Acceso a eventos asignados, materiales y registro de asistencia'),
(3, 'PARTICIPANTE', 'Inscripción a eventos, consulta de asistencia y certificados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_evento`
--

CREATE TABLE `sesiones_evento` (
  `id_sesion` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `lugar_especifico` varchar(200) DEFAULT NULL,
  `estado` enum('PROGRAMADA','EN_CURSO','CONCLUIDA','CANCELADA') NOT NULL DEFAULT 'PROGRAMADA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sesiones_evento`
--

INSERT INTO `sesiones_evento` (`id_sesion`, `id_evento`, `titulo`, `fecha`, `hora_inicio`, `hora_fin`, `lugar_especifico`, `estado`) VALUES
(1, 1, 'Sesión 1: Modelado de Datos y Arquitectura MVC', '2026-09-15', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA'),
(2, 1, 'Sesión 2: Transacciones PDO y Control de Asistencia', '2026-09-16', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA'),
(3, 1, 'Sesión 3: Emisión Criptográfica de Certificados y QR', '2026-09-17', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA'),
(4, 2, 'Sesión Inaugural: Fundamentos de Arquitectura', '2026-09-23', '18:00:00', '21:00:00', 'Auditorio A', 'PROGRAMADA'),
(5, 2, 'Sesión Práctica: Implementación de Patrones MVC', '2026-09-24', '18:00:00', '21:00:00', 'Laboratorio B', 'PROGRAMADA'),
(6, 4, 'test sesion', '2026-09-21', '13:00:00', '16:00:00', 'Auditorio', 'PROGRAMADA'),
(7, 5, 'Sesion 1', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA'),
(8, 5, 'Sesion 2', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA'),
(9, 5, 'Sesion 3', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_reimpresion`
--

CREATE TABLE `solicitudes_reimpresion` (
  `id_solicitud` bigint(20) UNSIGNED NOT NULL,
  `id_certificado` bigint(20) UNSIGNED NOT NULL,
  `id_usuario_solicitante` bigint(20) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('PENDIENTE','APROBADA','RECHAZADA','ENTREGADA') NOT NULL DEFAULT 'PENDIENTE',
  `motivo_rechazo` text DEFAULT NULL,
  `id_usuario_resolucion` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `responsable_entrega` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_reimpresion`
--

INSERT INTO `solicitudes_reimpresion` (`id_solicitud`, `id_certificado`, `id_usuario_solicitante`, `motivo`, `estado`, `motivo_rechazo`, `id_usuario_resolucion`, `fecha_solicitud`, `fecha_resolucion`, `fecha_entrega`, `responsable_entrega`) VALUES
(1, 1, 4, 'Extravío del ejemplar físico original entregado en secretaría.', 'ENTREGADA', NULL, 1, '2026-09-12 05:31:52', '2026-09-12 01:31:52', '2026-09-12 01:31:52', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_evento`
--

CREATE TABLE `tipos_evento` (
  `id_tipo_evento` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_evento`
--

INSERT INTO `tipos_evento` (`id_tipo_evento`, `nombre`, `activo`) VALUES
(1, 'Curso', 1),
(2, 'Taller', 1),
(3, 'Conferencia', 1),
(4, 'Seminario', 1),
(5, 'Capacitación Especializada', 1),
(6, 'Webinar', 1),
(7, 'Diplomado Internacional', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `ci` varchar(25) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_rol` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `ci`, `nombres`, `apellidos`, `correo`, `telefono`, `password_hash`, `id_rol`, `activo`, `token_recuperacion`, `token_expiracion`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, '4567890', 'Carlos', 'Administrador Central', 'admin@eventos.edu', '70011223', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 1, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(2, '5678901', 'Dra. Elena', 'Ríos Morales', 'elena.rios@eventos.edu', '71122334', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-21 03:44:29'),
(3, '6789012', 'Ing. Marcos', 'Torres Benítez', 'marcos.torres@eventos.edu', '72233445', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(4, '7890123', 'Juan Pablo', 'Gutiérrez Mendoza', 'juan.gutierrez@estudiante.edu', '73344556', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 3, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(5, '8901234', 'María Fernanda', 'López Roca', 'maria.lopez@estudiante.edu', '74455667', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:57:41'),
(6, '13008609', 'Jonathan', 'Robles', 'administrador@gmail.com', '67355113', '$2y$10$0o/yfCAJCNKRtyQ9nh1HXe.VCzgX1XGgZRsVhxKn16aJx2kP6Ja2G', 3, 1, NULL, NULL, '2026-09-12 06:17:01', '2026-09-12 07:05:41'),
(8, '13264670', 'luis', 'mariscal', 'abc@gmail.com', '74729305', '$2y$10$HOdwtHbsPdSBHZ8npm0byuVogevzW81pFiB6nXJj9GXLbdqmBzLH2', 3, 1, NULL, NULL, '2026-09-15 13:23:59', '2026-09-15 13:23:59'),
(9, '1000005', 'ad', 'min', '123@gmail.com', '77777777', '$2y$10$6hHfBUQ9qSqT8gqECiHlkeMAApBiGcnrus2gV.Jrn8d/Z05eNwrIG', 1, 1, NULL, NULL, '2026-09-15 13:28:06', '2026-09-15 13:28:20'),
(10, '13462670', 'Rigoberto', 'Bolaños', 'rgb@edu.bo', '111111111', '$2y$10$Xw4JZtiqaspsmUtN1dZ88e2wD.wWk/nPGBk5DN1mzwOSsGlswOdjq', 2, 1, NULL, NULL, '2026-09-15 13:33:47', '2026-09-15 13:34:29'),
(11, '25641478', 'Eduardo', 'Garcia Salinaz', 'egs@edu.bo', '78454512', '$2y$10$ZkWjQlaTI6fN6G8dK1tvieppAbZFg5kkuAfNJwUPXNoIlPsDZ1/hy', 2, 1, NULL, NULL, '2026-09-20 21:53:58', '2026-09-20 21:54:57'),
(12, '14151617', 'Ana', 'Vaca Flores', 'avf@gmail.com', '78962214', '$2y$10$D4c/mNuX8a9A05hcwyPkHOzotrgds57Js/GomxoIjXW910pcWWqiW', 3, 1, NULL, NULL, '2026-09-21 03:23:58', '2026-09-21 03:23:58');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `uq_sesion_inscripcion` (`id_sesion`,`id_inscripcion`),
  ADD KEY `fk_asistencia_inscripcion` (`id_inscripcion`),
  ADD KEY `fk_asistencia_responsable` (`id_usuario_registro`);

--
-- Indices de la tabla `auditoria_actividad`
--
ALTER TABLE `auditoria_actividad`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `idx_auditoria_usuario_fecha` (`id_usuario`,`fecha_registro`),
  ADD KEY `idx_auditoria_modulo` (`modulo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id_certificado`),
  ADD UNIQUE KEY `codigo_unico` (`codigo_unico`),
  ADD UNIQUE KEY `id_inscripcion` (`id_inscripcion`),
  ADD KEY `fk_cer_evento` (`id_evento`),
  ADD KEY `fk_cer_usuario` (`id_usuario`),
  ADD KEY `fk_cer_usuario_anula` (`id_usuario_anulacion`),
  ADD KEY `idx_certificados_codigo` (`codigo_unico`),
  ADD KEY `idx_certificados_estado` (`estado`);

--
-- Indices de la tabla `configuracion_institucion`
--
ALTER TABLE `configuracion_institucion`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_eventos_tipo` (`id_tipo_evento`),
  ADD KEY `fk_eventos_categoria` (`id_categoria`),
  ADD KEY `fk_eventos_creador` (`id_usuario_creador`),
  ADD KEY `idx_eventos_estado` (`estado`),
  ADD KEY `idx_eventos_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `evento_expositores`
--
ALTER TABLE `evento_expositores`
  ADD PRIMARY KEY (`id_evento_expositor`),
  ADD UNIQUE KEY `uq_evento_expositor` (`id_evento`,`id_usuario`),
  ADD KEY `fk_ee_usuario` (`id_usuario`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `codigo_inscripcion` (`codigo_inscripcion`),
  ADD UNIQUE KEY `uq_usuario_evento` (`id_evento`,`id_usuario`),
  ADD KEY `fk_ins_usuario` (`id_usuario`),
  ADD KEY `idx_inscripciones_estado` (`estado`);

--
-- Indices de la tabla `materiales_evento`
--
ALTER TABLE `materiales_evento`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `fk_mat_evento` (`id_evento`),
  ADD KEY `fk_mat_usuario` (`id_usuario_subio`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `idx_sesiones_evento_fecha` (`id_evento`,`fecha`);

--
-- Indices de la tabla `solicitudes_reimpresion`
--
ALTER TABLE `solicitudes_reimpresion`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_sol_certificado` (`id_certificado`),
  ADD KEY `fk_sol_solicitante` (`id_usuario_solicitante`),
  ADD KEY `fk_sol_resolutor` (`id_usuario_resolucion`),
  ADD KEY `fk_sol_responsable_entrega` (`responsable_entrega`),
  ADD KEY `idx_reimpresiones_estado` (`estado`);

--
-- Indices de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  ADD PRIMARY KEY (`id_tipo_evento`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `ci` (`ci`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_usuarios_correo` (`correo`),
  ADD KEY `idx_usuarios_ci` (`ci`),
  ADD KEY `idx_usuarios_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `auditoria_actividad`
--
ALTER TABLE `auditoria_actividad`
  MODIFY `id_auditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `evento_expositores`
--
ALTER TABLE `evento_expositores`
  MODIFY `id_evento_expositor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `materiales_evento`
--
ALTER TABLE `materiales_evento`
  MODIFY `id_material` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  MODIFY `id_sesion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `solicitudes_reimpresion`
--
ALTER TABLE `solicitudes_reimpresion`
  MODIFY `id_solicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  MODIFY `id_tipo_evento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `fk_asistencia_inscripcion` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id_inscripcion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asistencia_responsable` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asistencia_sesion` FOREIGN KEY (`id_sesion`) REFERENCES `sesiones_evento` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditoria_actividad`
--
ALTER TABLE `auditoria_actividad`
  ADD CONSTRAINT `fk_audit_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD CONSTRAINT `fk_cer_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cer_inscripcion` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id_inscripcion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cer_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cer_usuario_anula` FOREIGN KEY (`id_usuario_anulacion`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `fk_eventos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_tipo` FOREIGN KEY (`id_tipo_evento`) REFERENCES `tipos_evento` (`id_tipo_evento`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento_expositores`
--
ALTER TABLE `evento_expositores`
  ADD CONSTRAINT `fk_ee_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ee_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_ins_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ins_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales_evento`
--
ALTER TABLE `materiales_evento`
  ADD CONSTRAINT `fk_mat_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mat_usuario` FOREIGN KEY (`id_usuario_subio`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  ADD CONSTRAINT `fk_sesiones_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_reimpresion`
--
ALTER TABLE `solicitudes_reimpresion`
  ADD CONSTRAINT `fk_sol_certificado` FOREIGN KEY (`id_certificado`) REFERENCES `certificados` (`id_certificado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sol_resolutor` FOREIGN KEY (`id_usuario_resolucion`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sol_responsable_entrega` FOREIGN KEY (`responsable_entrega`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sol_solicitante` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

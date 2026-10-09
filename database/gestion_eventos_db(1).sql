-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2026 a las 05:27:30
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
  `origen_registro` enum('PARTICIPANTE','DOCENTE','ADMINISTRADOR') NOT NULL DEFAULT 'DOCENTE',
  `fecha_confirmacion_participante` datetime DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id_asistencia`, `id_sesion`, `id_inscripcion`, `estado`, `id_usuario_registro`, `origen_registro`, `fecha_confirmacion_participante`, `fecha_registro`, `observacion`) VALUES
(1, 1, 1, 'PRESENTE', 3, 'DOCENTE', NULL, '2026-09-12 05:37:43', 'Confirmado vía llamada de lista'),
(2, 2, 1, 'PRESENTE', 1, 'DOCENTE', NULL, '2026-09-12 05:29:57', 'Asistencia normal'),
(3, 3, 1, 'JUSTIFICADO', 1, 'DOCENTE', NULL, '2026-09-12 05:29:57', 'Presentó certificado médico'),
(6, 6, 4, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-09-21 13:27:28', NULL),
(11, 9, 5, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-09-20 22:58:09', NULL),
(12, 8, 5, 'JUSTIFICADO', 11, 'DOCENTE', NULL, '2026-09-20 22:58:11', NULL),
(13, 7, 5, 'FALTA', 11, 'DOCENTE', NULL, '2026-09-20 22:58:14', NULL),
(14, 7, 6, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-09-21 13:27:10', NULL),
(15, 8, 6, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-09-21 13:27:13', NULL),
(16, 9, 6, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-09-21 13:27:15', NULL),
(18, 10, 9, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:37:55', NULL),
(19, 15, 9, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:37:57', NULL),
(20, 12, 9, 'JUSTIFICADO', 10, 'DOCENTE', NULL, '2026-09-22 12:37:59', NULL),
(21, 13, 9, 'JUSTIFICADO', 10, 'DOCENTE', NULL, '2026-09-22 12:38:02', NULL),
(22, 14, 9, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:38:07', NULL),
(23, 14, 8, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:38:08', NULL),
(24, 13, 8, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:38:11', NULL),
(25, 12, 8, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:38:13', NULL),
(26, 15, 8, 'FALTA', 10, 'DOCENTE', NULL, '2026-09-22 12:38:16', NULL),
(27, 10, 8, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 12:38:18', NULL),
(28, 10, 10, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 14:14:40', NULL),
(29, 12, 10, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 14:14:46', NULL),
(30, 13, 10, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 14:15:16', NULL),
(31, 15, 10, 'FALTA', 10, 'DOCENTE', NULL, '2026-09-22 14:14:52', NULL),
(32, 14, 10, 'PRESENTE', 10, 'DOCENTE', NULL, '2026-09-22 14:15:13', NULL),
(35, 16, 11, 'PRESENTE', 8, 'PARTICIPANTE', '2026-09-23 09:16:18', '2026-09-23 13:16:18', NULL),
(36, 17, 11, 'PRESENTE', 8, 'PARTICIPANTE', '2026-09-23 09:17:30', '2026-09-23 13:17:30', NULL),
(37, 17, 12, 'PRESENTE', 13, 'PARTICIPANTE', '2026-09-23 09:17:53', '2026-09-23 13:17:53', NULL),
(38, 17, 13, 'FALTA', 10, 'DOCENTE', '2026-09-23 09:18:19', '2026-09-23 13:19:17', NULL),
(40, 31, 17, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:09', NULL),
(41, 31, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:10', NULL),
(42, 31, 19, 'JUSTIFICADO', 11, 'DOCENTE', NULL, '2026-10-01 13:54:11', NULL),
(43, 32, 17, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:24', NULL),
(44, 32, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:25', NULL),
(45, 32, 19, 'FALTA', 11, 'DOCENTE', NULL, '2026-10-01 13:54:26', NULL),
(46, 33, 17, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:32', NULL),
(47, 33, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:33', NULL),
(48, 33, 19, 'JUSTIFICADO', 11, 'DOCENTE', NULL, '2026-10-01 13:54:35', NULL),
(49, 34, 17, 'FALTA', 11, 'DOCENTE', NULL, '2026-10-01 13:54:43', NULL),
(50, 34, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:54:45', NULL),
(51, 34, 19, 'FALTA', 11, 'DOCENTE', NULL, '2026-10-01 13:54:46', NULL),
(52, 35, 17, 'JUSTIFICADO', 11, 'DOCENTE', NULL, '2026-10-01 13:55:01', NULL),
(53, 35, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:55:02', NULL),
(54, 35, 19, 'FALTA', 11, 'DOCENTE', NULL, '2026-10-01 13:55:14', NULL),
(55, 36, 17, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:55:21', NULL),
(56, 36, 18, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:55:22', NULL),
(57, 36, 19, 'PRESENTE', 11, 'DOCENTE', NULL, '2026-10-01 13:55:24', NULL);

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
(29, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta elena.rios@eventos.edu (ID #2) establecida en estado activo = 1', '2026-09-21 03:44:29'),
(30, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #4', '2026-09-21 13:28:01'),
(31, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-AF49-D9AE emitido para la inscripción #4', '2026-09-21 13:28:13'),
(32, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #4', '2026-09-21 13:36:33'),
(33, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #5', '2026-09-21 13:37:18'),
(34, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #5', '2026-09-21 13:37:47'),
(35, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-08CD-F53E emitido para la inscripción #6', '2026-09-21 13:37:52'),
(36, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #5', '2026-09-21 13:47:34'),
(37, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-08CD-F53E regenerado con el diseño actual.', '2026-09-21 13:47:38'),
(38, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #5', '2026-09-21 13:54:56'),
(39, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #5', '2026-09-21 13:55:16'),
(40, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-08CD-F53E regenerado con el diseño actual.', '2026-09-21 13:55:19'),
(41, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #5', '2026-09-21 13:55:53'),
(42, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-08CD-F53E regenerado con el diseño actual.', '2026-09-21 13:55:57'),
(43, 9, 'GUARDAR_CATALOGO_EVENTO', 'EVENTOS', '127.0.0.1', 'Categoría guardado: Comunicación e Idiomas', '2026-09-22 12:13:34'),
(44, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #6 con código EVT-2026-ING01', '2026-09-22 12:19:03'),
(45, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #6 con código EVT-2026-ING01', '2026-09-22 12:20:02'),
(46, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #6 con código EVT-2026-ING01', '2026-09-22 12:21:08'),
(47, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Rigoberto Bolaños al evento #6 como Docente encargado', '2026-09-22 12:21:56'),
(48, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #8 en el evento #6 (Inscripción #8)', '2026-09-22 12:22:27'),
(49, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #10 para el evento #6', '2026-09-22 12:23:22'),
(50, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #11 para el evento #6', '2026-09-22 12:23:43'),
(51, 9, 'ELIMINAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se eliminó la sesión #11 del evento #6', '2026-09-22 12:23:57'),
(52, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #12 para el evento #6', '2026-09-22 12:24:23'),
(53, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #11 en el evento #6 (Inscripción #9)', '2026-09-22 12:26:06'),
(54, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #13 para el evento #6', '2026-09-22 12:27:30'),
(55, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #14 para el evento #6', '2026-09-22 12:27:49'),
(56, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #15 para el evento #6', '2026-09-22 12:28:18'),
(57, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #6 cambió su estado a PUBLICADO', '2026-09-22 12:28:52'),
(58, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #6', '2026-09-22 12:36:09'),
(59, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:36:27'),
(60, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 emitido para la inscripción #8', '2026-09-22 12:38:55'),
(61, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:39:30'),
(62, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:39:36'),
(63, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:40:09'),
(64, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:40:13'),
(65, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:41:02'),
(66, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:41:07'),
(67, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:44:46'),
(68, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:44:49'),
(69, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:45:10'),
(70, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:45:15'),
(71, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:46:13'),
(72, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:46:23'),
(73, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:48:47'),
(74, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:48:51'),
(75, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:49:17'),
(76, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:49:20'),
(77, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:49:44'),
(78, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:49:54'),
(79, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #6', '2026-09-22 12:52:52'),
(80, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:53:19'),
(81, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:53:38'),
(82, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:53:59'),
(83, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:54:03'),
(84, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:54:21'),
(85, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:54:25'),
(86, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:54:40'),
(87, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 12:54:54'),
(88, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-22 12:54:58'),
(89, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 14:18:50'),
(90, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-51E8-1D3D emitido para la inscripción #10', '2026-09-22 14:19:02'),
(91, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D emitido para la inscripción #9', '2026-09-22 14:19:54'),
(92, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #6', '2026-09-22 14:20:28'),
(93, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-22 14:20:51'),
(94, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D regenerado con el diseño actual.', '2026-09-22 14:20:57'),
(95, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #7 con código EVT-TEST-000004', '2026-09-23 13:10:33'),
(96, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Rigoberto Bolaños al evento #7 como Expositor Principal', '2026-09-23 13:10:47'),
(97, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #16 para el evento #7', '2026-09-23 13:11:29'),
(98, 9, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #17 para el evento #7', '2026-09-23 13:12:00'),
(99, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #7 cambió su estado a PUBLICADO', '2026-09-23 13:12:05'),
(100, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #8 en el evento #7 (Inscripción #11)', '2026-09-23 13:12:10'),
(101, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #13 en el evento #7 (Inscripción #12)', '2026-09-23 13:12:30'),
(102, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #12 en el evento #7 (Inscripción #13)', '2026-09-23 13:12:37'),
(103, 10, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #16 abierta durante 15 minutos.', '2026-09-23 13:16:05'),
(104, 10, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #16 cerrada y porcentajes recalculados.', '2026-09-23 13:16:48'),
(105, 10, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #17 abierta durante 30 minutos.', '2026-09-23 13:17:24'),
(106, 10, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #17 cerrada y porcentajes recalculados.', '2026-09-23 13:19:09'),
(107, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #7', '2026-09-23 13:20:49'),
(108, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #7', '2026-09-23 13:21:07'),
(109, 9, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-F354-8962 emitido para la inscripción #11', '2026-09-23 13:21:12'),
(110, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-F354-8962 regenerado con el diseño actual.', '2026-09-23 13:21:15'),
(111, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #7', '2026-09-23 13:21:35'),
(112, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-F354-8962 regenerado con el diseño actual.', '2026-09-23 13:21:39'),
(113, 9, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #8 con código EVT-2026-ING02', '2026-09-23 14:50:22'),
(114, 9, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Rigoberto Bolaños al evento #8 como Docente encargado', '2026-09-23 14:50:33'),
(115, 9, 'CREAR_SERIE_SESIONES', 'SERIES_SESIONES_EVENTO', '127.0.0.1', 'Se creó la serie #1 con 6 sesiones.', '2026-09-23 14:52:34'),
(116, 9, 'CREAR_SERIE_SESIONES', 'SERIES_SESIONES_EVENTO', '127.0.0.1', 'Se creó la serie #2 con 7 sesiones.', '2026-09-23 14:53:56'),
(117, 9, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #8 cambió su estado a PUBLICADO', '2026-09-23 14:54:11'),
(118, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #8 en el evento #8 (Inscripción #14)', '2026-09-23 23:13:46'),
(119, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-23 23:14:23'),
(120, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D regenerado con el diseño actual.', '2026-09-23 23:14:51'),
(121, 9, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla configurada para el evento #6', '2026-09-23 23:15:25'),
(122, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-23 23:15:49'),
(123, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D regenerado con el diseño actual.', '2026-09-23 23:15:53'),
(124, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-23 23:16:57'),
(125, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D regenerado con el diseño actual.', '2026-09-23 23:17:02'),
(126, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 regenerado con el diseño actual.', '2026-09-23 23:17:04'),
(127, 9, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para el evento #6', '2026-09-23 23:17:46'),
(128, 9, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-B4B9-8C0D regenerado con el diseño actual.', '2026-09-23 23:17:56'),
(129, 9, 'ELIMINAR_CATALOGO_EVENTO', 'EVENTOS', '127.0.0.1', 'Área: Educación e Innovación Pedagógica', '2026-09-25 13:28:37'),
(130, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #13 en el evento #8 (Inscripción #15)', '2026-09-25 13:29:26'),
(131, 9, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #4 en el evento #8 (Inscripción #16)', '2026-09-25 13:34:07'),
(132, 9, 'ACTUALIZAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se actualizó el evento ID #8 con código EVT-2026-ING02', '2026-09-25 13:34:57'),
(133, 9, 'GUARDAR_CATALOGO_EVENTO', 'EVENTOS', '127.0.0.1', 'Tipo de evento guardado: Workshop', '2026-09-25 14:46:05'),
(134, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta maria.lopez@estudiante.edu (ID #5) establecida en estado activo = 0', '2026-09-25 14:46:19'),
(135, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta marcos.torres@eventos.edu (ID #3) establecida en estado activo = 0', '2026-09-25 14:46:40'),
(136, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta marcos.torres@eventos.edu (ID #3) establecida en estado activo = 1', '2026-09-25 14:46:41'),
(137, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta maria.lopez@estudiante.edu (ID #5) establecida en estado activo = 1', '2026-09-25 14:46:42'),
(138, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta abc@gmail.com (ID #8) establecida en estado activo = 0', '2026-09-25 14:48:57'),
(139, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta administrador@gmail.com (ID #6) establecida en estado activo = 0', '2026-09-25 14:49:15'),
(140, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta abc@gmail.com (ID #8) establecida en estado activo = 1', '2026-09-25 14:49:19'),
(141, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta administrador@gmail.com (ID #6) establecida en estado activo = 1', '2026-09-25 14:49:22'),
(142, 9, 'CAMBIAR_ROL', 'USUARIOS', '127.0.0.1', 'Se asignó el rol ID #1 al usuario ID #1', '2026-09-25 14:49:41'),
(143, 9, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta abc@gmail.com (ID #8) establecida en estado activo = 0', '2026-09-27 21:28:57'),
(144, 9, 'ACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta abc@gmail.com (ID #8) establecida en estado activo = 1', '2026-09-27 21:29:03'),
(145, 9, 'GENERAR_ENLACE_RECUPERACION', 'USUARIOS', '::1', 'Se generó un enlace temporal de recuperación para la cuenta ID #1', '2026-09-27 22:26:20'),
(146, 1, 'DESACTIVAR_CUENTA', 'USUARIOS', '127.0.0.1', 'Cuenta 123@gmail.com (ID #9) establecida en estado activo = 0', '2026-09-27 22:27:48'),
(147, 1, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla #9 configurada para el evento #8', '2026-09-28 03:56:07'),
(148, 1, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla #10 configurada para el evento #8', '2026-09-28 03:56:42'),
(149, 1, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para la plantilla #10 del evento #8', '2026-09-28 03:57:08'),
(150, 1, 'EMITIR_CERTIFICADO_EXPOSITOR_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-61E6-00E9 emitido para el expositor #10', '2026-09-28 03:57:13'),
(151, 1, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para la plantilla #10 del evento #8', '2026-09-28 03:57:33'),
(152, 1, 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-61E6-00E9 regenerado con el diseño actual.', '2026-09-28 03:57:37'),
(153, 1, 'ANULAR_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-C886-3148 anulado. Motivo: Documento emitido por error', '2026-09-29 02:48:22'),
(154, 1, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para la plantilla #4 del evento #6', '2026-09-29 03:03:58'),
(155, 1, 'ANULAR_CERTIFICADO', 'CERTIFICADOS', '::1', 'Certificado CERT-2026-AF49-D9AE anulado. Motivo: Error de impresion', '2026-09-29 03:21:44'),
(156, 1, 'ARCHIVAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '::1', 'Plantilla #1 archivada del evento #4', '2026-09-29 03:22:29'),
(157, 1, 'CREAR_EVENTO', 'EVENTOS', '127.0.0.1', 'Se creó el evento ID #9 con código EVT-2026-ING03', '2026-09-29 12:15:49'),
(158, 1, 'CREAR_SERIE_SESIONES', 'SERIES_SESIONES_EVENTO', '127.0.0.1', 'Se creó la serie #3 con 6 sesiones.', '2026-09-29 12:17:04'),
(159, 1, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Eduardo Garcia Salinaz al evento #9 como Docente encargado', '2026-09-29 12:19:55'),
(160, 1, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Rigoberto Bolaños al evento #9 como Docente Auxiliar', '2026-09-29 12:20:15'),
(161, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #8 en el evento #9 (Inscripción #17)', '2026-09-29 12:20:22'),
(162, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #13 en el evento #9 (Inscripción #18)', '2026-09-29 12:20:27'),
(163, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #12 en el evento #9 (Inscripción #19)', '2026-09-29 12:20:31'),
(164, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #9 cambió su estado a PUBLICADO', '2026-09-29 12:20:40'),
(165, 1, 'GUARDAR_CATALOGO_EVENTO', 'EVENTOS', '::1', 'Tipo de evento guardado: Feria', '2026-09-29 12:50:50'),
(166, 1, 'CREAR_EVENTO', 'EVENTOS', '::1', 'Se creó el evento ID #10 con código EVT-FR-CIS01', '2026-09-29 12:52:05'),
(167, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '::1', 'El evento ID #10 cambió su estado a PUBLICADO', '2026-09-29 12:52:07'),
(168, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '::1', 'El evento ID #10 cambió su estado a BORRADOR', '2026-09-29 12:52:08'),
(169, 1, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '::1', 'Se registró la sesión #37 para el evento #10', '2026-09-29 12:52:26'),
(170, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '::1', 'Inscripción manual forzada para el usuario ID #8 en el evento #10 (Inscripción #20)', '2026-09-29 12:52:40'),
(171, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '::1', 'El evento ID #10 cambió su estado a PUBLICADO', '2026-09-29 12:52:56'),
(172, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #31 abierta durante 30 minutos.', '2026-09-29 12:58:20'),
(173, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '127.0.0.1', 'Sesión #31 cerrada y porcentajes recalculados.', '2026-09-29 12:58:26'),
(174, 1, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla #11 configurada para el evento #10', '2026-09-29 15:57:56'),
(175, 1, 'RESOLVER_REIMPRESION', 'CERTIFICADOS', '::1', 'Solicitud #2 resuelta como RECHAZADA.', '2026-09-30 14:46:03'),
(176, 1, 'RESOLVER_REIMPRESION', 'CERTIFICADOS', '::1', 'Solicitud #3 resuelta como APROBADA.', '2026-09-30 14:52:03'),
(177, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #6 cambió su estado a BORRADOR', '2026-10-01 12:50:09'),
(178, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #6 cambió su estado a PUBLICADO', '2026-10-01 12:50:11'),
(179, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #5 cambió su estado a BORRADOR', '2026-10-01 13:15:19'),
(180, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #4 cambió su estado a BORRADOR', '2026-10-01 13:15:21'),
(181, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #3 cambió su estado a BORRADOR', '2026-10-01 13:15:23'),
(182, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #7 cambió su estado a BORRADOR', '2026-10-01 13:15:27'),
(183, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #12 en el evento #10 (Inscripción #21)', '2026-10-01 13:52:59'),
(184, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #2 en el evento #10 (Inscripción #22)', '2026-10-01 13:53:13'),
(185, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #6 en el evento #10 (Inscripción #23)', '2026-10-01 13:53:21'),
(186, 1, 'INSCRIPCION_MANUAL', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual forzada para el usuario ID #3 en el evento #10 (Inscripción #24)', '2026-10-01 13:53:28'),
(187, 1, 'ASIGNAR_EXPOSITOR_EVENTO', 'EVENTO_EXPOSITORES', '127.0.0.1', 'Se asignó a Eduardo Garcia Salinaz al evento #10 como Expositor Principal', '2026-10-01 13:53:57'),
(188, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #32 abierta durante 30 minutos.', '2026-10-01 13:54:18'),
(189, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #32 cerrada y porcentajes recalculados.', '2026-10-01 13:54:22'),
(190, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #33 abierta durante 30 minutos.', '2026-10-01 13:54:29'),
(191, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #33 cerrada y porcentajes recalculados.', '2026-10-01 13:54:30'),
(192, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #34 abierta durante 30 minutos.', '2026-10-01 13:54:40'),
(193, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #34 cerrada y porcentajes recalculados.', '2026-10-01 13:54:42'),
(194, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #35 abierta durante 30 minutos.', '2026-10-01 13:54:51'),
(195, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #35 cerrada y porcentajes recalculados.', '2026-10-01 13:54:53'),
(196, 11, 'ABRIR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #36 abierta durante 30 minutos.', '2026-10-01 13:55:18'),
(197, 11, 'CERRAR_ASISTENCIA', 'ASISTENCIAS', '::1', 'Sesión #36 cerrada y porcentajes recalculados.', '2026-10-01 13:55:20'),
(198, 1, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #38 para el evento #9', '2026-10-01 13:57:17'),
(199, 1, 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Plantilla #12 configurada para el evento #9', '2026-10-01 14:03:24'),
(200, 1, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para la plantilla #12 del evento #9', '2026-10-01 14:04:39'),
(201, 1, 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', '127.0.0.1', 'Posiciones actualizadas para la plantilla #12 del evento #9', '2026-10-01 14:28:36'),
(202, 1, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', '127.0.0.1', 'Certificado CERT-2026-024A-1424 emitido para la inscripción #17', '2026-10-01 14:28:45'),
(203, 1, 'RECHAZAR_SOLICITUD_RECUPERACION', 'USUARIOS', '127.0.0.1', 'Solicitud de recuperación #1 rechazada. Motivo: No mostro sus documentos', '2026-10-04 14:58:58'),
(204, 1, 'RESOLVER_REIMPRESION', 'CERTIFICADOS', '127.0.0.1', 'Solicitud #4 resuelta como APROBADA.', '2026-10-05 14:29:03'),
(205, 1, 'ENTREGA_REIMPRESION', 'CERTIFICADOS', '127.0.0.1', 'Se registró la entrega material de la reimpresión #4.', '2026-10-05 14:29:41'),
(206, 1, 'RESOLVER_REIMPRESION', 'CERTIFICADOS', '::1', 'Solicitud #5 resuelta como RECHAZADA.', '2026-10-06 12:24:14'),
(207, 1, 'CREAR_EVENTO', 'EVENTOS', '::1', 'Se creó el evento ID #11 con código EVT-CAP-ECO-000011', '2026-10-06 12:53:21'),
(208, 1, 'APROBAR_SOLICITUD_RECUPERACION', 'USUARIOS', '127.0.0.1', 'Solicitud de recuperación #2 aprobada para la cuenta #8', '2026-10-06 15:09:34'),
(209, 1, 'GENERAR_ENLACE_RECUPERACION', 'USUARIOS', '127.0.0.1', 'Se generó un enlace temporal de recuperación para la cuenta ID #13', '2026-10-07 21:44:41'),
(210, 1, 'INSCRIPCION_MANUAL_LOTE', 'INSCRIPCIONES', '127.0.0.1', 'Inscripción manual de 13 participante(s) en el evento #14 (inscripciones: 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37)', '2026-10-07 23:37:40'),
(211, 1, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #39 para el evento #14', '2026-10-07 23:51:29'),
(212, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #14 cambió su estado a PUBLICADO', '2026-10-07 23:51:44'),
(213, 1, 'ENTREGA_REIMPRESION', 'CERTIFICADOS', '127.0.0.1', 'Se registró la entrega material de la reimpresión #3.', '2026-10-07 23:55:23'),
(214, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #18 cambió su estado a PUBLICADO', '2026-10-08 00:13:23'),
(215, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #14 cambió su estado a BORRADOR', '2026-10-08 00:14:26'),
(216, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #11 cambió su estado a PUBLICADO', '2026-10-08 00:14:48'),
(217, 1, 'CREAR_SESION_EVENTO', 'SESIONES_EVENTO', '127.0.0.1', 'Se registró la sesión #40 para el evento #14', '2026-10-08 00:53:44'),
(218, 1, 'CAMBIO_ESTADO_EVENTO', 'EVENTOS', '127.0.0.1', 'El evento ID #14 cambió su estado a PUBLICADO', '2026-10-08 00:53:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(8) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `abreviatura`, `activo`) VALUES
(1, 'Tecnología e Informática', 'TEC', 1),
(2, 'Ciencias de la Salud', 'SAL', 1),
(3, 'Ciencias Económicas y Empresariales', 'ECO', 1),
(4, 'Derecho y Ciencias Jurídicas', 'DER', 1),
(6, 'Comunicación e Idiomas', 'COM', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `id_certificado` bigint(20) UNSIGNED NOT NULL,
  `codigo_unico` varchar(50) NOT NULL,
  `id_inscripcion` bigint(20) UNSIGNED DEFAULT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_plantilla` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `certificados` (`id_certificado`, `codigo_unico`, `id_inscripcion`, `id_evento`, `id_plantilla`, `id_usuario`, `tipo_participacion`, `fecha_emision`, `ruta_archivo_pdf`, `codigo_qr`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `id_usuario_anulacion`) VALUES
(1, 'CERT-2026-F2C5-B0B1', 1, 1, NULL, 4, 'PARTICIPANTE', '2026-09-12 05:31:52', NULL, NULL, 'EMITIDO', NULL, NULL, NULL),
(2, 'CERT-2026-AF49-D9AE', 4, 4, NULL, 8, 'PARTICIPANTE', '2026-09-21 13:28:09', 'CERT-2026-AF49-D9AE.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-AF49-D9AE', 'ANULADO', 'Error de impresion', '2026-09-28 23:21:44', 1),
(3, 'CERT-2026-08CD-F53E', 6, 5, NULL, 12, 'PARTICIPANTE', '2026-09-21 13:37:49', 'CERT-2026-08CD-F53E.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-08CD-F53E', 'EMITIDO', NULL, NULL, NULL),
(4, 'CERT-2026-C886-3148', 8, 6, NULL, 8, 'PARTICIPANTE', '2026-09-22 12:38:52', 'CERT-2026-C886-3148.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-C886-3148', 'ANULADO', 'Documento emitido por error', '2026-09-28 22:48:22', 1),
(5, 'CERT-2026-51E8-1D3D', 10, 6, NULL, 13, 'PARTICIPANTE', '2026-09-22 14:19:00', 'CERT-2026-51E8-1D3D.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-51E8-1D3D', 'EMITIDO', NULL, NULL, NULL),
(6, 'CERT-2026-B4B9-8C0D', 9, 6, NULL, 11, 'PARTICIPANTE', '2026-09-22 14:19:52', 'CERT-2026-B4B9-8C0D.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-B4B9-8C0D', 'EMITIDO', NULL, NULL, NULL),
(7, 'CERT-2026-F354-8962', 11, 7, NULL, 8, 'PARTICIPANTE', '2026-09-23 13:21:09', 'CERT-2026-F354-8962.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-F354-8962', 'EMITIDO', NULL, NULL, NULL),
(8, 'CERT-2026-61E6-00E9', NULL, 8, 10, 10, 'EXPOSITOR', '2026-09-28 03:57:11', 'CERT-2026-61E6-00E9.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-61E6-00E9', 'EMITIDO', NULL, NULL, NULL),
(9, 'CERT-2026-024A-1424', 17, 9, 12, 8, 'PARTICIPANTE', '2026-10-01 14:28:43', 'CERT-2026-024A-1424.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-024A-1424', 'EMITIDO', NULL, NULL, NULL);

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
(3, 'EVT-TEST-000001', 'Testeo de taller', 'jsdjfoisdjfosdjdfoisdjfoijs', 2, 4, 'PRESENCIAL', 'Auditorio central', 'https://meet/google.com', 25, '2026-09-30 15:00:00', '2026-10-08 15:00:00', '2026-10-12', '2026-10-12', 0, 60, 80.00, 0.00, 'BORRADOR', 9, '2026-09-19 23:00:19', '2026-10-01 13:15:23'),
(4, 'EVT-TEST-000002', 'test archivo', 'Testeo', 2, 3, 'VIRTUAL', 'Auditorio', 'https://meet/google.com', 60, '2026-09-20 15:00:00', '2026-09-21 00:00:00', '2026-09-21', '2026-09-21', 1, 20, 80.00, 0.00, 'BORRADOR', 9, '2026-09-20 02:04:53', '2026-10-01 13:15:21'),
(5, 'EVT-TEST-000003', 'Test sesiones', 'Texto descripcion', 1, 4, 'PRESENCIAL', 'Curso 5-A', NULL, 40, '2026-09-20 17:00:00', '2026-09-21 10:00:00', '2026-09-21', '2026-09-30', 1, 70, 80.00, 0.00, 'BORRADOR', 9, '2026-09-20 22:54:13', '2026-10-01 13:15:19'),
(6, 'EVT-2026-ING01', 'Curso de Ingles Basico - Intermedio', 'Clase de capacitación para Ingles Basico e Intermedio', 1, 6, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 80, '2026-09-14 10:00:00', '2026-09-22 17:00:00', '2026-09-23', '2026-09-28', 1, 80, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-22 12:19:03', '2026-10-01 12:50:11'),
(7, 'EVT-TEST-000004', 'Testeo de asistencia', 'test asisencia', 1, 1, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 30, '2026-09-22 09:00:00', '2026-09-23 12:00:00', '2026-09-23', '2026-09-25', 1, 20, 80.00, 0.00, 'BORRADOR', 9, '2026-09-23 13:10:33', '2026-10-01 13:15:27'),
(8, 'EVT-2026-ING02', 'Curso de Ingles Intermedio - Avanzado', 'Clases de Ingles de nivel intermedioi', 1, 6, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 50, '2026-09-22 09:00:00', '2026-09-28 12:00:00', '2026-09-23', '2026-10-09', 1, 100, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-23 14:50:22', '2026-09-25 13:34:57'),
(9, 'EVT-2026-ING03', 'Curso de Ingles Especializado', 'Cursos especializado de Ingles', 1, 6, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 50, '2026-09-29 08:00:00', '2026-10-02 12:00:00', '2026-09-29', '2026-10-09', 1, 100, 80.00, 0.00, 'PUBLICADO', 1, '2026-09-29 12:15:49', '2026-09-29 12:20:40'),
(10, 'EVT-FR-CIS01', 'Feria Ciencias', 'Test', 9, 3, 'PRESENCIAL', 'Campo Ferial', NULL, 50, '2026-09-29 09:00:00', '2026-09-29 09:00:00', '2026-09-29', '2026-09-30', 1, 20, 80.00, 0.00, 'PUBLICADO', 1, '2026-09-29 12:52:05', '2026-09-29 12:52:56'),
(11, 'EVT-CAP-ECO-000011', 'Test Codigo', 'Test', 5, 3, 'PRESENCIAL', 'Auditorio Central', NULL, 50, '2026-10-06 10:00:00', '2026-10-08 12:00:00', '2026-10-09', '2026-10-09', 0, 20, 80.00, 0.00, 'PUBLICADO', 1, '2026-10-06 12:53:21', '2026-10-08 00:14:48'),
(12, 'EVT-CUR-TEC-000012', 'Introducción al Desarrollo Web con Django', 'Curso práctico orientado al aprendizaje de los fundamentos del desarrollo de aplicaciones web utilizando Python y Django.', 1, 1, 'PRESENCIAL', 'Laboratorio de Informática 1', NULL, 40, '2026-10-07 08:00:00', '2026-10-18 18:00:00', '2026-10-20', '2026-10-24', 1, 20, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(13, 'EVT-CONF-TEC-000013', 'Inteligencia Artificial Aplicada a la Educación', 'Conferencia sobre las aplicaciones actuales de la inteligencia artificial y su impacto en los procesos educativos.', 3, 1, 'PRESENCIAL', 'Auditorio Central', NULL, 120, '2026-10-08 08:00:00', '2026-10-22 18:00:00', '2026-10-24', '2026-10-24', 1, 4, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(14, 'EVT-TAL-SAL-000014', 'Primeros Auxilios y Atención de Emergencias', 'Taller práctico para desarrollar conocimientos básicos de primeros auxilios y respuesta ante situaciones de emergencia.', 2, 2, 'PRESENCIAL', 'Sala de Capacitación', NULL, 35, '2026-10-10 08:00:00', '2026-10-25 18:00:00', '2026-10-27', '2026-10-28', 1, 8, 90.00, 0.00, 'PUBLICADO', 1, '2026-10-07 21:50:58', '2026-10-08 00:53:53'),
(15, 'EVT-WEB-ECO-000015', 'Marketing Digital para Emprendedores', 'Webinar dirigido a estudiantes y emprendedores sobre estrategias de promoción y posicionamiento en medios digitales.', 6, 3, 'VIRTUAL', NULL, 'https://meet.google.com/marketing-demo', 0, '2026-10-10 08:00:00', '2026-10-28 18:00:00', '2026-10-30', '2026-10-30', 1, 3, 75.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(16, 'EVT-CUR-COM-000016', 'Comunicación Efectiva y Oratoria', 'Curso destinado al fortalecimiento de las habilidades de comunicación, expresión oral y presentación ante públicos.', 1, 6, 'PRESENCIAL', 'Aula Magna', NULL, 45, '2026-10-12 08:00:00', '2026-10-29 18:00:00', '2026-11-02', '2026-11-06', 1, 16, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(17, 'EVT-SEM-DER-000017', 'Actualización en Derecho Laboral Boliviano', 'Seminario de actualización sobre normativa, derechos y obligaciones relacionados con el ámbito laboral boliviano.', 4, 4, 'PRESENCIAL', 'Auditorio Jurídico', NULL, 80, '2026-10-15 08:00:00', '2026-11-02 18:00:00', '2026-11-05', '2026-11-05', 1, 6, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(18, 'EVT-CAP-TEC-000018', 'Ciberseguridad y Protección de Datos', 'Capacitación especializada en seguridad informática, prevención de amenazas y protección de información digital.', 5, 1, 'PRESENCIAL', 'Laboratorio de Informática 2', NULL, 30, '2026-10-16 08:00:00', '2026-11-04 18:00:00', '2026-11-07', '2026-11-08', 1, 12, 85.00, 0.00, 'PUBLICADO', 1, '2026-10-07 21:50:58', '2026-10-08 00:13:23'),
(19, 'EVT-TAL-ECO-000019', 'Gestión Financiera para Pequeñas Empresas', 'Taller práctico para mejorar la administración de ingresos, egresos, costos y planificación financiera empresarial.', 2, 3, 'PRESENCIAL', 'Sala Empresarial', NULL, 40, '2026-10-18 08:00:00', '2026-11-07 18:00:00', '2026-11-10', '2026-11-11', 1, 8, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(20, 'EVT-CONF-SAL-000020', 'Salud Mental y Bienestar Universitario', 'Espacio de información y reflexión sobre salud mental, manejo del estrés y bienestar dentro del ámbito universitario.', 3, 2, 'PRESENCIAL', 'Auditorio Central', NULL, 150, '2026-10-20 08:00:00', '2026-11-10 18:00:00', '2026-11-12', '2026-11-12', 1, 4, 75.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(21, 'EVT-WKS-TEC-000021', 'Desarrollo de Aplicaciones con Python', 'Workshop práctico para introducir a los participantes en la programación y desarrollo de aplicaciones con Python.', 8, 1, 'PRESENCIAL', 'Laboratorio de Informática 1', NULL, 35, '2026-10-22 08:00:00', '2026-11-12 18:00:00', '2026-11-14', '2026-11-15', 1, 10, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(22, 'EVT-CUR-COM-000022', 'Inglés Comunicativo para Profesionales', 'Curso orientado al desarrollo de habilidades básicas de comunicación oral y escrita en inglés profesional.', 1, 6, 'PRESENCIAL', 'Aula de Idiomas 2', NULL, 30, '2026-10-24 08:00:00', '2026-11-14 18:00:00', '2026-11-16', '2026-11-27', 1, 24, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(23, 'EVT-SEM-DER-000023', 'Derechos Humanos y Ciudadanía', 'Seminario destinado al análisis de los derechos fundamentales, responsabilidades ciudadanas y convivencia democrática.', 4, 4, 'PRESENCIAL', 'Auditorio Jurídico', NULL, 70, '2026-10-25 08:00:00', '2026-11-15 18:00:00', '2026-11-18', '2026-11-18', 1, 6, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(24, 'EVT-FER-ECO-000024', 'Feria Universitaria de Innovación y Emprendimiento', 'Feria de exposición de proyectos, productos e iniciativas innovadoras desarrolladas por estudiantes y emprendedores.', 9, 3, 'PRESENCIAL', 'Coliseo Universitario', NULL, 0, '2026-10-27 08:00:00', '2026-11-17 18:00:00', '2026-11-20', '2026-11-21', 1, 12, 70.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(25, 'EVT-CAP-TEC-000025', 'Gestión de Bases de Datos con PostgreSQL', 'Capacitación práctica sobre diseño, creación, administración y consulta de bases de datos PostgreSQL.', 5, 1, 'PRESENCIAL', 'Laboratorio de Informática 2', NULL, 30, '2026-10-29 08:00:00', '2026-11-19 18:00:00', '2026-11-23', '2026-11-26', 1, 16, 85.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(26, 'EVT-WEB-SAL-000026', 'Prevención de Enfermedades Transmisibles', 'Webinar informativo sobre medidas de prevención, factores de riesgo y buenas prácticas frente a enfermedades transmisibles.', 6, 2, 'VIRTUAL', NULL, 'https://meet.google.com/salud-demo', 0, '2026-11-01 08:00:00', '2026-11-22 18:00:00', '2026-11-24', '2026-11-24', 1, 3, 75.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(27, 'EVT-TAL-COM-000027', 'Técnicas de Redacción Académica', 'Taller para mejorar la escritura académica, organización de ideas y elaboración correcta de documentos universitarios.', 2, 6, 'PRESENCIAL', 'Sala de Conferencias', NULL, 40, '2026-11-03 08:00:00', '2026-11-23 18:00:00', '2026-11-26', '2026-11-27', 1, 8, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(28, 'EVT-CUR-DER-000028', 'Introducción al Derecho Constitucional', 'Curso introductorio sobre principios constitucionales, organización del Estado y derechos establecidos en la Constitución.', 1, 4, 'PRESENCIAL', 'Aula Jurídica 1', NULL, 50, '2026-11-05 08:00:00', '2026-11-26 18:00:00', '2026-11-30', '2026-12-04', 1, 20, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(29, 'EVT-CONF-ECO-000029', 'Transformación Digital en las Organizaciones', 'Conferencia sobre el impacto de las nuevas tecnologías en los procesos administrativos y empresariales.', 3, 3, 'VIRTUAL', NULL, 'https://meet.google.com/empresa-demo', 0, '2026-11-07 08:00:00', '2026-11-29 18:00:00', '2026-12-01', '2026-12-01', 1, 4, 75.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(30, 'EVT-WKS-TEC-000030', 'Desarrollo de APIs REST con Django', 'Workshop práctico para diseñar y desarrollar servicios web mediante APIs REST utilizando Django.', 8, 1, 'PRESENCIAL', 'Laboratorio de Informática 1', NULL, 30, '2026-11-10 08:00:00', '2026-12-01 18:00:00', '2026-12-03', '2026-12-05', 1, 12, 85.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(31, 'EVT-TAL-SAL-000031', 'Nutrición y Hábitos de Vida Saludable', 'Taller sobre alimentación equilibrada, prevención y adopción de hábitos saludables en la vida cotidiana.', 2, 2, 'PRESENCIAL', 'Sala de Capacitación', NULL, 45, '2026-11-12 08:00:00', '2026-12-03 18:00:00', '2026-12-07', '2026-12-07', 1, 6, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(32, 'EVT-SEM-COM-000032', 'Comunicación Digital y Redes Sociales', 'Seminario sobre herramientas, estrategias y buenas prácticas para la comunicación institucional mediante medios digitales.', 4, 6, 'VIRTUAL', NULL, 'https://meet.google.com/comunicacion-demo', 0, '2026-11-15 08:00:00', '2026-12-05 18:00:00', '2026-12-09', '2026-12-09', 1, 5, 75.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(33, 'EVT-CAP-DER-000033', 'Mediación y Resolución de Conflictos', 'Capacitación sobre técnicas de negociación, mediación y solución pacífica de controversias.', 5, 4, 'PRESENCIAL', 'Auditorio Jurídico', NULL, 35, '2026-11-17 08:00:00', '2026-12-08 18:00:00', '2026-12-10', '2026-12-11', 1, 12, 85.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(34, 'EVT-DIP-ECO-000034', 'Gestión Estratégica de Proyectos', 'Programa de formación avanzada en planificación, ejecución, seguimiento y evaluación de proyectos.', 7, 3, 'VIRTUAL', NULL, 'https://meet.google.com/proyectos-demo', 60, '2026-11-20 08:00:00', '2026-12-15 18:00:00', '2027-01-11', '2027-03-12', 1, 120, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(35, 'EVT-DIP-TEC-000035', 'Diplomado Internacional en Tecnologías Web', 'Diplomado orientado al desarrollo moderno de aplicaciones web, bases de datos, seguridad y despliegue de sistemas.', 7, 1, 'VIRTUAL', NULL, 'https://meet.google.com/web-demo', 50, '2026-11-23 08:00:00', '2026-12-18 18:00:00', '2027-01-18', '2027-03-26', 1, 140, 80.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(36, 'EVT-FER-TEC-000036', 'Feria de Ciencia, Tecnología e Innovación', 'Feria destinada a la presentación y difusión de proyectos científicos y tecnológicos desarrollados por estudiantes.', 9, 1, 'PRESENCIAL', 'Coliseo Universitario', NULL, 0, '2026-12-01 08:00:00', '2027-01-15 18:00:00', '2027-01-22', '2027-01-23', 1, 16, 70.00, 0.00, 'BORRADOR', 1, '2026-10-07 21:50:58', '2026-10-07 21:50:58');

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
(5, 5, 11, 'Docente', '2026-09-20 22:54:30'),
(6, 6, 10, 'Docente encargado', '2026-09-22 12:21:56'),
(7, 7, 10, 'Expositor Principal', '2026-09-23 13:10:47'),
(8, 8, 10, 'Docente encargado', '2026-09-23 14:50:33'),
(9, 9, 11, 'Docente encargado', '2026-09-29 12:19:55'),
(10, 9, 10, 'Docente Auxiliar', '2026-09-29 12:20:15'),
(11, 10, 11, 'Expositor Principal', '2026-10-01 13:53:57');

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
(4, 'INS-2026-A8990108', 4, 8, 'INSCRITO', 'WEB_PARTICIPANTE', 100.00, NULL, 1, NULL, '2026-09-20 02:05:57'),
(5, 'INS-2026-5FFBDAB9', 5, 8, 'INSCRITO', 'ADMINISTRATIVO', 66.67, NULL, 0, NULL, '2026-09-20 22:56:50'),
(6, 'INS-2026-12ECE48B', 5, 12, 'INSCRITO', 'WEB_PARTICIPANTE', 100.00, NULL, 1, NULL, '2026-09-21 03:29:56'),
(7, 'INS-2026-08870521', 2, 12, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-09-21 03:30:03'),
(8, 'INS-2026-E516E1DF', 6, 8, 'INSCRITO', 'ADMINISTRATIVO', 80.00, NULL, 1, NULL, '2026-09-22 12:22:27'),
(9, 'INS-2026-8ED8CAA7', 6, 11, 'INSCRITO', 'ADMINISTRATIVO', 100.00, NULL, 1, NULL, '2026-09-22 12:26:06'),
(10, 'INS-2026-BBADEB44', 6, 13, 'INSCRITO', 'WEB_PARTICIPANTE', 80.00, NULL, 1, NULL, '2026-09-22 14:11:07'),
(11, 'INS-2026-69DFBDB1', 7, 8, 'INSCRITO', 'ADMINISTRATIVO', 100.00, NULL, 1, NULL, '2026-09-23 13:12:10'),
(12, 'INS-2026-67EA6D5A', 7, 13, 'INSCRITO', 'ADMINISTRATIVO', 50.00, NULL, 0, NULL, '2026-09-23 13:12:30'),
(13, 'INS-2026-A67945D5', 7, 12, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-23 13:12:37'),
(14, 'INS-2026-B4BB8AAA', 8, 8, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-23 23:13:46'),
(15, 'INS-2026-70598ADF', 8, 13, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-25 13:29:26'),
(16, 'INS-2026-503989C7', 8, 4, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-25 13:34:07'),
(17, 'INS-2026-CBB25A1C', 9, 8, 'INSCRITO', 'ADMINISTRATIVO', 83.33, NULL, 1, NULL, '2026-09-29 12:20:22'),
(18, 'INS-2026-583644C7', 9, 13, 'INSCRITO', 'ADMINISTRATIVO', 100.00, NULL, 1, NULL, '2026-09-29 12:20:27'),
(19, 'INS-2026-DD89080F', 9, 12, 'INSCRITO', 'ADMINISTRATIVO', 50.00, NULL, 0, NULL, '2026-09-29 12:20:31'),
(20, 'INS-2026-F196BCE0', 10, 8, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-29 12:52:40'),
(21, 'INS-2026-15232397', 10, 12, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-01 13:52:59'),
(22, 'INS-2026-190D5214', 10, 2, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-01 13:53:13'),
(23, 'INS-2026-0A0CE901', 10, 6, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-01 13:53:21'),
(24, 'INS-2026-29C6257A', 10, 3, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-01 13:53:28'),
(25, 'INS-2026-9A6CD623', 14, 8, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(26, 'INS-2026-7A9A5AA1', 14, 21, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(27, 'INS-2026-0DB5FAD9', 14, 53, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(28, 'INS-2026-8DCA5855', 14, 62, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(29, 'INS-2026-EB5244AD', 14, 47, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(30, 'INS-2026-571A8F74', 14, 15, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(31, 'INS-2026-10C2DEE5', 14, 61, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(32, 'INS-2026-10FB7995', 14, 6, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(33, 'INS-2026-A86A0E5D', 14, 25, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(34, 'INS-2026-2CA682A4', 14, 31, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(35, 'INS-2026-9CB80E32', 14, 44, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(36, 'INS-2026-A666D86F', 14, 41, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(37, 'INS-2026-8B51C400', 14, 35, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-10-07 23:37:40'),
(38, 'INS-2026-EA5E1F64', 11, 11, 'INSCRITO', 'WEB_PARTICIPANTE', 0.00, NULL, 0, NULL, '2026-10-08 00:15:00');

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
(2, 5, 9, 'Cuestionario Realidad extendida', NULL, '6b1da47852cf736ae05f53d6b13f766d7ccecfb1.docx', 'DOCX', 210079, '2026-09-20 22:54:13'),
(3, 6, 9, 'INGLÉS CLASE 1', NULL, '506e09675cb17be3be21b07ac0590352384298aa.pdf', 'PDF', 10836623, '2026-09-22 12:19:03'),
(4, 6, 9, 'INGLES CLASE 3', NULL, '1e17bd1e0d7e121ae2d8925363d3fc17558aa5c4.pdf', 'PDF', 5241796, '2026-09-22 12:20:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantillas_certificado`
--

CREATE TABLE `plantillas_certificado` (
  `id_plantilla` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `nombre_plantilla` varchar(150) NOT NULL DEFAULT 'Plantilla de certificado',
  `destinatario` enum('PARTICIPANTE','EXPOSITOR','AMBOS') NOT NULL DEFAULT 'PARTICIPANTE',
  `ruta_archivo_pdf` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `pagina` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `configuracion_campos` longtext NOT NULL,
  `id_usuario_subio` bigint(20) UNSIGNED NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plantillas_certificado`
--

INSERT INTO `plantillas_certificado` (`id_plantilla`, `id_evento`, `nombre_plantilla`, `destinatario`, `ruta_archivo_pdf`, `nombre_archivo`, `pagina`, `configuracion_campos`, `id_usuario_subio`, `activa`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, 4, 'Plantilla de certificado', 'PARTICIPANTE', '55e3c8e7cbec21f95a03e4ddaebf6639d080509d.pdf', 'Certificado de participación.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":127.01717902350813,\"y\":117.88751366725201,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":189.0488245931284,\"y\":117.61897718965005,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":201.66998191681736,\"y\":132.11994698015488,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":138.5641952983725,\"y\":147.96359915866935,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":180.99276672694393,\"y\":146.35238029305773,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":85.125678119349,\"y\":192.2721179629896,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":267.46112115732365,\"y\":180.4565129485042,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false}}', 9, 0, '2026-09-21 13:28:01', '2026-09-29 03:22:29'),
(2, 5, 'Plantilla de certificado', 'PARTICIPANTE', '6f01d951a06d18aabd37ac4e244ef192943a0be7.pdf', 'Plantilla prueba.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":114.35716029482,\"y\":108.90223580777,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":222.67737070575433,\"y\":108.60218794686999,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":152.46428504820733,\"y\":129.0059509045086,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":195.07223348680895,\"y\":150.00984238501724,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":169.56745113328012,\"y\":138.60772846126633,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":87.352119780121,\"y\":20.385861769327,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":267.3857068109,\"y\":20.985969791523,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-21 13:37:18', '2026-09-21 13:55:53'),
(4, 6, 'Plantilla de certificado', 'PARTICIPANTE', 'f68b17780bfedd4c660af3583b843ee504be810d.pdf', 'Certificado diploma de Inglés tradicional elegante rojo y azul-1.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":85.40219875998794,\"y\":102.33397265431626,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":164.01791280341,\"y\":100.65416351685,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":76.331154831901,\"y\":122.82764413138,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":140.50039150837,\"y\":134.92226992112,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":199.63015933589625,\"y\":123.16360595887177,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"rol_expositor\":{\"activo\":false,\"x\":20,\"y\":20,\"ancho\":140,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":177.12053181064,\"y\":42.532767360592,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":133.78109970978,\"y\":162.13517794804,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-22 12:36:09', '2026-09-29 03:03:58'),
(7, 7, 'Plantilla de certificado', 'PARTICIPANTE', '5a128202e4180da5d899196acfb00604a71029af.pdf', 'Plantilla prueba.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":124.37409119176841,\"y\":107.70936189420155,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":221.4678576812905,\"y\":106.36551458423023,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":139.4924977385798,\"y\":127.86707154377139,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":192.5749029473843,\"y\":149.03266667581974,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":166.03370034298203,\"y\":136.93804088607783,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":262.11957306272,\"y\":28.086408778401,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":256,\"y\":180,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-23 13:20:49', '2026-09-23 13:21:35'),
(9, 8, 'Participacion', 'AMBOS', '3f455e021c2b20a65e53341c67209fedd0e15523.pdf', 'Certificado diploma de Inglés tradicional elegante rojo y azul-1.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":148.5,\"y\":82,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":148.5,\"y\":98,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":148.5,\"y\":116,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":148.5,\"y\":132,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":148.5,\"y\":140,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"rol_expositor\":{\"activo\":false,\"x\":148.5,\"y\":148,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":18,\"y\":190,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":256,\"y\":180,\"tamano\":28}}', 1, 1, '2026-09-28 03:56:07', '2026-09-28 03:56:07'),
(10, 8, 'Expositor', 'EXPOSITOR', '3831f74083eaf8f7fbe5115950c6fa044e26f52c.pdf', 'Plantilla prueba.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":131.09338299035124,\"y\":108.04532372169439,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":213.06874293306,\"y\":108.71724737668,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":157.6345855947535,\"y\":128.2030333712642,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":188.20736327831,\"y\":147.35285753836,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":163.68194821348,\"y\":137.27400271357,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"rol_expositor\":{\"activo\":false,\"x\":277,\"y\":148,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":86.746057119705,\"y\":21.031210401051,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":256,\"y\":180,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 1, 1, '2026-09-28 03:56:42', '2026-09-28 03:57:33'),
(11, 10, 'Test', 'EXPOSITOR', '28c55303c8b6af0f9285f7d95d3023e9709a7ec4.pdf', 'Certificado diploma de Inglés tradicional elegante rojo y azul-1.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":148.5,\"y\":82,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":148.5,\"y\":98,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":148.5,\"y\":116,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":148.5,\"y\":132,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":148.5,\"y\":140,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"rol_expositor\":{\"activo\":false,\"x\":148.5,\"y\":148,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":18,\"y\":190,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":256,\"y\":180,\"tamano\":28}}', 1, 1, '2026-09-29 15:57:56', '2026-09-29 15:57:56'),
(12, 9, 'Participacion', 'PARTICIPANTE', '335e827b5f46fed970a9ac34cc727980654af71b.pdf', 'Plantilla prueba - copia.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":139.4294360307414,\"y\":109.07155636436983,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":224.05423824593,\"y\":108.26560890355,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":149.10084199819,\"y\":129.48889203849,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":170.86150542495,\"y\":139.42891072194,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":194.77137017782,\"y\":150.44352601982,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"rol_expositor\":{\"activo\":false,\"x\":277,\"y\":148,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":257.63550896624,\"y\":24.984371285434,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":256.82955846896,\"y\":173.81600238361,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 1, 1, '2026-10-01 14:03:24', '2026-10-01 14:28:36');

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
-- Estructura de tabla para la tabla `series_sesiones_evento`
--

CREATE TABLE `series_sesiones_evento` (
  `id_serie` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `lugar_especifico` varchar(200) DEFAULT NULL,
  `frecuencia` enum('DIARIA','SEMANAL') NOT NULL DEFAULT 'SEMANAL',
  `intervalo_recurrencia` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('ACTIVA','FINALIZADA','CANCELADA') NOT NULL DEFAULT 'ACTIVA',
  `id_usuario_creador` bigint(20) UNSIGNED NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `series_sesiones_evento`
--

INSERT INTO `series_sesiones_evento` (`id_serie`, `id_evento`, `titulo`, `fecha_inicio`, `fecha_fin`, `hora_inicio`, `hora_fin`, `lugar_especifico`, `frecuencia`, `intervalo_recurrencia`, `estado`, `id_usuario_creador`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, 8, 'Capacipation de Ingles Primer Modulo', '2026-09-23', '2026-09-30', '09:00:00', '12:00:00', 'Enlace Virtual', 'SEMANAL', 1, 'ACTIVA', 9, '2026-09-23 14:52:34', '2026-09-23 14:52:34'),
(2, 8, 'Capacipation de Ingles Segundo Modulo', '2026-10-01', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'SEMANAL', 1, 'ACTIVA', 9, '2026-09-23 14:53:56', '2026-09-23 14:53:56'),
(3, 9, 'Clase', '2026-10-02', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'SEMANAL', 1, 'ACTIVA', 1, '2026-09-29 12:17:04', '2026-09-29 12:17:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serie_sesiones_dias`
--

CREATE TABLE `serie_sesiones_dias` (
  `id_serie` bigint(20) UNSIGNED NOT NULL,
  `dia_semana` tinyint(3) UNSIGNED NOT NULL COMMENT '1=Lunes, 2=Martes, ..., 7=Domingo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `serie_sesiones_dias`
--

INSERT INTO `serie_sesiones_dias` (`id_serie`, `dia_semana`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_evento`
--

CREATE TABLE `sesiones_evento` (
  `id_sesion` bigint(20) UNSIGNED NOT NULL,
  `id_evento` bigint(20) UNSIGNED NOT NULL,
  `id_serie` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `lugar_especifico` varchar(200) DEFAULT NULL,
  `estado` enum('PROGRAMADA','EN_CURSO','CONCLUIDA','CANCELADA') NOT NULL DEFAULT 'PROGRAMADA',
  `es_excepcion` tinyint(1) NOT NULL DEFAULT 0,
  `id_usuario_apertura` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_apertura_asistencia` datetime DEFAULT NULL,
  `fecha_cierre_programada` datetime DEFAULT NULL,
  `id_usuario_cierre` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_cierre_asistencia` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sesiones_evento`
--

INSERT INTO `sesiones_evento` (`id_sesion`, `id_evento`, `id_serie`, `titulo`, `fecha`, `hora_inicio`, `hora_fin`, `lugar_especifico`, `estado`, `es_excepcion`, `id_usuario_apertura`, `fecha_apertura_asistencia`, `fecha_cierre_programada`, `id_usuario_cierre`, `fecha_cierre_asistencia`) VALUES
(1, 1, NULL, 'Sesión 1: Modelado de Datos y Arquitectura MVC', '2026-09-15', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(2, 1, NULL, 'Sesión 2: Transacciones PDO y Control de Asistencia', '2026-09-16', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(3, 1, NULL, 'Sesión 3: Emisión Criptográfica de Certificados y QR', '2026-09-17', '18:30:00', '21:30:00', 'Laboratorio 3', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(4, 2, NULL, 'Sesión Inaugural: Fundamentos de Arquitectura', '2026-09-23', '18:00:00', '21:00:00', 'Auditorio A', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(5, 2, NULL, 'Sesión Práctica: Implementación de Patrones MVC', '2026-09-24', '18:00:00', '21:00:00', 'Laboratorio B', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(6, 4, NULL, 'test sesion', '2026-09-21', '13:00:00', '16:00:00', 'Auditorio', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(7, 5, NULL, 'Sesion 1', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(8, 5, NULL, 'Sesion 2', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(9, 5, NULL, 'Sesion 3', '2026-09-21', '15:30:00', '17:00:00', 'Curso 5-A', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(10, 6, NULL, 'Clase 1', '2026-09-23', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(12, 6, NULL, 'Clase 2', '2026-09-24', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(13, 6, NULL, 'Clase 3', '2026-09-25', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(14, 6, NULL, 'Clase 4', '2026-09-28', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(15, 6, NULL, 'Clase 5', '2026-09-23', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(16, 7, NULL, 'test 1', '2026-09-23', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 10, '2026-09-23 09:16:05', '2026-09-23 09:31:05', 10, '2026-09-23 09:16:48'),
(17, 7, NULL, 'test 2', '2026-09-23', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 10, '2026-09-23 09:17:24', '2026-09-23 09:47:24', 10, '2026-09-23 09:19:09'),
(18, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-23', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(19, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-24', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(20, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-25', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(21, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-28', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(22, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-29', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(23, 8, 1, 'Capacipation de Ingles Primer Modulo', '2026-09-30', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(24, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-01', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(25, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-02', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(26, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-05', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(27, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-06', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(28, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-07', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(29, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-08', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(30, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(31, 9, 3, 'Clase', '2026-10-02', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-09-29 08:58:20', '2026-09-29 09:28:20', 11, '2026-09-29 08:58:26'),
(32, 9, 3, 'Clase', '2026-10-05', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-10-01 09:54:18', '2026-10-01 10:24:18', 11, '2026-10-01 09:54:22'),
(33, 9, 3, 'Clase', '2026-10-06', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-10-01 09:54:29', '2026-10-01 10:24:29', 11, '2026-10-01 09:54:30'),
(34, 9, 3, 'Clase', '2026-10-07', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-10-01 09:54:40', '2026-10-01 10:24:40', 11, '2026-10-01 09:54:42'),
(35, 9, 3, 'Clase', '2026-10-08', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-10-01 09:54:51', '2026-10-01 10:24:51', 11, '2026-10-01 09:54:53'),
(36, 9, 3, 'Clase', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'CONCLUIDA', 0, 11, '2026-10-01 09:55:18', '2026-10-01 10:25:18', 11, '2026-10-01 09:55:20'),
(37, 10, NULL, 'Feria Ciencias', '2026-09-29', '09:00:00', '12:00:00', 'Campo Ferial', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(38, 9, NULL, 'Examen', '2026-10-09', '18:00:00', '20:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(39, 14, NULL, 'Taller', '2026-10-27', '09:00:00', '12:00:00', 'Sala de Capacitación', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL),
(40, 14, NULL, 'Taller', '2026-10-28', '09:00:00', '12:00:00', 'Sala de Capacitación', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_recuperacion_password`
--

CREATE TABLE `solicitudes_recuperacion_password` (
  `id_solicitud` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('PENDIENTE','APROBADA','RECHAZADA') NOT NULL DEFAULT 'PENDIENTE',
  `motivo_rechazo` text DEFAULT NULL,
  `id_usuario_resolucion` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_recuperacion_password`
--

INSERT INTO `solicitudes_recuperacion_password` (`id_solicitud`, `id_usuario`, `estado`, `motivo_rechazo`, `id_usuario_resolucion`, `fecha_solicitud`, `fecha_resolucion`) VALUES
(1, 8, 'RECHAZADA', 'No mostro sus documentos', 1, '2026-10-04 14:58:11', '2026-10-04 10:58:58'),
(2, 8, 'APROBADA', NULL, 1, '2026-10-05 14:32:21', '2026-10-06 11:09:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_reimpresion`
--

CREATE TABLE `solicitudes_reimpresion` (
  `id_solicitud` bigint(20) UNSIGNED NOT NULL,
  `id_certificado` bigint(20) UNSIGNED NOT NULL,
  `id_usuario_solicitante` bigint(20) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `ruta_comprobante` varchar(255) DEFAULT NULL,
  `nombre_comprobante` varchar(255) DEFAULT NULL,
  `tipo_comprobante` varchar(100) DEFAULT NULL,
  `tamano_comprobante` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `solicitudes_reimpresion` (`id_solicitud`, `id_certificado`, `id_usuario_solicitante`, `motivo`, `ruta_comprobante`, `nombre_comprobante`, `tipo_comprobante`, `tamano_comprobante`, `estado`, `motivo_rechazo`, `id_usuario_resolucion`, `fecha_solicitud`, `fecha_resolucion`, `fecha_entrega`, `responsable_entrega`) VALUES
(1, 1, 4, 'Extravío del ejemplar físico original entregado en secretaría.', NULL, NULL, NULL, NULL, 'ENTREGADA', NULL, 1, '2026-09-12 05:31:52', '2026-09-12 01:31:52', '2026-09-12 01:31:52', 1),
(2, 2, 8, 'Error de impresion', NULL, NULL, NULL, NULL, 'RECHAZADA', 'No se adjunto comprobante de pago', 1, '2026-09-29 03:20:28', '2026-09-30 10:46:03', NULL, NULL),
(3, 7, 8, 'Test solicitud', 'reimpresion_ca6ba57c9e9364f0bbf28523ee312a95.jpg', 'Paleta.jpeg', 'image/jpeg', 248147, 'ENTREGADA', NULL, 1, '2026-09-30 14:51:42', '2026-09-30 10:52:03', '2026-10-07 19:55:23', 1),
(4, 9, 8, 'jasdlsaflj', 'reimpresion_9103c31621e389fdf00f7d31af45f222.jpg', 'Paleta.jpeg', 'image/jpeg', 248147, 'ENTREGADA', NULL, 1, '2026-10-05 14:28:42', '2026-10-05 10:29:03', '2026-10-05 10:29:41', 1),
(5, 6, 11, 'Test', 'reimpresion_20a077e7c7904dd8b2ab3dbfe60ea764.jpg', '767190433_1595862205660834_3108776283956800064_n.jpg', 'image/jpeg', 199049, 'RECHAZADA', 'No adjunto boleta de pago', 1, '2026-10-06 12:23:16', '2026-10-06 08:24:14', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_evento`
--

CREATE TABLE `tipos_evento` (
  `id_tipo_evento` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(8) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_evento`
--

INSERT INTO `tipos_evento` (`id_tipo_evento`, `nombre`, `abreviatura`, `activo`) VALUES
(1, 'Curso', 'CUR', 1),
(2, 'Taller', 'TAL', 1),
(3, 'Conferencia', 'CONF', 1),
(4, 'Seminario', 'SEM', 1),
(5, 'Capacitación Especializada', 'CAP', 1),
(6, 'Webinar', 'WEB', 1),
(7, 'Diplomado Internacional', 'DIP', 1),
(8, 'Workshop', 'WKS', 1),
(9, 'Feria', 'FER', 1);

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
  `foto_perfil` varchar(255) DEFAULT NULL,
  `fecha_ultimo_cambio_foto` datetime DEFAULT NULL,
  `genero` enum('FEMENINO','MASCULINO','NO_BINARIO','PREFIERO_NO_INDICAR') DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `condicion_academica` enum('ESTUDIANTE','PROFESIONAL') DEFAULT NULL,
  `institucion` varchar(200) DEFAULT NULL,
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

INSERT INTO `usuarios` (`id_usuario`, `ci`, `nombres`, `apellidos`, `correo`, `telefono`, `foto_perfil`, `fecha_ultimo_cambio_foto`, `genero`, `fecha_nacimiento`, `condicion_academica`, `institucion`, `password_hash`, `id_rol`, `activo`, `token_recuperacion`, `token_expiracion`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, '4567890', 'Carlos', 'Administrador Central', 'admin@eventos.edu', '70011223', 'perfil_1_72c04df31ced7466c4fa1997724bdc02.png', '2026-10-07 21:57:15', NULL, NULL, NULL, NULL, '$2y$10$0caAUavAuj2/H5Ts9Gf.sO.pRaj7Nr72zN6UNBsOMNpw0B0VRltuW', 1, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-10-08 01:57:15'),
(2, '5678901', 'Dra. Elena', 'Ríos Morales', 'elena.rios@eventos.edu', '71122334', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-21 03:44:29'),
(3, '6789012', 'Ing. Marcos', 'Torres Benítez', 'marcos.torres@eventos.edu', '72233445', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-25 14:46:41'),
(4, '7890123', 'Juan Pablo', 'Gutiérrez Mendoza', 'juan.gutierrez@estudiante.edu', '73344556', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 3, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(5, '8901234', 'María Fernanda', 'López Roca', 'maria.lopez@estudiante.edu', '74455667', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-25 14:46:42'),
(6, '13008609', 'Jonathan', 'Robles', 'administrador@gmail.com', '67355113', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$0o/yfCAJCNKRtyQ9nh1HXe.VCzgX1XGgZRsVhxKn16aJx2kP6Ja2G', 3, 1, NULL, NULL, '2026-09-12 06:17:01', '2026-09-25 14:49:22'),
(8, '13264670', 'luis', 'mariscal', 'luisandresmariscalfarell@gmail.com', '74729305', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$HOdwtHbsPdSBHZ8npm0byuVogevzW81pFiB6nXJj9GXLbdqmBzLH2', 3, 1, '572c33ddc0bd1980ba4d17364d27c4b4efc98b01471378f632b5fe5f65d43339', '2026-10-06 18:09:34', '2026-09-15 13:23:59', '2026-10-06 15:09:34'),
(9, '1000005', 'ad', 'min', '123@gmail.com', '77777777', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$6hHfBUQ9qSqT8gqECiHlkeMAApBiGcnrus2gV.Jrn8d/Z05eNwrIG', 1, 0, NULL, NULL, '2026-09-15 13:28:06', '2026-09-27 22:27:48'),
(10, '13462670', 'Rigoberto', 'Bolaños', 'rgb@edu.bo', '111111111', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$Xw4JZtiqaspsmUtN1dZ88e2wD.wWk/nPGBk5DN1mzwOSsGlswOdjq', 2, 1, NULL, NULL, '2026-09-15 13:33:47', '2026-09-15 13:34:29'),
(11, '25641478', 'Eduardo', 'Garcia Salinaz', 'egs@edu.bo', '78454512', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$ZkWjQlaTI6fN6G8dK1tvieppAbZFg5kkuAfNJwUPXNoIlPsDZ1/hy', 2, 1, NULL, NULL, '2026-09-20 21:53:58', '2026-09-20 21:54:57'),
(12, '14151617', 'Ana', 'Vaca Flores', 'avf@gmail.com', '78962214', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$D4c/mNuX8a9A05hcwyPkHOzotrgds57Js/GomxoIjXW910pcWWqiW', 3, 1, NULL, NULL, '2026-09-21 03:23:58', '2026-09-21 03:23:58'),
(13, '123456798', 'Navarro', 'Navarro', 'adolfo@gmail.com', '1234567', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$.ZIKWJgQB2vLgB45488y8uhytjqwrmRImrXVIe.ZoARGoJp/MXX/G', 3, 1, NULL, NULL, '2026-09-22 14:10:22', '2026-10-07 21:44:56'),
(14, '100001', 'María Fernanda', 'Roca Suárez', 'maria.roca01@correo.test', '71010001', NULL, NULL, 'FEMENINO', '2002-03-14', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(15, '100002', 'José Andrés', 'Méndez Vargas', 'jose.mendez02@correo.test', '71010002', NULL, NULL, 'MASCULINO', '2001-07-22', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(16, '100003', 'Carla Sofía', 'Rivero Paz', 'carla.rivero03@correo.test', '71010003', NULL, NULL, 'FEMENINO', '2003-11-05', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(17, '100004', 'Diego Armando', 'Flores Justiniano', 'diego.flores04@correo.test', '71010004', NULL, NULL, 'MASCULINO', '2000-01-19', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(18, '100005', 'Andrea Lucía', 'Suárez Ortiz', 'andrea.suarez05@correo.test', '71010005', NULL, NULL, 'FEMENINO', '2002-09-08', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(19, '100006', 'Marco Antonio', 'Velasco Rojas', 'marco.velasco06@correo.test', '71010006', NULL, NULL, 'MASCULINO', '1999-06-30', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(20, '100007', 'Daniela Alejandra', 'Salvatierra Peña', 'daniela.salvatierra07@correo.test', '71010007', NULL, NULL, 'FEMENINO', '2001-12-17', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(21, '100008', 'Luis Fernando', 'Chávez Méndez', 'luis.chavez08@correo.test', '71010008', NULL, NULL, 'MASCULINO', '2002-04-12', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(22, '100009', 'Valeria Nicole', 'Soria Roca', 'valeria.soria09@correo.test', '71010009', NULL, NULL, 'FEMENINO', '2003-08-26', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(23, '100010', 'Rodrigo Javier', 'Gutiérrez Paz', 'rodrigo.gutierrez10@correo.test', '71010010', NULL, NULL, 'MASCULINO', '1998-02-03', 'PROFESIONAL', 'Empresa privada', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(24, '100011', 'Ana Paula', 'Moreno Vaca', 'ana.moreno11@correo.test', '71010011', NULL, NULL, 'FEMENINO', '2000-05-11', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(25, '100012', 'Miguel Ángel', 'Ardaya López', 'miguel.ardaya12@correo.test', '71010012', NULL, NULL, 'MASCULINO', '1997-10-29', 'PROFESIONAL', 'Gobierno Municipal', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(26, '100013', 'Fernanda Isabel', 'Melgar Ruiz', 'fernanda.melgar13@correo.test', '71010013', NULL, NULL, 'FEMENINO', '2002-07-07', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(27, '100014', 'Carlos Eduardo', 'Vaca Ribera', 'carlos.vaca14@correo.test', '71010014', NULL, NULL, 'MASCULINO', '2001-12-15', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(28, '100015', 'Paola Andrea', 'Hurtado Céspedes', 'paola.hurtado15@correo.test', '71010015', NULL, NULL, 'FEMENINO', '1999-09-09', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(29, '100016', 'Juan Pablo', 'Mendoza Barba', 'juan.mendoza16@correo.test', '71010016', NULL, NULL, 'MASCULINO', '2003-03-21', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(30, '100017', 'Gabriela Alejandra', 'Limpias Roca', 'gabriela.limpias17@correo.test', '71010017', NULL, NULL, 'FEMENINO', '2002-01-06', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(31, '100018', 'Sebastián Andrés', 'Ávila Suárez', 'sebastian.avila18@correo.test', '71010018', NULL, NULL, 'MASCULINO', '2000-06-24', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(32, '100019', 'Camila Belén', 'Montero Vargas', 'camila.montero19@correo.test', '71010019', NULL, NULL, 'FEMENINO', '2003-11-13', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(33, '100020', 'Óscar Daniel', 'Pérez Rojas', 'oscar.perez20@correo.test', '71010020', NULL, NULL, 'MASCULINO', '1996-05-02', 'PROFESIONAL', 'Empresa privada', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(34, '100021', 'Natalia Fernanda', 'Ribera Chávez', 'natalia.ribera21@correo.test', '71010021', NULL, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(35, '100022', 'Kevin Alejandro', 'Coimbra Vaca', 'kevin.coimbra22@correo.test', '71010022', NULL, NULL, 'MASCULINO', NULL, 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(36, '100023', 'Alejandra Sofía', 'Durán Melgar', 'alejandra.duran23@correo.test', '71010023', NULL, NULL, 'FEMENINO', NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(37, '100024', 'Cristian David', 'Guzmán Flores', 'cristian.guzman24@correo.test', '71010024', NULL, NULL, NULL, '2002-04-16', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(38, '100025', 'Mariana Isabel', 'Méndez Rivero', 'mariana.mendez25@correo.test', '71010025', NULL, NULL, 'FEMENINO', '2001-09-27', NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(39, '100026', 'Héctor Andrés', 'Saucedo Roca', 'hector.saucedo26@correo.test', '71010026', NULL, NULL, 'MASCULINO', NULL, NULL, 'Instituto Técnico Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(40, '100027', 'Claudia Vanessa', 'Ortiz Paz', 'claudia.ortiz27@correo.test', '71010027', NULL, NULL, NULL, NULL, 'PROFESIONAL', NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(41, '100028', 'Bruno Alejandro', 'Céspedes Vaca', 'bruno.cespedes28@correo.test', '71010028', NULL, NULL, 'MASCULINO', '2000-02-01', NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(42, '100029', 'Jimena Andrea', 'Vargas Limpias', 'jimena.vargas29@correo.test', '71010029', NULL, NULL, 'FEMENINO', NULL, 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(43, '100030', 'Mauricio Javier', 'Salazar Ribera', 'mauricio.salazar30@correo.test', '71010030', NULL, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(44, '100031', 'Luciana Belén', 'Barba Moreno', 'luciana.barba31@correo.test', NULL, NULL, NULL, 'FEMENINO', '2003-08-11', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(45, '100032', 'Andrés Felipe', 'Rocha Suárez', 'andres.rocha32@correo.test', NULL, NULL, NULL, 'MASCULINO', NULL, 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(46, '100033', 'Mónica Patricia', 'Aguilera Rojas', 'monica.aguilera33@correo.test', NULL, NULL, NULL, 'FEMENINO', '1995-01-20', 'PROFESIONAL', NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(47, '100034', 'Eduardo José', 'López Rivero', 'eduardo.lopez34@correo.test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(48, '100035', 'Karla Daniela', 'Vaca Flores', 'karla.vaca35@correo.test', NULL, NULL, NULL, 'FEMENINO', '2002-06-14', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(49, '100036', 'Nicolás Andrés', 'Moreno Salvatierra', 'nicolas.moreno36@correo.test', '71010036', NULL, NULL, 'MASCULINO', '2001-03-28', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(50, '100037', 'Adriana Lucía', 'Justiniano Roca', 'adriana.justiniano37@correo.test', '71010037', NULL, NULL, 'FEMENINO', '1998-10-18', 'PROFESIONAL', 'Clínica Trinidad', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(51, '100038', 'Sergio Alejandro', 'Peña Vargas', 'sergio.pena38@correo.test', '71010038', NULL, NULL, 'MASCULINO', '1997-05-05', 'PROFESIONAL', 'Consultora Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(52, '100039', 'Lorena Isabel', 'Méndez Barba', 'lorena.mendez39@correo.test', '71010039', NULL, NULL, 'FEMENINO', '2000-12-09', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(53, '100040', 'Fernando Javier', 'Chávez Roca', 'fernando.chavez40@correo.test', '71010040', NULL, NULL, 'MASCULINO', '2002-02-22', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(54, '100041', 'Patricia Alejandra', 'Salvatierra Paz', 'patricia.salvatierra41@correo.test', '71010041', NULL, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(55, '100042', 'Ricardo Andrés', 'Ribera Suárez', 'ricardo.ribera42@correo.test', '71010042', NULL, NULL, 'MASCULINO', NULL, 'PROFESIONAL', NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(56, '100043', 'Elena Sofía', 'Melgar Ortiz', 'elena.melgar43@correo.test', '71010043', NULL, NULL, 'FEMENINO', '2003-07-03', NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(57, '100044', 'Pablo Daniel', 'Durán Vargas', 'pablo.duran44@correo.test', '71010044', NULL, NULL, NULL, NULL, 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(58, '100045', 'Sofía Valentina', 'Rojas Vaca', 'sofia.rojas45@correo.test', '71010045', NULL, NULL, 'FEMENINO', '2002-09-17', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(59, '100046', 'Javier Alejandro', 'Flores Céspedes', 'javier.flores46@correo.test', '71010046', NULL, NULL, 'MASCULINO', '1998-12-12', 'PROFESIONAL', 'Alcaldía de Trinidad', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(60, '100047', 'Melissa Fernanda', 'Suárez Rivero', 'melissa.suarez47@correo.test', '71010047', NULL, NULL, 'FEMENINO', NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(61, '100048', 'Álvaro José', 'Paz Moreno', 'alvaro.paz48@correo.test', '71010048', NULL, NULL, 'MASCULINO', '2001-06-06', 'ESTUDIANTE', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(62, '100049', 'Tatiana Andrea', 'Limpias Chávez', 'tatiana.limpias49@correo.test', '71010049', NULL, NULL, NULL, '2000-05-25', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(63, '100050', 'Renato Miguel', 'Barba Méndez', 'renato.barba50@correo.test', '71010050', NULL, NULL, 'MASCULINO', NULL, NULL, NULL, 'PENDIENTE_CAMBIO_ADMIN', 3, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(64, '200001', 'Roberto Carlos', 'Méndez Aguilera', 'roberto.mendez@eventos.test', '72020001', NULL, NULL, 'MASCULINO', '1985-05-18', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(65, '200002', 'Laura Patricia', 'Suárez Ribera', 'laura.suarez@eventos.test', '72020002', NULL, NULL, 'FEMENINO', '1988-11-09', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(66, '200003', 'Marcelo Andrés', 'Vargas Roca', 'marcelo.vargas@eventos.test', '72020003', NULL, NULL, 'MASCULINO', '1982-02-21', 'PROFESIONAL', 'Consultora Tecnológica Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(67, '200004', 'Gabriela Fernanda', 'Moreno Paz', 'gabriela.moreno@eventos.test', '72020004', NULL, NULL, 'FEMENINO', '1990-07-15', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(68, '200005', 'Jorge Luis', 'Flores Salvatierra', 'jorge.flores@eventos.test', '72020005', NULL, NULL, 'MASCULINO', '1984-04-03', 'PROFESIONAL', 'Colegio de Profesionales del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(69, '200006', 'Verónica Isabel', 'Ribera Chávez', 'veronica.ribera@eventos.test', '72020006', NULL, NULL, 'FEMENINO', '1987-01-27', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(70, '200007', 'Daniel Eduardo', 'Rojas Céspedes', 'daniel.rojas@eventos.test', '72020007', NULL, NULL, 'MASCULINO', '1986-09-12', 'PROFESIONAL', 'Centro de Investigación Tecnológica', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(71, '200008', 'Ana María', 'Vaca Justiniano', 'ana.vaca@eventos.test', '72020008', NULL, NULL, 'FEMENINO', '1989-06-30', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(72, '200009', 'Fernando José', 'Ortiz Melgar', 'fernando.ortiz@eventos.test', '72020009', NULL, NULL, 'MASCULINO', '1983-12-06', 'PROFESIONAL', 'Fundación Educación y Tecnología', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58'),
(73, '200010', 'Claudia Marcela', 'Peña Rivero', 'claudia.pena@eventos.test', '72020010', NULL, NULL, 'FEMENINO', '1991-03-24', 'PROFESIONAL', 'Universidad Autónoma del Beni', 'PENDIENTE_CAMBIO_ADMIN', 2, 1, NULL, NULL, '2026-10-07 21:50:58', '2026-10-07 21:50:58');

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
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `uq_categorias_abreviatura` (`abreviatura`);

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id_certificado`),
  ADD UNIQUE KEY `codigo_unico` (`codigo_unico`),
  ADD UNIQUE KEY `uq_certificado_participante_plantilla` (`id_inscripcion`,`id_plantilla`),
  ADD UNIQUE KEY `uq_certificado_expositor_plantilla` (`id_evento`,`id_usuario`,`id_plantilla`,`tipo_participacion`),
  ADD KEY `fk_cer_evento` (`id_evento`),
  ADD KEY `fk_cer_usuario` (`id_usuario`),
  ADD KEY `fk_cer_usuario_anula` (`id_usuario_anulacion`),
  ADD KEY `idx_certificados_codigo` (`codigo_unico`),
  ADD KEY `idx_certificados_estado` (`estado`),
  ADD KEY `idx_certificado_plantilla` (`id_plantilla`);

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
-- Indices de la tabla `plantillas_certificado`
--
ALTER TABLE `plantillas_certificado`
  ADD PRIMARY KEY (`id_plantilla`),
  ADD KEY `fk_plantilla_usuario` (`id_usuario_subio`),
  ADD KEY `idx_plantilla_evento_destinatario` (`id_evento`,`destinatario`,`activa`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `series_sesiones_evento`
--
ALTER TABLE `series_sesiones_evento`
  ADD PRIMARY KEY (`id_serie`),
  ADD KEY `idx_serie_evento` (`id_evento`,`estado`),
  ADD KEY `idx_serie_fechas` (`fecha_inicio`,`fecha_fin`),
  ADD KEY `fk_serie_usuario_creador` (`id_usuario_creador`);

--
-- Indices de la tabla `serie_sesiones_dias`
--
ALTER TABLE `serie_sesiones_dias`
  ADD PRIMARY KEY (`id_serie`,`dia_semana`);

--
-- Indices de la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `idx_sesiones_evento_fecha` (`id_evento`,`fecha`),
  ADD KEY `idx_sesion_ventana_asistencia` (`estado`,`fecha_apertura_asistencia`,`fecha_cierre_programada`),
  ADD KEY `fk_sesion_usuario_apertura` (`id_usuario_apertura`),
  ADD KEY `fk_sesion_usuario_cierre` (`id_usuario_cierre`),
  ADD KEY `idx_sesion_serie` (`id_serie`);

--
-- Indices de la tabla `solicitudes_recuperacion_password`
--
ALTER TABLE `solicitudes_recuperacion_password`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `idx_recuperacion_estado_fecha` (`estado`,`fecha_solicitud`),
  ADD KEY `idx_recuperacion_usuario` (`id_usuario`),
  ADD KEY `idx_recuperacion_resolutor` (`id_usuario_resolucion`);

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
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `uq_tipos_evento_abreviatura` (`abreviatura`);

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
  MODIFY `id_asistencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `auditoria_actividad`
--
ALTER TABLE `auditoria_actividad`
  MODIFY `id_auditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `evento_expositores`
--
ALTER TABLE `evento_expositores`
  MODIFY `id_evento_expositor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `materiales_evento`
--
ALTER TABLE `materiales_evento`
  MODIFY `id_material` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `plantillas_certificado`
--
ALTER TABLE `plantillas_certificado`
  MODIFY `id_plantilla` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `series_sesiones_evento`
--
ALTER TABLE `series_sesiones_evento`
  MODIFY `id_serie` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  MODIFY `id_sesion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `solicitudes_recuperacion_password`
--
ALTER TABLE `solicitudes_recuperacion_password`
  MODIFY `id_solicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `solicitudes_reimpresion`
--
ALTER TABLE `solicitudes_reimpresion`
  MODIFY `id_solicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  MODIFY `id_tipo_evento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

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
  ADD CONSTRAINT `fk_cer_usuario_anula` FOREIGN KEY (`id_usuario_anulacion`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_certificado_plantilla` FOREIGN KEY (`id_plantilla`) REFERENCES `plantillas_certificado` (`id_plantilla`) ON UPDATE CASCADE;

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
-- Filtros para la tabla `plantillas_certificado`
--
ALTER TABLE `plantillas_certificado`
  ADD CONSTRAINT `fk_plantilla_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_plantilla_usuario` FOREIGN KEY (`id_usuario_subio`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `series_sesiones_evento`
--
ALTER TABLE `series_sesiones_evento`
  ADD CONSTRAINT `fk_serie_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_serie_usuario_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `serie_sesiones_dias`
--
ALTER TABLE `serie_sesiones_dias`
  ADD CONSTRAINT `fk_serie_dia_serie` FOREIGN KEY (`id_serie`) REFERENCES `series_sesiones_evento` (`id_serie`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  ADD CONSTRAINT `fk_sesion_serie` FOREIGN KEY (`id_serie`) REFERENCES `series_sesiones_evento` (`id_serie`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sesion_usuario_apertura` FOREIGN KEY (`id_usuario_apertura`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sesion_usuario_cierre` FOREIGN KEY (`id_usuario_cierre`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sesiones_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_recuperacion_password`
--
ALTER TABLE `solicitudes_recuperacion_password`
  ADD CONSTRAINT `fk_recuperacion_resolutor` FOREIGN KEY (`id_usuario_resolucion`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

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

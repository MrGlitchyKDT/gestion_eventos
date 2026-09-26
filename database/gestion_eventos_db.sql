-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-09-2026 a las 12:42:13
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
(38, 17, 13, 'FALTA', 10, 'DOCENTE', '2026-09-23 09:18:19', '2026-09-23 13:19:17', NULL);

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
(142, 9, 'CAMBIAR_ROL', 'USUARIOS', '127.0.0.1', 'Se asignó el rol ID #1 al usuario ID #1', '2026-09-25 14:49:41');

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
(6, 'Comunicación e Idiomas', 1);

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
(1, 'CERT-2026-F2C5-B0B1', 1, 1, 4, 'PARTICIPANTE', '2026-09-12 05:31:52', NULL, NULL, 'EMITIDO', NULL, NULL, NULL),
(2, 'CERT-2026-AF49-D9AE', 4, 4, 8, 'PARTICIPANTE', '2026-09-21 13:28:09', 'CERT-2026-AF49-D9AE.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-AF49-D9AE', 'EMITIDO', NULL, NULL, NULL),
(3, 'CERT-2026-08CD-F53E', 6, 5, 12, 'PARTICIPANTE', '2026-09-21 13:37:49', 'CERT-2026-08CD-F53E.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-08CD-F53E', 'EMITIDO', NULL, NULL, NULL),
(4, 'CERT-2026-C886-3148', 8, 6, 8, 'PARTICIPANTE', '2026-09-22 12:38:52', 'CERT-2026-C886-3148.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-C886-3148', 'EMITIDO', NULL, NULL, NULL),
(5, 'CERT-2026-51E8-1D3D', 10, 6, 13, 'PARTICIPANTE', '2026-09-22 14:19:00', 'CERT-2026-51E8-1D3D.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-51E8-1D3D', 'EMITIDO', NULL, NULL, NULL),
(6, 'CERT-2026-B4B9-8C0D', 9, 6, 11, 'PARTICIPANTE', '2026-09-22 14:19:52', 'CERT-2026-B4B9-8C0D.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-B4B9-8C0D', 'EMITIDO', NULL, NULL, NULL),
(7, 'CERT-2026-F354-8962', 11, 7, 8, 'PARTICIPANTE', '2026-09-23 13:21:09', 'CERT-2026-F354-8962.pdf', 'http://localhost/gestion_eventos/public/index.php?action=verificar_certificado&codigo=CERT-2026-F354-8962', 'EMITIDO', NULL, NULL, NULL);

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
(5, 'EVT-TEST-000003', 'Test sesiones', 'Texto descripcion', 1, 4, 'PRESENCIAL', 'Curso 5-A', NULL, 40, '2026-09-20 17:00:00', '2026-09-21 10:00:00', '2026-09-21', '2026-09-30', 1, 70, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-20 22:54:13', '2026-09-21 03:33:06'),
(6, 'EVT-2026-ING01', 'Curso de Ingles Basico - Intermedio', 'Clase de capacitación para Ingles Basico e Intermedio', 1, 6, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 80, '2026-09-14 10:00:00', '2026-09-22 17:00:00', '2026-09-23', '2026-09-28', 1, 80, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-22 12:19:03', '2026-09-22 12:28:52'),
(7, 'EVT-TEST-000004', 'Testeo de asistencia', 'test asisencia', 1, 1, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 30, '2026-09-22 09:00:00', '2026-09-23 12:00:00', '2026-09-23', '2026-09-25', 1, 20, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-23 13:10:33', '2026-09-23 13:12:04'),
(8, 'EVT-2026-ING02', 'Curso de Ingles Intermedio - Avanzado', 'Clases de Ingles de nivel intermedioi', 1, 6, 'VIRTUAL', NULL, 'https://meet.google.com/test-abc-def', 50, '2026-09-22 09:00:00', '2026-09-28 12:00:00', '2026-09-23', '2026-10-09', 1, 100, 80.00, 0.00, 'PUBLICADO', 9, '2026-09-23 14:50:22', '2026-09-25 13:34:57');

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
(8, 8, 10, 'Docente encargado', '2026-09-23 14:50:33');

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
(16, 'INS-2026-503989C7', 8, 4, 'INSCRITO', 'ADMINISTRATIVO', 0.00, NULL, 0, NULL, '2026-09-25 13:34:07');

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

INSERT INTO `plantillas_certificado` (`id_plantilla`, `id_evento`, `ruta_archivo_pdf`, `nombre_archivo`, `pagina`, `configuracion_campos`, `id_usuario_subio`, `activa`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, 4, '55e3c8e7cbec21f95a03e4ddaebf6639d080509d.pdf', 'Certificado de participación.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":127.01717902350813,\"y\":117.88751366725201,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":189.0488245931284,\"y\":117.61897718965005,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":201.66998191681736,\"y\":132.11994698015488,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":138.5641952983725,\"y\":147.96359915866935,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":180.99276672694393,\"y\":146.35238029305773,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":85.125678119349,\"y\":192.2721179629896,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":267.46112115732365,\"y\":180.4565129485042,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false}}', 9, 1, '2026-09-21 13:28:01', '2026-09-21 13:36:33'),
(2, 5, '6f01d951a06d18aabd37ac4e244ef192943a0be7.pdf', 'Plantilla prueba.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":114.35716029482,\"y\":108.90223580777,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true},\"ci\":{\"activo\":true,\"x\":222.67737070575433,\"y\":108.60218794686999,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"evento\":{\"activo\":true,\"x\":152.46428504820733,\"y\":129.0059509045086,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true},\"horas\":{\"activo\":true,\"x\":195.07223348680895,\"y\":150.00984238501724,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false},\"fechas\":{\"activo\":true,\"x\":169.56745113328012,\"y\":138.60772846126633,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false},\"codigo\":{\"activo\":true,\"x\":87.352119780121,\"y\":20.385861769327,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false},\"qr\":{\"activo\":true,\"x\":267.3857068109,\"y\":20.985969791523,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-21 13:37:18', '2026-09-21 13:55:53'),
(4, 6, 'f68b17780bfedd4c660af3583b843ee504be810d.pdf', 'Certificado diploma de Inglés tradicional elegante rojo y azul-1.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":72.63554434268053,\"y\":101.6620489993306,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":164.0179128034072,\"y\":100.6541635168521,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":76.33115483190109,\"y\":122.82764413137893,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":140.50039150836724,\"y\":134.92226992112083,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":186.86350491858886,\"y\":123.16360595887177,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":177.12053181064,\"y\":42.532767360592,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":133.78109970978,\"y\":162.13517794804,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-22 12:36:09', '2026-09-23 23:17:46'),
(7, 7, '5a128202e4180da5d899196acfb00604a71029af.pdf', 'Plantilla prueba.pdf', 1, '{\"nombre\":{\"activo\":true,\"x\":124.37409119176841,\"y\":107.70936189420155,\"ancho\":257,\"tamano\":22,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"ci\":{\"activo\":true,\"x\":221.4678576812905,\"y\":106.36551458423023,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"evento\":{\"activo\":true,\"x\":139.4924977385798,\"y\":127.86707154377139,\"ancho\":247,\"tamano\":15,\"alineacion\":\"C\",\"negrita\":true,\"ancla\":\"centro\"},\"horas\":{\"activo\":true,\"x\":192.5749029473843,\"y\":149.03266667581974,\"ancho\":257,\"tamano\":11,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"fechas\":{\"activo\":true,\"x\":166.03370034298203,\"y\":136.93804088607783,\"ancho\":257,\"tamano\":10,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"codigo\":{\"activo\":true,\"x\":262.11957306272,\"y\":28.086408778401,\"ancho\":100,\"tamano\":8,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"validacion\":{\"activo\":false,\"x\":18,\"y\":196,\"ancho\":170,\"tamano\":6,\"alineacion\":\"L\",\"negrita\":false,\"ancla\":\"centro\"},\"qr\":{\"activo\":true,\"x\":256,\"y\":180,\"ancho\":28,\"tamano\":28,\"alineacion\":\"C\",\"negrita\":false,\"ancla\":\"centro\"},\"_lienzo\":{\"ancho\":297.1270833333333,\"alto\":210.07916172777777}}', 9, 1, '2026-09-23 13:20:49', '2026-09-23 13:21:35');

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
(2, 8, 'Capacipation de Ingles Segundo Modulo', '2026-10-01', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'SEMANAL', 1, 'ACTIVA', 9, '2026-09-23 14:53:56', '2026-09-23 14:53:56');

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
(2, 5);

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
(30, 8, 2, 'Capacipation de Ingles Segundo Modulo', '2026-10-09', '09:00:00', '12:00:00', 'Enlace Virtual', 'PROGRAMADA', 0, NULL, NULL, NULL, NULL, NULL);

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
(7, 'Diplomado Internacional', 1),
(8, 'Workshop', 1);

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
(3, '6789012', 'Ing. Marcos', 'Torres Benítez', 'marcos.torres@eventos.edu', '72233445', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-25 14:46:41'),
(4, '7890123', 'Juan Pablo', 'Gutiérrez Mendoza', 'juan.gutierrez@estudiante.edu', '73344556', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 3, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-12 05:01:47'),
(5, '8901234', 'María Fernanda', 'López Roca', 'maria.lopez@estudiante.edu', '74455667', '$2y$10$yIeP6J14z.E7E.W9sN7WvOSZqD0NlO8.dkn2N0eWfUv8o8eBszj7W', 2, 1, NULL, NULL, '2026-09-12 05:01:47', '2026-09-25 14:46:42'),
(6, '13008609', 'Jonathan', 'Robles', 'administrador@gmail.com', '67355113', '$2y$10$0o/yfCAJCNKRtyQ9nh1HXe.VCzgX1XGgZRsVhxKn16aJx2kP6Ja2G', 3, 1, NULL, NULL, '2026-09-12 06:17:01', '2026-09-25 14:49:22'),
(8, '13264670', 'luis', 'mariscal', 'abc@gmail.com', '74729305', '$2y$10$HOdwtHbsPdSBHZ8npm0byuVogevzW81pFiB6nXJj9GXLbdqmBzLH2', 3, 1, NULL, NULL, '2026-09-15 13:23:59', '2026-09-25 14:49:19'),
(9, '1000005', 'ad', 'min', '123@gmail.com', '77777777', '$2y$10$6hHfBUQ9qSqT8gqECiHlkeMAApBiGcnrus2gV.Jrn8d/Z05eNwrIG', 1, 1, NULL, NULL, '2026-09-15 13:28:06', '2026-09-15 13:28:20'),
(10, '13462670', 'Rigoberto', 'Bolaños', 'rgb@edu.bo', '111111111', '$2y$10$Xw4JZtiqaspsmUtN1dZ88e2wD.wWk/nPGBk5DN1mzwOSsGlswOdjq', 2, 1, NULL, NULL, '2026-09-15 13:33:47', '2026-09-15 13:34:29'),
(11, '25641478', 'Eduardo', 'Garcia Salinaz', 'egs@edu.bo', '78454512', '$2y$10$ZkWjQlaTI6fN6G8dK1tvieppAbZFg5kkuAfNJwUPXNoIlPsDZ1/hy', 2, 1, NULL, NULL, '2026-09-20 21:53:58', '2026-09-20 21:54:57'),
(12, '14151617', 'Ana', 'Vaca Flores', 'avf@gmail.com', '78962214', '$2y$10$D4c/mNuX8a9A05hcwyPkHOzotrgds57Js/GomxoIjXW910pcWWqiW', 3, 1, NULL, NULL, '2026-09-21 03:23:58', '2026-09-21 03:23:58'),
(13, '123456798', 'Navarro', 'Navarro', 'adolfo@gmail.com', '1234567', '$2y$10$hikdIJP1IaX3FMG9JUxYxO1vkuoKN2rcvCN17ZJa/8X0XNNblx5Fi', 3, 1, NULL, NULL, '2026-09-22 14:10:22', '2026-09-22 14:10:22');

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
-- Indices de la tabla `plantillas_certificado`
--
ALTER TABLE `plantillas_certificado`
  ADD PRIMARY KEY (`id_plantilla`),
  ADD UNIQUE KEY `uq_plantilla_evento` (`id_evento`),
  ADD KEY `fk_plantilla_usuario` (`id_usuario_subio`);

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
  MODIFY `id_asistencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `auditoria_actividad`
--
ALTER TABLE `auditoria_actividad`
  MODIFY `id_auditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `evento_expositores`
--
ALTER TABLE `evento_expositores`
  MODIFY `id_evento_expositor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `materiales_evento`
--
ALTER TABLE `materiales_evento`
  MODIFY `id_material` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `plantillas_certificado`
--
ALTER TABLE `plantillas_certificado`
  MODIFY `id_plantilla` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `series_sesiones_evento`
--
ALTER TABLE `series_sesiones_evento`
  MODIFY `id_serie` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sesiones_evento`
--
ALTER TABLE `sesiones_evento`
  MODIFY `id_sesion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `solicitudes_reimpresion`
--
ALTER TABLE `solicitudes_reimpresion`
  MODIFY `id_solicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  MODIFY `id_tipo_evento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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

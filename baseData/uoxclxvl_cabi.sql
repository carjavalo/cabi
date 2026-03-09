-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-03-2026 a las 09:54:01
-- Versión del servidor: 11.4.10-MariaDB-cll-lve
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uoxclxvl_cabi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-carflore2001@yahoo.com|181.48.44.182', 'i:2;', 1772810320),
('laravel-cache-carflore2001@yahoo.com|181.48.44.182:timer', 'i:1772810320;', 1772810320),
('laravel-cache-carflorez2001@gmail.com|181.48.44.182', 'i:3;', 1772810176),
('laravel-cache-carflorez2001@gmail.com|181.48.44.182:timer', 'i:1772810176;', 1772810176),
('laravel-cache-carjavalosiste@gmail.com|190.6.160.83', 'i:1;', 1772716586),
('laravel-cache-carjavalosiste@gmail.com|190.6.160.83:timer', 'i:1772716586;', 1772716586),
('laravel-cache-facturacionhuv@gmail.com|181.48.44.182', 'i:2;', 1772809881),
('laravel-cache-facturacionhuv@gmail.com|181.48.44.182:timer', 'i:1772809881;', 1772809881),
('laravel-cache-gelen0509@hotmail.com|190.6.160.83', 'i:1;', 1772743192),
('laravel-cache-gelen0509@hotmail.com|190.6.160.83:timer', 'i:1772743192;', 1772743192);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `codigo` varchar(255) NOT NULL,
  `estructura` longtext DEFAULT NULL,
  `preguntas_count` int(11) NOT NULL DEFAULT 0,
  `enlace` varchar(255) DEFAULT NULL,
  `qr_path` varchar(255) DEFAULT NULL,
  `schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema`)),
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `permitir_repetir` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `titulo`, `descripcion`, `codigo`, `estructura`, `preguntas_count`, `enlace`, `qr_path`, `schema`, `activo`, `fecha_inicio`, `fecha_fin`, `permitir_repetir`, `created_by`, `created_at`, `updated_at`) VALUES
(7, 'Gym', NULL, '', '{\"questions\":[{\"type\":\"multiple\",\"title\":\"SIstes al GYM\",\"options\":[\"Si\",\"No\"],\"correctAnswers\":[0]},{\"type\":\"multiple\",\"title\":\"Con que frecuencia va por semana\",\"options\":[\"No voy\",\"Una ves a la Semana\",\"Dos veces a la Semana\",\"Minimo tres veces a la semana\",\"Todos los dias\"],\"correctAnswers\":[3,4]},{\"type\":\"multiple\",\"title\":\"VEndrias al GYm\",\"options\":[\"Si\",\"No\"],\"correctAnswers\":[0]}]}', 3, NULL, NULL, NULL, 1, '2026-02-27 17:00:00', '2026-03-01 21:00:00', 0, 1, '2026-02-27 22:35:42', '2026-02-28 02:59:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas_respuestas`
--

CREATE TABLE `encuestas_respuestas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `encuesta_id` bigint(20) UNSIGNED NOT NULL,
  `respuestas` text NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `usuario_nombre` varchar(255) DEFAULT NULL,
  `usuario_email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `encuestas_respuestas`
--

INSERT INTO `encuestas_respuestas` (`id`, `encuesta_id`, `respuestas`, `usuario_id`, `usuario_nombre`, `usuario_email`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(6, 7, '{\"SIstes al GYM\":[\"Si\"],\"Con que frecuencia va por semana\":[\"Minimo tres veces a la semana\",\"Todos los dias\"],\"VEndrias al GYm\":[\"Si\"]}', 1, 'Carlos Jairton', 'carjavalosistem@gmail.com', '192.168.151.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 23:19:49', '2026-02-27 23:19:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `capacidad_maxima` int(11) NOT NULL DEFAULT 0,
  `inscritos` int(11) NOT NULL DEFAULT 0,
  `max_inscripciones_dia` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_inscripciones_semana` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `dias_activos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dias_activos`)),
  `franjas_horarias` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`franjas_horarias`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `descripcion`, `ubicacion`, `fecha_inicio`, `fecha_fin`, `capacidad_maxima`, `inscritos`, `max_inscripciones_dia`, `max_inscripciones_semana`, `imagen`, `activo`, `dias_activos`, `franjas_horarias`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CAF', 'Realizar ejercicio', 'CENTRO DE ACONDICIONAMIENTO FISICO HUV', '2026-03-07 11:00:00', '2026-04-06 23:00:00', 14000, 101, 1, 5, NULL, 1, '[1,2,3,4,5]', '[{\"dia_semana\":1,\"hora_inicio\":\"07:00:00\",\"hora_fin\":\"08:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"08:00:00\",\"hora_fin\":\"09:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"09:00:00\",\"hora_fin\":\"10:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"10:00:00\",\"hora_fin\":\"11:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"11:00:00\",\"hora_fin\":\"12:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"12:00:00\",\"hora_fin\":\"13:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"13:00:00\",\"hora_fin\":\"14:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"15:00:00\",\"hora_fin\":\"16:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"16:00:00\",\"hora_fin\":\"17:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":1,\"hora_inicio\":\"17:00:00\",\"hora_fin\":\"18:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"07:00:00\",\"hora_fin\":\"08:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"08:00:00\",\"hora_fin\":\"09:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"09:00:00\",\"hora_fin\":\"10:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"10:00:00\",\"hora_fin\":\"11:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"11:00:00\",\"hora_fin\":\"12:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"12:00:00\",\"hora_fin\":\"13:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"13:00:00\",\"hora_fin\":\"14:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"15:00:00\",\"hora_fin\":\"16:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"16:00:00\",\"hora_fin\":\"17:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":2,\"hora_inicio\":\"17:00:00\",\"hora_fin\":\"18:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"07:00:00\",\"hora_fin\":\"08:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"08:00:00\",\"hora_fin\":\"09:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"09:00:00\",\"hora_fin\":\"10:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"10:00:00\",\"hora_fin\":\"11:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"11:00:00\",\"hora_fin\":\"12:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"12:00:00\",\"hora_fin\":\"13:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"13:00:00\",\"hora_fin\":\"14:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"15:00:00\",\"hora_fin\":\"16:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"16:00:00\",\"hora_fin\":\"17:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":3,\"hora_inicio\":\"17:00:00\",\"hora_fin\":\"18:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"07:00:00\",\"hora_fin\":\"08:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"08:00:00\",\"hora_fin\":\"09:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"09:00:00\",\"hora_fin\":\"10:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"10:00:00\",\"hora_fin\":\"11:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"11:00:00\",\"hora_fin\":\"12:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"12:00:00\",\"hora_fin\":\"13:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"13:00:00\",\"hora_fin\":\"14:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"15:00:00\",\"hora_fin\":\"16:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":4,\"hora_inicio\":\"16:30:00\",\"hora_fin\":\"17:30:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"07:00:00\",\"hora_fin\":\"08:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"08:00:00\",\"hora_fin\":\"09:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"09:00:00\",\"hora_fin\":\"10:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"10:00:00\",\"hora_fin\":\"11:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"11:00:00\",\"hora_fin\":\"12:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"12:00:00\",\"hora_fin\":\"13:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"13:00:00\",\"hora_fin\":\"14:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"15:00:00\",\"hora_fin\":\"16:00:00\",\"capacidad_maxima\":20},{\"dia_semana\":5,\"hora_inicio\":\"16:30:00\",\"hora_fin\":\"17:30:00\",\"capacidad_maxima\":20}]', NULL, '2026-03-02 20:12:36', '2026-03-09 12:17:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_dias`
--

CREATE TABLE `evento_dias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `dia_semana` int(11) NOT NULL COMMENT '0=Domingo, 1=Lunes, etc.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_dias`
--

INSERT INTO `evento_dias` (`id`, `evento_id`, `dia_semana`, `created_at`, `updated_at`) VALUES
(103, 1, 1, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(104, 1, 2, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(105, 1, 3, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(106, 1, 4, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(107, 1, 5, '2026-03-03 19:21:24', '2026-03-03 19:21:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_franjas`
--

CREATE TABLE `evento_franjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `dia_semana` int(11) DEFAULT NULL COMMENT '0=Domingo, 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `capacidad_maxima` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_franjas`
--

INSERT INTO `evento_franjas` (`id`, `evento_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `capacidad_maxima`, `created_at`, `updated_at`) VALUES
(496, 1, 1, '07:00:00', '08:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(497, 1, 1, '08:00:00', '09:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(498, 1, 1, '09:00:00', '10:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(499, 1, 1, '10:00:00', '11:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(500, 1, 1, '11:00:00', '12:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(501, 1, 1, '12:00:00', '13:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(502, 1, 1, '13:00:00', '14:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(503, 1, 1, '15:00:00', '16:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(504, 1, 1, '16:00:00', '17:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(505, 1, 1, '17:00:00', '18:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(506, 1, 2, '07:00:00', '08:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(507, 1, 2, '08:00:00', '09:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(508, 1, 2, '09:00:00', '10:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(509, 1, 2, '10:00:00', '11:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(510, 1, 2, '11:00:00', '12:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(511, 1, 2, '12:00:00', '13:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(512, 1, 2, '13:00:00', '14:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(513, 1, 2, '15:00:00', '16:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(514, 1, 2, '16:00:00', '17:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(515, 1, 2, '17:00:00', '18:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(516, 1, 3, '07:00:00', '08:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(517, 1, 3, '08:00:00', '09:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(518, 1, 3, '09:00:00', '10:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(519, 1, 3, '10:00:00', '11:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(520, 1, 3, '11:00:00', '12:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(521, 1, 3, '12:00:00', '13:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(522, 1, 3, '13:00:00', '14:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(523, 1, 3, '15:00:00', '16:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(524, 1, 3, '16:00:00', '17:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(525, 1, 3, '17:00:00', '18:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(526, 1, 4, '07:00:00', '08:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(527, 1, 4, '08:00:00', '09:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(528, 1, 4, '09:00:00', '10:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(529, 1, 4, '10:00:00', '11:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(530, 1, 4, '11:00:00', '12:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(531, 1, 4, '12:00:00', '13:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(532, 1, 4, '13:00:00', '14:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(533, 1, 4, '15:00:00', '16:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(534, 1, 4, '16:30:00', '17:30:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(535, 1, 5, '07:00:00', '08:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(536, 1, 5, '08:00:00', '09:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(537, 1, 5, '09:00:00', '10:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(538, 1, 5, '10:00:00', '11:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(539, 1, 5, '11:00:00', '12:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(540, 1, 5, '12:00:00', '13:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(541, 1, 5, '13:00:00', '14:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(542, 1, 5, '15:00:00', '16:00:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24'),
(543, 1, 5, '16:30:00', '17:30:00', 20, '2026-03-03 19:21:24', '2026-03-03 19:21:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_inscripciones`
--

CREATE TABLE `evento_inscripciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `evento_franja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `identificacion` varchar(255) DEFAULT NULL,
  `fecha_reserva` date DEFAULT NULL,
  `asistencia` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_inscripciones`
--

INSERT INTO `evento_inscripciones` (`id`, `evento_id`, `evento_franja_id`, `user_id`, `nombre_completo`, `identificacion`, `fecha_reserva`, `asistencia`, `created_at`, `updated_at`) VALUES
(9, 1, 501, NULL, 'Leidy Julieth Quiñones Valencia', '1143928663', '2026-03-09', 0, '2026-03-05 20:29:41', '2026-03-05 20:29:41'),
(10, 1, 501, NULL, 'Carmen Helena Gallego Melendez', '1112219177', '2026-03-09', 0, '2026-03-05 20:30:12', '2026-03-05 20:30:12'),
(11, 1, 511, NULL, 'Leidy Julieth Quiñones Valencia', '1143928663', '2026-03-10', 0, '2026-03-05 20:30:21', '2026-03-05 20:30:21'),
(12, 1, 521, NULL, 'Leidy Julieth Quiñones Valencia', '1143928663', '2026-03-11', 0, '2026-03-05 20:30:44', '2026-03-05 20:30:44'),
(13, 1, 501, NULL, 'JENNIFER HERRERA VEGA', '38569559', '2026-03-09', 0, '2026-03-05 20:30:50', '2026-03-05 20:30:50'),
(14, 1, 531, NULL, 'Leidy Julieth Quiñones Valencia', '1143928663', '2026-03-05', 0, '2026-03-05 20:30:57', '2026-03-05 20:30:57'),
(15, 1, 511, NULL, 'JENNIFER HERRERA VEGA', '38569559', '2026-03-10', 0, '2026-03-05 20:31:04', '2026-03-05 20:31:04'),
(16, 1, 540, NULL, 'Leidy Julieth Quiñones Valencia', '1143928663', '2026-03-06', 0, '2026-03-05 20:31:10', '2026-03-05 20:31:10'),
(17, 1, 521, NULL, 'JENNIFER HERRERA VEGA', '38569559', '2026-03-11', 0, '2026-03-05 20:31:20', '2026-03-05 20:31:20'),
(18, 1, 531, NULL, 'JENNIFER HERRERA VEGA', '38569559', '2026-03-05', 0, '2026-03-05 20:32:02', '2026-03-05 20:32:02'),
(19, 1, 540, NULL, 'JENNIFER HERRERA VEGA', '38569559', '2026-03-06', 0, '2026-03-05 20:32:16', '2026-03-05 20:32:16'),
(20, 1, 501, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-09', 0, '2026-03-05 20:34:02', '2026-03-05 20:34:02'),
(21, 1, 511, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-10', 0, '2026-03-05 20:34:15', '2026-03-05 20:34:15'),
(22, 1, 521, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-11', 0, '2026-03-05 20:34:24', '2026-03-05 20:34:24'),
(23, 1, 531, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-05', 0, '2026-03-05 20:34:33', '2026-03-05 20:34:33'),
(24, 1, 540, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-06', 0, '2026-03-05 20:34:42', '2026-03-05 20:34:42'),
(25, 1, 501, NULL, 'IVANA ANDREA HERNANDEZ MONTAÑO', '1111744281', '2026-03-09', 0, '2026-03-05 20:40:14', '2026-03-05 20:40:14'),
(26, 1, 540, NULL, 'Carmen Helena Gallego Melendez', '1112219177', '2026-03-06', 0, '2026-03-05 20:40:15', '2026-03-05 20:40:15'),
(27, 1, 511, NULL, 'IVANA ANDREA HERNANDEZ MONTAÑO', '1111744281', '2026-03-10', 0, '2026-03-05 20:40:31', '2026-03-05 20:40:31'),
(28, 1, 521, NULL, 'IVANA ANDREA HERNANDEZ MONTAÑO', '1111744281', '2026-03-11', 0, '2026-03-05 20:41:03', '2026-03-05 20:41:03'),
(29, 1, 531, NULL, 'IVANA ANDREA HERNANDEZ MONTAÑO', '1111744281', '2026-03-05', 0, '2026-03-05 20:41:48', '2026-03-05 20:41:48'),
(30, 1, 540, NULL, 'IVANA ANDREA HERNANDEZ MONTAÑO', '1111744281', '2026-03-06', 0, '2026-03-05 20:42:07', '2026-03-05 20:42:07'),
(31, 1, 501, NULL, 'FARAH CORTAZAR BOLAÑOS', '29123010', '2026-03-09', 0, '2026-03-05 20:54:59', '2026-03-05 20:54:59'),
(32, 1, 511, NULL, 'FARAH CORTAZAR BOLAÑOS', '29123010', '2026-03-10', 0, '2026-03-05 20:55:09', '2026-03-05 20:55:09'),
(33, 1, 531, NULL, 'FARAH CORTAZAR BOLAÑOS', '29123010', '2026-03-05', 0, '2026-03-05 20:55:23', '2026-03-05 20:55:23'),
(34, 1, 540, 18, 'Anderson RODRIGUEZ VELASCO', '14678160', '2026-03-06', 0, '2026-03-05 20:56:21', '2026-03-05 20:56:21'),
(35, 1, 501, 18, 'Anderson RODRIGUEZ VELASCO', '14678160', '2026-03-09', 0, '2026-03-05 20:56:44', '2026-03-05 20:56:44'),
(36, 1, 534, 16, 'DENNYS VANESSA RAMIREZ FERNANDEZ', '1151957794', '2026-03-05', 0, '2026-03-05 20:56:45', '2026-03-05 20:56:45'),
(37, 1, 534, 19, 'Juan Manuel Roa Chavez', '16462936', '2026-03-05', 0, '2026-03-05 21:14:01', '2026-03-05 21:14:01'),
(38, 1, 543, 20, 'Kely Andrea Salazar Duran', '1126586574', '2026-03-06', 0, '2026-03-05 21:21:47', '2026-03-05 21:21:47'),
(39, 1, 543, 21, 'Laura Sophia Ospina Rodríguez', '1006109680', '2026-03-06', 0, '2026-03-05 21:21:51', '2026-03-05 21:21:51'),
(40, 1, 501, 23, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-09', 0, '2026-03-05 21:34:20', '2026-03-05 21:34:20'),
(41, 1, 511, 23, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-10', 0, '2026-03-05 21:34:41', '2026-03-05 21:34:41'),
(42, 1, 521, 23, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-11', 0, '2026-03-05 21:34:59', '2026-03-05 21:34:59'),
(43, 1, 531, 23, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-05', 0, '2026-03-05 21:35:21', '2026-03-05 21:35:21'),
(44, 1, 540, 23, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-06', 0, '2026-03-05 21:35:38', '2026-03-05 21:35:38'),
(45, 1, 543, 22, 'Erika Janeth Martínez Hernandez', '38553054', '2026-03-06', 0, '2026-03-05 21:37:00', '2026-03-05 21:37:00'),
(48, 1, 511, 11, 'Daniela Samboni Pacheco', '1192924706', '2026-03-10', 0, '2026-03-05 23:02:45', '2026-03-05 23:02:45'),
(50, 1, 521, 11, 'Daniela Samboni Pacheco', '1192924706', '2026-03-11', 0, '2026-03-05 23:06:43', '2026-03-05 23:06:43'),
(51, 1, 531, 11, 'Daniela Samboni Pacheco', '1192924706', '2026-03-05', 0, '2026-03-05 23:06:59', '2026-03-05 23:06:59'),
(52, 1, 540, 11, 'Daniela Samboni Pacheco', '1192924706', '2026-03-06', 0, '2026-03-05 23:07:23', '2026-03-05 23:07:23'),
(56, 1, 531, NULL, 'Carmen Helena Gallego Melendez', '1112219177', '2026-03-12', 0, '2026-03-06 12:19:37', '2026-03-06 12:19:37'),
(57, 1, 521, NULL, 'Carmen Helena Gallego Melendez', '1112219177', '2026-03-11', 0, '2026-03-06 12:21:13', '2026-03-06 12:21:13'),
(58, 1, 511, NULL, 'Carmen Helena Gallego Melendez', '1112219177', '2026-03-10', 0, '2026-03-06 12:22:04', '2026-03-06 12:22:04'),
(60, 1, 511, 24, 'GLORIA MILDRED MORENO CHARRIA', '66773018', '2026-03-10', 0, '2026-03-06 13:58:33', '2026-03-06 13:58:33'),
(61, 1, 521, 24, 'GLORIA MILDRED MORENO CHARRIA', '66773018', '2026-03-11', 0, '2026-03-06 14:00:01', '2026-03-06 14:00:01'),
(62, 1, 531, 24, 'GLORIA MILDRED MORENO CHARRIA', '66773018', '2026-03-12', 0, '2026-03-06 14:00:14', '2026-03-06 14:00:14'),
(63, 1, 540, 24, 'GLORIA MILDRED MORENO CHARRIA', '66773018', '2026-03-06', 0, '2026-03-06 14:00:49', '2026-03-06 14:00:49'),
(64, 1, 501, 24, 'GLORIA MILDRED MORENO CHARRIA', '66773018', '2026-03-09', 0, '2026-03-06 14:06:35', '2026-03-06 14:06:35'),
(65, 1, 501, 32, 'EDWIN FERNANDO TRUJILLO LARGO', '6384226', '2026-03-09', 0, '2026-03-06 14:54:21', '2026-03-06 14:54:21'),
(66, 1, 511, 32, 'EDWIN FERNANDO TRUJILLO LARGO', '6384226', '2026-03-10', 0, '2026-03-06 14:54:33', '2026-03-06 14:54:33'),
(67, 1, 521, 32, 'EDWIN FERNANDO TRUJILLO LARGO', '6384226', '2026-03-11', 0, '2026-03-06 14:54:43', '2026-03-06 14:54:43'),
(68, 1, 531, 32, 'EDWIN FERNANDO TRUJILLO LARGO', '6384226', '2026-03-12', 0, '2026-03-06 14:54:53', '2026-03-06 14:54:53'),
(69, 1, 540, 32, 'EDWIN FERNANDO TRUJILLO LARGO', '6384226', '2026-03-06', 0, '2026-03-06 14:55:05', '2026-03-06 14:55:05'),
(70, 1, 505, 2, 'Melany Alessandra Bolaños', '1109543008', '2026-03-09', 0, '2026-03-06 14:58:06', '2026-03-06 14:58:06'),
(71, 1, 515, 2, 'Melany Alessandra Bolaños', '1109543008', '2026-03-10', 0, '2026-03-06 14:58:23', '2026-03-06 14:58:23'),
(72, 1, 525, 2, 'Melany Alessandra Bolaños', '1109543008', '2026-03-11', 0, '2026-03-06 14:58:36', '2026-03-06 14:58:36'),
(73, 1, 534, 2, 'Melany Alessandra Bolaños', '1109543008', '2026-03-12', 0, '2026-03-06 14:58:49', '2026-03-06 14:58:49'),
(74, 1, 543, 2, 'Melany Alessandra Bolaños', '1109543008', '2026-03-06', 0, '2026-03-06 14:58:58', '2026-03-06 14:58:58'),
(75, 1, 505, 37, 'Jessica Grajales Osorio', '1143832293', '2026-03-09', 0, '2026-03-06 15:38:51', '2026-03-06 15:38:51'),
(76, 1, 500, 38, 'Alejandra López Quintero', '1107518024', '2026-03-09', 0, '2026-03-06 15:41:54', '2026-03-06 15:41:54'),
(77, 1, 531, NULL, 'FARAH CORTAZAR BOLAÑOS', '29123010', '2026-03-12', 0, '2026-03-06 16:05:20', '2026-03-06 16:05:20'),
(78, 1, 500, NULL, 'María Fernanda Morales Valdés', '31414851', '2026-03-09', 0, '2026-03-06 16:09:09', '2026-03-06 16:09:09'),
(79, 1, 505, NULL, 'Santiago Cruz Giraldo', '1144105828', '2026-03-09', 0, '2026-03-06 16:09:16', '2026-03-06 16:09:16'),
(80, 1, 531, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-12', 0, '2026-03-06 16:09:22', '2026-03-06 16:09:22'),
(81, 1, 515, NULL, 'Santiago Cruz Giraldo', '1144105828', '2026-03-10', 0, '2026-03-06 16:09:37', '2026-03-06 16:09:37'),
(82, 1, 512, NULL, 'María Fernanda Morales Valdés', '31414851', '2026-03-10', 0, '2026-03-06 16:09:46', '2026-03-06 16:09:46'),
(83, 1, 525, NULL, 'Santiago Cruz Giraldo', '1144105828', '2026-03-11', 0, '2026-03-06 16:09:50', '2026-03-06 16:09:50'),
(84, 1, 534, NULL, 'Santiago Cruz Giraldo', '1144105828', '2026-03-12', 0, '2026-03-06 16:10:06', '2026-03-06 16:10:06'),
(85, 1, 543, NULL, 'Santiago Cruz Giraldo', '1144105828', '2026-03-06', 0, '2026-03-06 16:10:18', '2026-03-06 16:10:18'),
(86, 1, 518, NULL, 'María Fernanda Morales Valdés', '31414851', '2026-03-11', 0, '2026-03-06 16:11:02', '2026-03-06 16:11:02'),
(87, 1, 530, NULL, 'María Fernanda Morales Valdés', '31414851', '2026-03-12', 0, '2026-03-06 16:11:22', '2026-03-06 16:11:22'),
(88, 1, 543, NULL, 'María Fernanda Morales Valdés', '31414851', '2026-03-06', 0, '2026-03-06 16:12:21', '2026-03-06 16:12:21'),
(89, 1, 502, NULL, 'Lina Marcela Garcia Velasco', '1151939313', '2026-03-09', 0, '2026-03-06 21:58:19', '2026-03-06 21:58:19'),
(90, 1, 502, NULL, 'Angie Trochez', '1005783664', '2026-03-09', 0, '2026-03-06 21:58:42', '2026-03-06 21:58:42'),
(91, 1, 515, NULL, 'Alejandra López Quintero', '1107518024', '2026-03-10', 0, '2026-03-06 22:01:53', '2026-03-06 22:01:53'),
(92, 1, 515, 30, 'Lina Marcela Garcia Velasco', '1151939313', '2026-03-10', 0, '2026-03-06 22:01:55', '2026-03-06 22:01:55'),
(93, 1, 515, 30, 'Angie Trochez', '1005783664', '2026-03-10', 0, '2026-03-06 22:02:07', '2026-03-06 22:02:07'),
(94, 1, 525, NULL, 'Alejandra López Quintero', '1107518024', '2026-03-11', 0, '2026-03-06 22:02:22', '2026-03-06 22:02:22'),
(95, 1, 534, 30, 'Lina Marcela Garcia Velasco', '1151939313', '2026-03-12', 0, '2026-03-06 22:03:04', '2026-03-06 22:03:04'),
(96, 1, 534, NULL, 'Alejandra López Quintero', '1107518024', '2026-03-12', 0, '2026-03-06 22:03:11', '2026-03-06 22:03:11'),
(97, 1, 534, 30, 'Angie Trochez', '1005783664', '2026-03-12', 0, '2026-03-06 22:03:18', '2026-03-06 22:03:18'),
(98, 1, 540, NULL, 'Alejandra López Quintero', '1107518024', '2026-03-06', 0, '2026-03-06 22:04:11', '2026-03-06 22:04:11'),
(99, 1, 541, 30, 'Angie Trochez', '1005783664', '2026-03-06', 0, '2026-03-06 22:04:49', '2026-03-06 22:04:49'),
(100, 1, 541, 30, 'Lina Marcela Garcia Velasco', '1151939313', '2026-03-06', 0, '2026-03-06 22:05:20', '2026-03-06 22:05:20'),
(101, 1, 501, 11, 'Daniela Jiménez Orozco', '1144065090', '2026-03-09', 0, '2026-03-07 11:22:31', '2026-03-07 11:22:31'),
(102, 1, 511, 11, 'Daniela Jiménez Orozco', '1144065090', '2026-03-10', 0, '2026-03-07 11:22:44', '2026-03-07 11:22:44'),
(103, 1, 521, 11, 'Daniela Jiménez Orozco', '1144065090', '2026-03-11', 0, '2026-03-07 11:23:02', '2026-03-07 11:23:02'),
(104, 1, 531, 11, 'Daniela Jiménez Orozco', '1144065090', '2026-03-12', 0, '2026-03-07 11:23:15', '2026-03-07 11:23:15'),
(105, 1, 540, 11, 'Daniela Jiménez Orozco', '1144065090', '2026-03-13', 0, '2026-03-07 11:23:29', '2026-03-07 11:23:29'),
(106, 1, 501, 11, 'Juliana Cortes Diaz', '1113303584', '2026-03-09', 0, '2026-03-07 11:23:41', '2026-03-07 11:23:41'),
(107, 1, 511, 11, 'Juliana Cortes Diaz', '1113303584', '2026-03-10', 0, '2026-03-07 11:24:00', '2026-03-07 11:24:00'),
(108, 1, 521, 11, 'Juliana Cortes Diaz', '1113303584', '2026-03-11', 0, '2026-03-07 11:24:13', '2026-03-07 11:24:13'),
(109, 1, 531, 11, 'Juliana Cortes Diaz', '1113303584', '2026-03-12', 0, '2026-03-07 11:24:26', '2026-03-07 11:24:26'),
(110, 1, 540, 11, 'Juliana Cortes Diaz', '1113303584', '2026-03-13', 0, '2026-03-07 11:24:38', '2026-03-07 11:24:38'),
(111, 1, 540, NULL, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-13', 0, '2026-03-07 12:40:03', '2026-03-07 12:40:03'),
(112, 1, 531, NULL, 'Yessica Lorena Torres Espinosa', '1144052106', '2026-03-12', 0, '2026-03-07 12:40:16', '2026-03-07 12:40:16'),
(113, 1, 540, NULL, 'LEYDI VANESSA HERRERA OSORIO', '1143836044', '2026-03-13', 0, '2026-03-09 11:18:56', '2026-03-09 11:18:56'),
(115, 1, 505, 2, 'carmen Arboleda Florez', '66855065', '2026-03-09', 0, '2026-03-09 12:17:16', '2026-03-09 12:17:16'),
(116, 1, 515, 2, 'carmen Arboleda Florez', '66855065', '2026-03-10', 0, '2026-03-09 12:17:32', '2026-03-09 12:17:32'),
(117, 1, 534, 2, 'carmen Arboleda Florez', '66855065', '2026-03-12', 0, '2026-03-09 12:17:59', '2026-03-09 12:17:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horariosgim`
--

CREATE TABLE `horariosgim` (
  `Identificacion` varchar(50) NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL,
  `Dia` varchar(100) NOT NULL,
  `Horario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripgym`
--

CREATE TABLE `inscripgym` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `primer_apellido` varchar(150) NOT NULL,
  `segundo_apellido` varchar(150) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `tipo_vinculacion` varchar(150) NOT NULL,
  `servicio_unidad` varchar(150) NOT NULL,
  `contacto_emergencia` varchar(150) NOT NULL,
  `correolec` varchar(150) DEFAULT NULL,
  `identificacion` varchar(255) DEFAULT NULL,
  `autorizado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripgym`
--

INSERT INTO `inscripgym` (`id`, `nombres`, `primer_apellido`, `segundo_apellido`, `edad`, `celular`, `tipo_vinculacion`, `servicio_unidad`, `contacto_emergencia`, `correolec`, `identificacion`, `autorizado`) VALUES
(3, 'CARLOS JAIRTON ', 'VALDERRAMA', 'OROBIO', NULL, '3123003123', 'Agesoc', 'Gestion de la Informacion', 'KEVIN HAVARRO 3123003123', 'UNO@CORREO.COM', '94529372', 0),
(10, 'Santiago', 'Cruz', 'Giraldo', 27, '3153492895', 'Agesoc', 'Gestion de la Informacion', 'Erika V. Cruz 3157523875', 'scruzg124@gmail.com', '1144105828', 1),
(11, 'NATALIA', 'Vallecilla', 'Gutierrez', 29, '3162723769', 'Asstracud', 'Talento Humano', 'Alvaro Andrade 3158012316', 'nativalle28@gmail.com', '1116274570', 1),
(12, 'Leidy Julieth', 'Quiñones', 'Valencia', 36, '3113676920', 'Agesoc', 'Talento Humano', '3155751486', 'lquinoneshuv@gmail.com', '1143928663', 1),
(13, 'JENNIFER', 'HERRERA', 'VEGA', 41, '3185936684', 'Agesoc', 'Talento Humano', '3176713477', 'tjvega@hotmail.com', '38569559', 1),
(14, 'Daniela', 'Jiménez', 'Orozco', 32, '3017713777', 'Agesoc', 'Oficina Asesora Gestión de Calidad', 'Maria Isabel Orozco', 'dani.jimenez0916@gmail.com', '1144065090', 1),
(15, 'IVANA ANDREA', 'HERNANDEZ', 'MONTAÑO', 25, '3150089648', 'Agesoc', 'Talento Humano', 'LUCY ELIZABETH 3122150701', 'IVANAHERNANDEZ2819@GMAIL.COM', '1111744281', 1),
(16, 'Carmen Helena', 'Gallego', 'Melendez', 39, '3217278286', 'Agesoc', 'Talento Humano', 'Julieth Gallego', 'gelen0509@hotmail.com', '1112219177', 1),
(17, 'Daniela', 'Samboni', 'Pacheco', NULL, '3157060061', 'Agesoc', 'Oficina Asesora Gestión de Calidad', '3013329278', 'dani_samboni@hotmail.com', '1192924706', 1),
(18, 'LEYDI VANESSA', 'HERRERA', 'OSORIO', 34, '3176763882', 'Agesoc', 'Facturación', 'LEDVIA OSORIO 3172689350', 'va.nee.o@hotmail.com', '1143836044', 1),
(19, 'DENNYS VANESSA', 'RAMIREZ', 'FERNANDEZ', 30, '3045625677', 'Asstracud', 'Sala Medica Mujeres', 'RUBEN RAMIREZ 3184258077', 'vanesita-1606@hotmail.com', '1151957794', 1),
(20, 'FARAH', 'CORTAZAR', 'BOLAÑOS', 46, '3152139217', 'Agesoc', 'Facturación', 'MARIO OLIVEROS 3156261533', 'coordfacturacionuci@gmail.com', '29123010', 1),
(21, 'María Isabel', 'Ambuila', 'Ambuila', 31, '3146475185', 'Planta', 'Jefatura oficina gerencia servicios de salud', 'Wilman Ambuila 3136766796', 'mariaisabel-1994@hotmail.com', '1112482909', 1),
(22, 'Anderson', 'RODRIGUEZ', 'VELASCO', 41, '3217618081', 'Asstracud', 'Epidemiología', 'MARCELA RODRIGUEZ CEL 3127731608', 'estadisticasvitales@correohuv.gov.co', '14678160', 1),
(23, 'Juan Manuel', 'Roa', 'Chavez', 42, '3053316567', 'Agesoc', 'Quirófano Central', 'María ospina', 'roajuan82@gmail.com', '16462936', 1),
(24, 'Laura Sophia', 'Ospina', 'Rodríguez', 22, '3225122369', '', '', 'Jhon Ospina: 3175615917', 'sophialauospina@gmail.com', '1006109680', 1),
(25, 'Kely Andrea', 'Salazar', 'Duran', 38, '3046436947', 'Asstracud', 'Coordinación Servicios ambulatorios', 'Wilson Ramírez 3108459641', 'kellysala15@gmail.com', '1126586574', 1),
(26, 'Erika Janeth', 'Martínez', 'Hernandez', 44, '3122840526', 'Asstracud', 'Coordinación de Psicología', '311 2710043', 'eriksakae@gmail.com', '38553054', 1),
(27, 'Yessica Lorena', 'Torres', 'Espinosa', 33, '3023743667', 'Asstracud', 'Sala Cirugía Pediátrica Ana Frank', 'José Reyber 3147106308', 'yessitorrese@gmail.com', '1144052106', 1),
(28, 'Gloria Mildred', 'Moreno', 'Charria', 51, '3182988184', 'Planta', 'Oficina Gerencia  General', 'Daniela Marin 3158733849', 'gloriamorenohsjd@gmail.com', '67773018', 0),
(29, 'Marileth', 'Urdinola', 'Gonzalez', 42, '3174087094', 'Asstracud', 'Oficina Asesora Gestión de Calidad', '3017713777', 'marileth20@gmail.com', '29314496', 0),
(30, 'Juliana', 'Cortes', 'Diaz', 37, '3182745910', 'Agesoc', 'Oficina Asesora Gestión de Calidad', 'Mario santa', 'julita0527@gmail.com', '1113303584', 1),
(31, 'María Fernanda', 'Morales', 'Valdés', 56, '3166911667', 'Planta', 'Atención Al Usuario', '3155567988', 'capacitacionvirtualhuv@gmail.com', '31414851', 1),
(32, 'viviana', 'valencia', 'valencia', 43, '3053658294', 'Asstracud', 'Sala cirugía pediátrica Ana Frank', 'Kevin mesa 310 6078062', 'vivi.vvv1983@gmail.com', '38602524', 1),
(33, 'Angie', 'Trochez', '', 26, '3105283045', 'Asstracud', 'Programa De Atención Domiciliaria', 'Marcela Garcia', 'marcelagarcia0711@gmail.com', '1005783664', 1),
(34, 'sergio leonardo', 'gutierrez', 'rojas', 41, '3162781755', 'Agesoc', 'Servicio De Alimentación (cocina y repostero)', 'jhoana villano', 'jhoa_61@hotmail.com', '1127954836', 1),
(36, 'Lina Marcela', 'Garcia', 'Velasco', 34, '3223596786', 'Asstracud', 'Coordinación Servicios ambulatorios', '3105283042', 'marcelagarciamd7@gmail.com', '1151939313', 1),
(37, 'SONIA', 'HERRERA', 'CARDONA', 64, '3113402719', 'Planta', 'Gestion de la Informacion', '3128873123', 'sohecar@gmail.com', '30724289', 1),
(38, 'GLORIA MILDRED', 'MORENO', 'CHARRIA', 51, '3182988184', 'Planta', 'Oficina Gerencia  General', 'DANIELA MARIN 3158733849', NULL, '66773018', 1),
(39, 'EDWIN FERNANDO', 'TRUJILLO', 'LARGO', 47, '3015723204', 'Agesoc', 'Compras', 'EDWAN FELIPE TRUJILLO 3022360877', 'edwinfernandotrujillolargo@gmail.com', '6384226', 1),
(40, 'Melany Alessandra', 'Bolaños', '', 20, '3043184970', 'Asstracud', 'Talento Humano', 'Oscar Bolaños 3175342376', 'bolanosalexandra528@gmail.com', '1109543008', 1),
(41, 'carmen', 'Arboleda', 'Florez', 53, '3128852227', 'Planta', 'Terapia Intensiva  (UCI)', '3182608424', 'facturacionucihuv@gmail.com', '66855065', 1),
(42, 'Jessica', 'Grajales', 'Osorio', 35, '3226336149', 'Agesoc', 'Glosas', 'Mauricio 3188214038', 'sisigrata@hotmail.com', '1143832293', 1),
(43, 'Alejandra', 'López', 'Quintero', 27, '3015505267', 'Asstracud', 'Coordinación Servicios ambulatorios', 'Nury Quintero 3192094009', 'alopezquintero36@gmail.com', '1107518024', 1),
(44, 'Nataly', 'Duque', 'Davalos', 30, '3128788937', 'Agesoc', 'Oficina De Apoyo Diagnostico Terapéutico', '3137970476', 'natalyduque924@gmail.com', '1107096202', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_24_000001_create_horariosgim_table', 2),
(5, '2026_02_25_000002_create_inscripgym_table', 3),
(6, '2026_02_25_000003_add_id_to_gym_tables_if_missing', 3),
(7, '2026_02_25_000004_convert_inscripgym_pk_to_id', 3),
(8, '2026_02_25_000005_safe_add_id_and_make_primary_inscripgym', 3),
(9, '2026_02_25_000006_safe_add_id_and_make_primary_horariosgim', 3),
(10, '2026_02_25_000007_force_auto_increment_ids', 4),
(11, '2026_02_25_000008_drop_redundant_columns_from_horariosgim', 5),
(12, '2026_02_25_000009_drop_apellidos_variants_horariosgim', 6),
(13, '2026_02_25_000010_add_correolec_to_inscripgym', 7),
(14, '2026_02_25_000011_add_unique_constraints_to_inscripgym', 8),
(15, '2026_02_25_000012_add_unique_identificacion_to_horariosgim', 9),
(16, '2026_02_26_000001_add_profile_fields_to_users', 10),
(17, '2026_02_26_000010_add_profile_fields_to_users', 11),
(18, '2026_02_26_000011_drop_name_and_role_from_users', 12),
(19, '2026_02_26_000011_ensure_role_in_users', 13),
(20, '2026_02_26_000020_create_servicios_table', 14),
(21, '2026_02_26_000021_create_vinculaciones_table', 15),
(22, '2026_02_26_000022_add_servicio_id_to_users', 16),
(23, '2026_02_26_000023_add_tipo_vinculacion_id_to_users', 17),
(24, '2026_02_26_000024_add_name_to_users', 18),
(25, '2026_02_26_000025_drop_tipo_from_servicios_vinculaciones', 19),
(26, '2026_02_26_000100_create_publicidad_table', 20),
(27, '2026_02_27_000001_create_encuestas_table', 21),
(28, '2026_02_27_000002_create_repencuestas_table', 21),
(29, '2026_02_27_000003_add_schema_to_encuestas_table', 22),
(30, '2026_02_27_000001_add_fields_to_encuestas_table', 23),
(31, '2026_02_27_161833_create_encuestas_respuestas_table', 24),
(32, '2026_02_27_164648_add_permitir_repetir_to_encuestas_table', 25),
(33, '2026_02_27_182701_rename_inscripgym_columns', 26),
(34, '2026_02_27_183009_make_edad_nullable_in_inscripgym', 27),
(35, '2026_02_28_013810_add_additional_fields_to_publicidad_table', 28),
(36, '2026_02_28_015555_change_fecha_columns_to_datetime_in_publicidad', 29),
(37, '2026_02_27_210350_add_fecha_fields_to_encuestas_table', 30),
(38, '2026_03_02_075611_create_eventos_table', 31),
(39, '2026_03_02_080556_add_franjas_horarias_to_eventos_table', 32),
(40, '2026_03_02_132600_create_evento_relational_tables', 33),
(41, '2026_03_03_084946_add_autorizado_to_inscripgym_table', 34),
(42, '2026_03_03_100000_add_dia_semana_to_evento_franjas', 35),
(43, '2026_03_04_100000_add_limite_inscripciones_to_eventos_table', 36);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicidad`
--

CREATE TABLE `publicidad` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `seccion_titulo` varchar(255) DEFAULT NULL,
  `seccion_subtitulo` varchar(255) DEFAULT NULL,
  `prioridad` tinyint(4) NOT NULL DEFAULT 3,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `publicidad`
--

INSERT INTO `publicidad` (`id`, `titulo`, `tag`, `descripcion`, `banner`, `link`, `seccion_titulo`, `seccion_subtitulo`, `prioridad`, `fecha_inicio`, `fecha_fin`, `activo`, `created_at`, `updated_at`) VALUES
(4, 'Inscríbete al Centro De Acondicionamiento Físico - CAF', NULL, '\"Haz del deporte un hábito, no una excepción\"', 'http://cabi.huv.gov.co/img/publicidad/1772552883_WhatsAppImage2026-02-20at9.54.50AM.jpeg', 'http://cabi.huv.gov.co/bienestar/gym/inscripcion', NULL, NULL, 3, '2026-02-28 11:00:00', '2026-04-01 11:00:00', 1, '2026-02-27 02:13:12', '2026-03-05 14:53:49'),
(7, 'Grupo WhatsApp - CAF', 'Comunicación', 'Accede al grupo de WhatsApp del caf', 'http://cabi.huv.gov.co/img/publicidad/1772830675_WhatsApp_icon.png', 'https://chat.whatsapp.com/CVPZcg8LnWUKuFvpQsCKeh', NULL, NULL, 3, '2026-03-06 16:00:00', '2027-12-31 02:38:00', 1, '2026-03-06 16:40:21', '2026-03-06 21:03:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repencuestas`
--

CREATE TABLE `repencuestas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `encuesta_id` bigint(20) UNSIGNED NOT NULL,
  `respuestas` text DEFAULT NULL,
  `respondente` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Gestion de la Informacion', '2026-02-26 22:18:58', '2026-02-26 22:18:58'),
(2, 'Pic', '2026-02-26 23:34:03', '2026-02-27 00:10:57'),
(4, 'Oficina Gerencia  General', '2026-02-27 00:45:24', '2026-02-27 00:50:26'),
(5, 'Departamento de Investigación', '2026-02-27 00:45:32', '2026-02-27 00:48:11'),
(6, 'Presupuesto, contabilidad y Costos', '2026-02-27 00:45:40', '2026-02-27 00:48:35'),
(7, 'Control interno', '2026-02-27 00:45:51', '2026-02-27 00:45:51'),
(8, 'Control Interno Disciplinario', '2026-02-27 00:47:21', '2026-02-27 00:48:27'),
(9, 'Oficina Asesora Gestión de Calidad', '2026-02-27 00:49:37', '2026-02-27 00:49:37'),
(10, 'Oficina Asesoría Jurídica', '2026-02-27 00:49:53', '2026-02-27 00:49:53'),
(11, 'Tesoreria General', '2026-02-27 00:50:10', '2026-02-27 00:50:10'),
(12, 'Coordinación Servicios ambulatorios', '2026-02-27 00:50:42', '2026-02-27 00:50:42'),
(13, 'Sala cirugía pediátrica Ana Frank', '2026-02-27 00:51:00', '2026-02-27 00:51:00'),
(14, 'U.c.i. Cipaf pediatría Ana Frank', '2026-02-27 00:51:19', '2026-02-27 00:51:19'),
(15, 'Hemato Oncología hospitalización Adulto', '2026-02-27 00:56:50', '2026-02-27 00:56:50'),
(16, 'Unid. Recién nacidos (Cirena)', '2026-02-27 00:57:10', '2026-02-27 00:57:10'),
(17, 'Unidad De Quemados', '2026-02-27 00:57:26', '2026-02-27 00:57:26'),
(18, 'Terapia Intensiva  (UCI)', '2026-02-27 00:57:45', '2026-02-27 00:57:45'),
(19, 'Quirófano Central', '2026-02-27 00:58:06', '2026-02-27 00:58:06'),
(20, 'Sala Medica Mujeres', '2026-02-27 00:58:30', '2026-02-27 00:58:30'),
(21, 'Sala de Ortopedia', '2026-02-27 01:00:44', '2026-02-27 01:00:44'),
(22, 'Sala Cirugía Pediátrica Ana Frank', '2026-02-27 01:01:06', '2026-02-27 01:01:06'),
(23, 'Unidad De Trauma y Reanimación', '2026-02-27 01:01:29', '2026-02-27 01:01:29'),
(24, 'Admisión y Sala de Partos', '2026-02-27 01:01:58', '2026-02-27 01:01:58'),
(25, 'Unidad De Diagnóstico Perinatal', '2026-02-27 01:02:27', '2026-02-27 01:02:27'),
(26, 'Pediatría (Consulta Externa)', '2026-02-27 01:02:46', '2026-02-27 01:02:46'),
(27, 'Sala Medica Hombres', '2026-02-27 01:03:03', '2026-02-27 01:03:03'),
(28, 'Medicina Interna Urgencias', '2026-02-27 23:39:49', '2026-02-27 23:39:49'),
(29, 'Urulogia', '2026-02-27 23:41:46', '2026-02-27 23:41:46'),
(30, 'Laboratorio de patología', '2026-02-27 23:42:15', '2026-02-27 23:42:15'),
(31, 'Mantenimiento mecánico', '2026-02-27 23:45:51', '2026-02-27 23:45:51'),
(33, 'Trabajo social', '2026-02-27 23:57:54', '2026-02-27 23:57:54'),
(34, 'Consultorios De Urgencias', '2026-02-27 23:58:16', '2026-02-27 23:58:16'),
(35, 'Docencia Asistencial y Extensión', '2026-02-28 00:02:50', '2026-02-28 00:02:50'),
(36, 'Programa De Atención Domiciliaria', '2026-02-28 00:24:36', '2026-02-28 00:24:36'),
(37, 'Oficina De Apoyo Diagnostico Terapéutico', '2026-02-28 00:25:09', '2026-02-28 00:25:09'),
(38, 'Unidad Salud Mental Hospitalización', '2026-02-28 00:25:37', '2026-02-28 00:25:37'),
(39, 'Banco De Sangre', '2026-02-28 00:26:00', '2026-02-28 00:26:00'),
(40, 'Banco De Leche', '2026-02-28 00:27:32', '2026-02-28 00:27:32'),
(41, 'Unidad De Alto Riesgo Obstétrico 1', '2026-02-28 00:40:29', '2026-02-28 00:40:29'),
(42, 'Coordinación De Pediatría', '2026-02-28 00:42:14', '2026-02-28 00:42:14'),
(43, 'Coordinación Oncología', '2026-02-28 00:42:35', '2026-02-28 00:42:35'),
(44, 'Sala Pediatría General', '2026-02-28 00:43:07', '2026-02-28 00:43:07'),
(45, 'Epidemiología', '2026-02-28 00:43:33', '2026-02-28 00:43:33'),
(46, 'Estadística y Archivo De Historias Clínicas', '2026-02-28 00:44:00', '2026-02-28 00:44:00'),
(47, 'Otorrinolaringología', '2026-02-28 00:44:44', '2026-02-28 00:44:44'),
(48, 'Oficina Coordinación de Enfermería', '2026-02-28 00:45:06', '2026-02-28 00:45:06'),
(49, 'Central De Esterilización', '2026-02-28 00:46:00', '2026-02-28 00:46:00'),
(50, 'Oficina  Gestión Técnica y logística', '2026-02-28 00:47:08', '2026-02-28 00:47:08'),
(51, 'Sala De Puerperio', '2026-02-28 00:47:59', '2026-02-28 00:47:59'),
(52, 'Seguridad Del Paciente', '2026-02-28 01:02:23', '2026-02-28 01:02:23'),
(53, 'Sala Pediatría Infecto', '2026-02-28 01:02:55', '2026-02-28 01:02:55'),
(54, 'Sala Cirugía Mujeres', '2026-02-28 01:03:55', '2026-02-28 01:03:55'),
(55, 'Sala Cirugía Hombres', '2026-02-28 01:04:34', '2026-02-28 01:04:34'),
(56, 'Radioterapia', '2026-02-28 01:05:23', '2026-02-28 01:05:23'),
(57, 'Atención Al Usuario', '2026-02-28 01:07:14', '2026-02-28 01:07:14'),
(58, 'Sala De Neurocirugía', '2026-02-28 01:24:57', '2026-02-28 01:24:57'),
(59, 'Estomatología-Odontología', '2026-02-28 01:26:45', '2026-02-28 01:26:45'),
(60, 'Nomina Relaciones Laborales', '2026-02-28 01:27:08', '2026-02-28 01:27:08'),
(61, 'Mantenimiento Biomédico', '2026-02-28 02:04:02', '2026-02-28 02:04:02'),
(62, 'Mantenimiento Locativo', '2026-02-28 02:05:50', '2026-02-28 02:05:50'),
(63, 'Servicio De Alimentación (cocina y repostero)', '2026-02-28 02:06:26', '2026-02-28 02:06:41'),
(64, 'Lavandería', '2026-02-28 02:09:14', '2026-02-28 02:09:14'),
(65, 'Jefatura Central De Esterilización', '2026-02-28 02:14:24', '2026-02-28 02:14:24'),
(66, 'Laboratorio Clínico General', '2026-02-28 02:15:34', '2026-02-28 02:15:34'),
(67, 'Consulta Post Morten', '2026-02-28 02:17:38', '2026-02-28 02:17:38'),
(68, 'Costurero (Roperia)', '2026-02-28 02:17:50', '2026-02-28 02:17:50'),
(69, 'Centro Documental', '2026-02-28 02:19:56', '2026-02-28 02:19:56'),
(70, 'Rehabilitación Cardiopulmonar', '2026-02-28 02:20:08', '2026-02-28 02:20:08'),
(71, 'Talento Humano', '2026-02-28 02:21:04', '2026-02-28 02:21:04'),
(72, 'Hospitalización Trasplante', '2026-02-28 02:21:37', '2026-02-28 02:21:37'),
(73, 'Oficina Coordinadora Clinica', '2026-03-03 20:31:00', '2026-03-03 20:31:00'),
(74, 'Coordinación de urgencias y emergencias', '2026-03-03 20:31:58', '2026-03-03 20:31:58'),
(75, 'Soporte nutricional adulto y terapia enterostomal', '2026-03-03 20:32:13', '2026-03-03 20:32:13'),
(76, 'Trabajo Social', '2026-03-03 20:35:31', '2026-03-03 20:35:31'),
(77, 'Oficina Asesora de Comunicaciones', '2026-03-03 20:37:27', '2026-03-03 20:37:27'),
(78, 'Compras', '2026-03-03 20:43:10', '2026-03-03 20:43:10'),
(79, 'Facturación', '2026-03-05 20:28:09', '2026-03-05 20:28:09'),
(80, 'Jefatura oficina gerencia servicios de salud', '2026-03-05 20:32:04', '2026-03-05 20:32:04'),
(81, 'Coordinación de Psicología', '2026-03-05 21:22:05', '2026-03-05 21:23:00'),
(82, 'Glosas', '2026-03-06 15:34:07', '2026-03-06 15:34:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0k0DGXSTbgzQXWERpLLRjEUlpXbwotLWeke0izL7', NULL, '181.48.44.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGxrT0pvQklURERqaHVGbm4zRVQxcEw5VkxkQ2tNSkJ0NXhtY0NUcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vcmVnaXN0ZXIiO3M6NToicm91dGUiO3M6ODoicmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773060834),
('3azGwG4MQlaoeuh1Pc6PSeQosw8nocBJ1cpXFhm9', NULL, '146.70.134.23', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0VKQ2N0NWRyVDE0dW9nMFhUeXRFVGdIOGtHNjBBQ0RMRFZ0Y1V2biI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773060450),
('3id8aD32A0uiH9G9F4O4MGAI3uUxEoJFL0yUL56a', NULL, '190.6.160.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmx2MzZUQWMxdktkd25wQVNWMFFzT1lDY05Ea2d0TktYMHZOV3J2YSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773064363),
('4cR7gVlwFwoJeZ2QISwmetFqcd5SRcBfP9ULfBHl', NULL, '66.102.8.77', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoianlUTjZRa3Z4QTNOT3N3OEtzcEpOM2lzVmV3Z01Va21XQnQ2bVJENCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvL2JpZW5lc3Rhci9neW0vaW5zY3JpcGNpb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062434),
('FBo3ipbdpIawJZ0ITgs72DyMIwEnQieDOHep8Q9F', NULL, '190.6.160.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQW4zWFJqSkdReW1LemxiS3k3WFJOQ0p0cWRTcWJhelRQeDZhOHNxRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vZXZlbnRvcy9hcGkvdXN1YXJpby8zODU2OTU1OSI7czo1OiJyb3V0ZSI7czoxOToiZXZlbnRvcy5hcGkudXN1YXJpbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773056554),
('gQik2YIWxkdSmEWQzLJhkkZ6PmWVBWBGsMGtVdDI', NULL, '191.95.53.242', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFg4dkdpTVg4bmNzbUxmSXo4eHczRHZ4UTZDbUNUOFR3WlhJMmlnTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vZXZlbnRvcy9pbnNjcmlwY2lvbi8xIjtzOjU6InJvdXRlIjtzOjE5OiJldmVudG9zLmluc2NyaXBjaW9uIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773056316),
('IBKXIOpM1tCF2ynX2q2hSN66UENYsFU3y2geOayC', NULL, '66.102.8.76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUG83ZWZNemZFZmxOdzk0c1RFMUIzaUFvY1dRNHdHdHd4d2ZvTk1KMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062347),
('J7kXPlMhoDtpGreEbMFeR1lUp4r0hw548TEA9vOo', NULL, '66.102.8.78', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUlORFBqV0dEbGlZcHprdG56S0JXd2FSY1FQTzNKQmFLYm9RazNaeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062347),
('KaXkYayBGOCkeRfL4RXFa2x1y85l2z2muGWA7VVk', NULL, '181.48.44.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiemlMc2J0cFFvQ0hHTXZaRDJ5NG1IUGQ0RmRHU29BM3NVZGViYkVtUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28iO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9fQ==', 1773058868),
('LXbpWPNKgRWTshlSD5j0tlYOJYbMFqiz1mg8R4pa', NULL, '66.102.8.78', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2JUa1EyR2xXeGpYUzcycVVUUkxEc1Vla0lNSVAyU0J0clkxM3NmRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062347),
('nbbH9sCj0rifvxkA8nOGK5tTwpiJzobARYuW6hwh', NULL, '66.102.8.76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidHJvWWhlYjNDUUNXSkNhTks1SkNiOHpDbEVVaVREY3pkOUdYeVkyeCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvL2JpZW5lc3Rhci9neW0vaW5zY3JpcGNpb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062434),
('OyR9OrFPcheY4Wszicchjq4UDCQY8uQL2TKbXVMN', NULL, '66.102.8.77', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTZSVk1GbkgwWUx5SFJIRnJ5cWFwaDFycHE5b1lyeGpaOG9UdG9LUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvL2JpZW5lc3Rhci9neW0vaW5zY3JpcGNpb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773062434),
('P1zZnkPdRzEb1yzubdZ9eOdRVdSKQXrj9C1IAVc1', 42, '191.95.51.225', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZWswREpXcEtKOWp5Mk1EV0prR0tPZW51ZkN4ZmxZMlhwTTNqVHBJMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8vY2FiaS5odXYuZ292LmNvL2JpZW5lc3Rhci9neW0vaW5zY3JpcGNpb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQyO30=', 1773062506),
('X4R39atriapsC4GtvAOgfeDenOjk19oYE1tMbamm', NULL, '190.6.160.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSG80d1JCenBIMmFZWUJZcVpyVnU2ZnVTNGdTMk14TXpzRzZXYk82NSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vYmllbmVzdGFyL2d5bS9pbnNjcmlwY2lvbiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773064269),
('xMXHFRuslTgTKyGn7f5a2AcXvsrEL4nIjRSp4hwM', NULL, '181.48.255.197', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlZlQXEyQzJCRUI2OEJsZU5OTFpuUmxlT3F0aXVJNGlLa1k4a2hvcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9jYWJpLmh1di5nb3YuY28vZXZlbnRvcy9pbnNjcmlwY2lvbi8xIjtzOjU6InJvdXRlIjtzOjE5OiJldmVudG9zLmluc2NyaXBjaW9uIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773059064);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido1` varchar(255) DEFAULT NULL,
  `apellido2` varchar(255) DEFAULT NULL,
  `identificacion` varchar(255) DEFAULT NULL,
  `servicio` varchar(255) DEFAULT NULL,
  `servicio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_vinculacion` varchar(255) DEFAULT NULL,
  `tipo_vinculacion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `nombre`, `apellido1`, `apellido2`, `identificacion`, `servicio`, `servicio_id`, `tipo_vinculacion`, `tipo_vinculacion_id`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Carlos Jairton', 'Carlos Jairton', 'Valderrama', 'Orobio', '94529371', 'Gestion de la Informacion', 1, 'Agesoc', 1, 'carjavalosistem@gmail.com', 'Super Admin', NULL, '$2y$12$KpGGy4PFVK0km6Ng9Fz37uX8EyXJYURM4Q/0vGK8ygFWPne89hL/G', NULL, '2026-02-26 21:18:58', '2026-02-26 21:18:58'),
(2, 'Melany Alessandra', NULL, 'Bolaños', NULL, '1109543008', 'Pic', 2, NULL, 2, 'bolanosalexandra528@gmail.com', 'Operador', NULL, '$2y$12$ivygXrLbigmN14uRKAb15ObHotyIsXtgn8YOYLrpvjOUuMJYo0MkC', NULL, '2026-02-26 23:43:51', '2026-03-05 20:39:39'),
(3, 'Kevin David', NULL, 'Chavarro', 'Eraso', '1005966532', 'Gestion de la Informacion', 1, 'Agesoc', 1, 'keviindavid00@gmail.com', 'Super Admin', NULL, '$2y$12$jshyTAJmL10g0B6YabhdFuhM4anAXZnRr8wmT5ZmtMeAxwpS.loZi', NULL, '2026-02-26 23:45:19', '2026-02-26 23:45:19'),
(4, 'Jeffer Hernando', NULL, 'Tovar', NULL, '94542294', 'Pic', 2, 'Agesoc', 1, 'jeffermiranda761@gmail.com', 'Operador', NULL, '$2y$12$iYd4703EFuIvOop0iqs3r.BD/xh.nb2r0bNVwrr/4YUza2QGOTF4i', NULL, '2026-03-03 15:53:55', '2026-03-03 15:53:55'),
(5, 'Juan Carlos', NULL, 'Quiñones', 'Arroyo', '16495648', 'Pic', 2, 'Planta', 3, 'jucaqui0735@hotmail.com', 'Operador', NULL, '$2y$12$h6xUUqSKMYxRbAJL1cfHte6oORLXo4qLujS/dERjMgXK/rEjeN5I.', NULL, '2026-03-03 15:55:18', '2026-03-03 15:55:18'),
(6, 'Santiago', NULL, 'Yepes', 'Sánchez', '1105364603', 'Pic', 2, 'Planta', 3, 'syepez418@gmail.com', 'Operador', NULL, '$2y$12$K4DEgNeU5vRKU8DLSRMN4ONan./Eay6EH8u.iaBwsF7gcxPJEH9lW', NULL, '2026-03-03 15:56:53', '2026-03-03 15:56:53'),
(7, 'Alessandra', NULL, 'Moreno', NULL, '1234567891', 'Pic', 2, 'Asstracud', 2, 'alessandra2345@gmail.com', 'Usuario', NULL, '$2y$12$tSqJKb.qOntptUgyTzYjYesMJxcmMBNisZObl2qWhzDiFXcqGKtba', NULL, '2026-03-04 22:20:56', '2026-03-04 22:20:56'),
(8, 'alejandra', NULL, 'Ramirez', 'Garces', '11122233', 'Atención Al Usuario', 57, 'Agesoc', 1, 'alejandra2334@gmail.com', 'Usuario', NULL, '$2y$12$desdyhL86lE5//A6kHR/TeIoPi4BMP5/2zBl2B0HCHZHEM2JsUtni', NULL, '2026-03-05 14:41:08', '2026-03-05 14:41:08'),
(9, 'Santiago', NULL, 'Cruz', 'Giraldo', '1144105828', 'Gestion de la Informacion', 1, 'Agesoc', 1, 'scruzg124@gmail.com', 'Usuario', NULL, '$2y$12$guInOg0ghCpPvWWG/KA.OufVz0Sc2lqQPPmJQysOHkahtEPeq/EVy', NULL, '2026-03-05 20:18:19', '2026-03-05 20:18:19'),
(10, 'NATALIA', NULL, 'VALLECILLA', 'GUTIERREZ', '1116274570', NULL, NULL, 'Asstracud', 2, 'nativalle28@gmail.com', 'Usuario', NULL, '$2y$12$n5zPwqpxMLLGwEYMHpHbjuEjZDDzA0s1hnjgxj6aJHLWREuubC/6q', NULL, '2026-03-05 20:18:23', '2026-03-05 20:18:23'),
(11, 'Daniela', NULL, 'Jiménez', 'Orozco', '1144065090', 'Oficina Asesora Gestión de Calidad', 9, 'Agesoc', 1, 'dani.jimenez0916@gmail.com', 'Usuario', NULL, '$2y$12$a6ZCklKnl81vljObzf5ND.DFlom9FPxKtfqNeie8XXDTVrl5WuDAa', 'LmL098ifXaIDO5l65SyJpO5Bu1x7q4JKYwixJYAY8JCQnQ0Ce6Zgw92GHfMf', '2026-03-05 20:20:38', '2026-03-05 20:20:38'),
(12, 'Leidy Julieth', NULL, 'Quiñones', 'Valencia', '1143928663', 'Talento Humano', 71, 'Agesoc', 1, 'lquinoneshuv@gmail.com', 'Usuario', NULL, '$2y$12$rg7Db6.ibkwxD4h8jZrtAuzGYYXjN667ehsqvPXG9dsRiABR5HfbG', NULL, '2026-03-05 20:21:11', '2026-03-05 20:21:11'),
(13, 'JENNIFER', NULL, 'HERRERA', 'VEGA', '38569559', 'Talento Humano', 71, 'Agesoc', 1, 'tjvega@hotmail.com', 'Usuario', NULL, '$2y$12$iO.DW0kn46hLg/WZ38oBGe2YGUreUS1RA4jX6.sUNlYetSLgARedC', 'e4JQlsx4rvKYXgF6ohuOTfzXGCUdvOt2YcAZvD4VVX1GFriFyz0S9wrDQakg', '2026-03-05 20:21:39', '2026-03-05 20:21:39'),
(14, 'Farah', NULL, 'Cortázar', 'Bolaños', '29123010', 'Facturación', 79, NULL, 1, 'coordfacturacionuci@gmail.com', 'Usuario', NULL, '$2y$12$aomtTY4zdj9Q6hioxC1q8uF445u/rm4UHua9C5W.S.jHLCHCxEE/u', NULL, '2026-03-05 20:23:21', '2026-03-05 20:33:42'),
(15, 'Carmen Helena', NULL, 'Gallego', 'Melendez', '1112219177', 'Talento Humano', 71, 'Agesoc', 1, 'gelen0509@hotmail.com', 'Usuario', NULL, '$2y$12$EHblHpxp4It1X2aa1vhYm.lZ2JTM.ZUk2sH.5weKYzILsjDPUZlZ2', NULL, '2026-03-05 20:24:20', '2026-03-05 20:24:20'),
(16, 'DENNYS VANESSA', NULL, 'RAMIREZ', 'FERNANDEZ', '1151957794', 'Sala Medica Mujeres', 20, 'Asstracud', 2, 'vanesita-1606@hotmail.com', 'Usuario', NULL, '$2y$12$o9076mUlx5eTUm8AVsfg5.XU6C4tq54QW2BaV7zUdF5fh12VTDBQ.', NULL, '2026-03-05 20:32:39', '2026-03-05 20:32:39'),
(17, 'Maria Isabel', NULL, 'Ambuila', 'Ambuila', '1112482909', 'Jefatura oficina gerencia servicios de salud', 80, 'Planta', 3, 'mariaisabel-1994@hotmail.com', 'Usuario', NULL, '$2y$12$zSpWysachcaS1ZoFRh1KE.BsLWDxrFlk6TXYy1Ao80pA4Gs/RPf1i', NULL, '2026-03-05 20:45:41', '2026-03-05 20:45:41'),
(18, 'ANDERSON', NULL, 'RODRIGUEZ', 'VELASCO', '14678160', 'Epidemiología', 45, 'Asstracud', 2, 'estadisticasvitales@correohuv.gov.co', 'Usuario', NULL, '$2y$12$NAFuxdVLsX6iVoiNkUiYqOVIknkgxdpjWiUroEMUCGoVMJju80zgK', NULL, '2026-03-05 20:47:57', '2026-03-05 20:47:57'),
(19, 'Juan Manuel', NULL, 'Roa', 'Chavez', '16462936', 'Quirófano Central', 19, 'Agesoc', 1, 'roajuan82@gmail.com', 'Usuario', NULL, '$2y$12$7afMVBaZdO0lu1aaalxNh.6r2XNSHq1RXXPkFIyw.7LYU4iZUjoRC', NULL, '2026-03-05 21:11:49', '2026-03-05 21:11:49'),
(20, 'Kely Andrea', NULL, 'Salazar', 'Duran', '1126586574', 'Coordinación Servicios ambulatorios', 12, 'Asstracud', 2, 'kellysala15@gmail.com', 'Usuario', NULL, '$2y$12$EL7nd04TbUnTVso41AOVo.ALretPuYjhjJC37KtOSFmwxebHW5Aju', NULL, '2026-03-05 21:14:48', '2026-03-05 21:14:48'),
(21, 'Laura Sophia', NULL, 'Ospina', 'Rodríguez', '1006109680', NULL, NULL, NULL, NULL, 'laura.sophia.ospina@correounivalle.edu.co', 'Usuario', NULL, '$2y$12$93moruCQWORA8ufd1SlHwe8GB/FC7HeP/wsjoFdlPII/RLC6feYym', NULL, '2026-03-05 21:15:35', '2026-03-05 21:15:35'),
(22, 'Erika Janeth', NULL, 'Martínez', 'Hernandez', '38553054', NULL, NULL, 'Asstracud', 2, 'eriksakae@gmail.com', 'Usuario', NULL, '$2y$12$8Rv/S/WTqAhLncTgtovjo.GvpmPSZ/SkmyKR8asMbyF.sQ4r9SDZW', NULL, '2026-03-05 21:21:24', '2026-03-05 21:21:24'),
(23, 'Yessica Lorena', NULL, 'Torres', 'Espinosa', '1144052106', 'Sala Cirugía Pediátrica Ana Frank', 22, 'Asstracud', 2, 'yessitorrese@gmail.com', 'Usuario', NULL, '$2y$12$p3BLLGnhyWn5.2PaltXm5eL8yAdbYCi.IValDHIL8ACxwm6yF.eW2', NULL, '2026-03-05 21:24:43', '2026-03-05 21:24:43'),
(24, 'GLORIA MILDRED', NULL, 'MORENO', 'CHARRIA', '66773018', 'Oficina Gerencia  General', 4, 'Planta', 3, 'gloriamorenohsjd@gmail.com', 'Usuario', NULL, '$2y$12$FdMnybwy0mShgeiAR9ylG.DMzuKoSBfdsoKiepVp5GQmh9JpkrV5C', NULL, '2026-03-05 21:37:37', '2026-03-05 21:37:37'),
(25, 'Marileth', NULL, 'Urdinola', 'Gonzalez', '29314496', 'Oficina Asesora Gestión de Calidad', 9, 'Asstracud', 2, 'marileth20@gmail.com', 'Usuario', NULL, '$2y$12$87d3ZzCge3M7Oz7LiGN7AejWIN8WP7EpGN8aLyp.ReIuRGaL7Jl7W', NULL, '2026-03-05 22:34:31', '2026-03-05 22:34:31'),
(26, 'María Fernanda', NULL, 'Morales', 'Valdés', '31414851', 'Atención Al Usuario', 57, 'Planta', 3, 'capacitacionvirtualhuv@gmail.com', 'Usuario', NULL, '$2y$12$q2zkT3naz5K0QzEOlYZvMe97WyfKz37iC.B98GeNOgYPqheUqj4O6', NULL, '2026-03-06 02:10:30', '2026-03-06 02:10:30'),
(27, 'Viviana', NULL, 'Valencia', 'Valencia', '38602524', 'Sala cirugía pediátrica Ana Frank', 13, 'Asstracud', 2, 'vivi.vvv1983@gmail.com', 'Usuario', NULL, '$2y$12$uLhXVRgDaG7N.baMz4fskeSliy3qdFpV7oyT.AdTsCA5l.RRjEN.y', 'xULg4EtFONuv9AXLj04KzozATdATzguqusDJSL4tPuApyys1efVQZdqMzFyW', '2026-03-06 02:48:55', '2026-03-06 02:48:55'),
(28, 'Angie', NULL, 'Trochez', NULL, '1005783664', 'Programa De Atención Domiciliaria', 36, 'Asstracud', 2, 'am.trovi29.2@gmail.com', 'Usuario', NULL, '$2y$12$jF2Mqz19Sc0dJdCPrnkxoeMy/YHaABiWxkII6PjN/Wm72b3ylzEF.', NULL, '2026-03-06 03:01:40', '2026-03-06 03:01:40'),
(29, 'sergio leonardo', NULL, 'gutierrez', 'rojas', '1127954836', 'Servicio De Alimentación (cocina y repostero)', 63, 'Agesoc', 1, 'leonardogutie10@gmail.com', 'Usuario', NULL, '$2y$12$SFG3fKyAxyit8C1do8ENxu2yvwPDZ5NeqmAqHseplsfipNqNEoK9C', NULL, '2026-03-06 04:28:38', '2026-03-06 04:28:38'),
(30, 'LINA MARCELA', NULL, 'GARCIA', 'VELASCO', '1151939313', 'Coordinación Servicios ambulatorios', 12, 'Asstracud', 2, 'marcelagarciamd7@gmail.com', 'Usuario', NULL, '$2y$12$733Xs980qrTOD6NrVL6W/OJZwxH5ByziktOz8V3PwbJ7wp6F63ce.', 'VfUts4ilvap5lznpozE6JM1SdeCvz4huKRk8XIXc7cm7tlSXNeWiUPh5Vxih', '2026-03-06 12:20:23', '2026-03-06 12:20:23'),
(31, 'SONIA', NULL, 'HERRERA', 'CARDONA', '30724289', 'Gestion de la Informacion', 1, NULL, 3, 'sohecar@gmail.com', 'Usuario', NULL, '$2y$12$cPVwv.Avfh/IOE5TzmDXV..NGHqDuQkis5ECGdwnIqwxgClIw7fXm', NULL, '2026-03-06 12:51:27', '2026-03-06 14:04:48'),
(32, 'EDWIN FERNADO', NULL, 'TRUJILLO', 'LARGO', '6384226', 'Compras', 78, 'Agesoc', 1, 'edwinfernandotrujillolargo@gmail.com', 'Usuario', NULL, '$2y$12$EFIii2T5lqxlPZdmkixRjuAQQKxKcVsyGljskUvHTKVahffd1Lbka', NULL, '2026-03-06 14:40:43', '2026-03-06 14:40:43'),
(33, 'Santiago', NULL, 'Delgado', 'Hurtado', '1144212975', 'Talento Humano', 71, NULL, 1, 'sandelhur99@gmail.com', 'Instructor GYM', NULL, '$2y$12$xs7B63VyVMH7QQFSbTWa9eT9SQDqhBqa0gK4x1NHF/jZyENM1.6jq', NULL, '2026-03-06 14:52:21', '2026-03-06 16:21:47'),
(36, 'CARMEN YANET', NULL, 'ARBOLEDA', 'FLOREZ', '66855065', 'Terapia Intensiva  (UCI)', 18, 'Planta', 3, 'facturacionucihuv@gmail.com', 'Usuario', NULL, '$2y$12$rEXuq0PIdJXZm5QHwPuphuXDwbx89u5LHwlZEKeejXR1UhanCocgu', NULL, '2026-03-06 15:20:06', '2026-03-06 15:20:06'),
(37, 'Jessica', NULL, 'Grajales', 'Osorio', '1143832293', 'Glosas', 82, 'Agesoc', 1, 'sisigrata@hotmail.com', 'Usuario', NULL, '$2y$12$KDlp67hs5NsPRo67SJvqAO9.RriUEHV71y8Lbf/oKdCOj56vDld/G', NULL, '2026-03-06 15:36:16', '2026-03-06 15:36:16'),
(38, 'Alejandra', NULL, 'López', 'Quintero', '1107518024', 'Coordinación Servicios ambulatorios', 12, 'Asstracud', 2, 'alopezquintero36@gmail.com', 'Usuario', NULL, '$2y$12$HrgQt.osK2ab9gHJbBZ3mOkek.XOAA5nR7T7X/y6qzhKBHSaKVCMy', NULL, '2026-03-06 15:38:36', '2026-03-06 15:38:36'),
(39, 'Nataly', NULL, 'Duque', 'Davalos', '1107096202', 'Oficina De Apoyo Diagnostico Terapéutico', 37, 'Agesoc', 1, 'natalyduque924@gmail.com', 'Usuario', NULL, '$2y$12$Ffp0.FTsU4/I5eHKt7KhO.aW1HGDER2NJnHcExdiuf3Hj57E1r25C', NULL, '2026-03-06 15:44:11', '2026-03-06 15:44:11'),
(41, 'LEYDI VANESSA', NULL, 'Herrera', 'Osorio', '1143836044', 'Facturación', 79, 'Agesoc', 1, 'va.nee.o@hotmail.com', 'Usuario', NULL, '$2y$12$kRt4Cnr2iTiefQ3qvlxEwOS8NyhuQvGP9TQbk6AXVWYd/7AywZWIO', NULL, '2026-03-09 11:14:43', '2026-03-09 11:14:43'),
(42, 'Olga Lucía', NULL, 'Marín', 'Zamora', '66854638', 'Facturación', 79, 'Agesoc', 1, 'molgalucia958@gmail.com', 'Usuario', NULL, '$2y$12$01jUhv7yhHjXCtY0ZbtV8OGZbBlvwMK3xGLLAK0bdR9KzeXABiP5e', NULL, '2026-03-09 12:20:21', '2026-03-09 12:20:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinculaciones`
--

CREATE TABLE `vinculaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vinculaciones`
--

INSERT INTO `vinculaciones` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Agesoc', '2026-02-26 23:31:18', '2026-02-26 23:31:18'),
(2, 'Asstracud', '2026-02-26 23:32:09', '2026-02-26 23:32:09'),
(3, 'Planta', '2026-02-27 00:25:56', '2026-02-27 00:25:56'),
(4, 'Diamante', '2026-03-03 19:33:00', '2026-03-03 19:33:00'),
(5, 'Napoles', '2026-03-03 19:33:12', '2026-03-03 19:33:12'),
(6, 'Imágenes San José', '2026-03-03 19:33:44', '2026-03-03 19:33:44'),
(7, 'Estudiantes', '2026-03-05 21:22:22', '2026-03-05 21:22:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `encuestas_codigo_unique` (`codigo`);

--
-- Indices de la tabla `encuestas_respuestas`
--
ALTER TABLE `encuestas_respuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `encuestas_respuestas_encuesta_id_index` (`encuesta_id`),
  ADD KEY `encuestas_respuestas_created_at_index` (`created_at`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evento_dias`
--
ALTER TABLE `evento_dias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_dias_evento_id_foreign` (`evento_id`);

--
-- Indices de la tabla `evento_franjas`
--
ALTER TABLE `evento_franjas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_franjas_evento_id_foreign` (`evento_id`);

--
-- Indices de la tabla `evento_inscripciones`
--
ALTER TABLE `evento_inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_inscripciones_evento_id_foreign` (`evento_id`),
  ADD KEY `evento_inscripciones_evento_franja_id_foreign` (`evento_franja_id`),
  ADD KEY `evento_inscripciones_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `horariosgim`
--
ALTER TABLE `horariosgim`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `horariosgim_identificacion_unique` (`Identificacion`);

--
-- Indices de la tabla `inscripgym`
--
ALTER TABLE `inscripgym`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inscripgym_correolec_unique` (`correolec`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `publicidad`
--
ALTER TABLE `publicidad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `repencuestas`
--
ALTER TABLE `repencuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repencuestas_encuesta_id_foreign` (`encuesta_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_identificacion_unique` (`identificacion`);

--
-- Indices de la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `encuestas_respuestas`
--
ALTER TABLE `encuestas_respuestas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `evento_dias`
--
ALTER TABLE `evento_dias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `evento_franjas`
--
ALTER TABLE `evento_franjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;

--
-- AUTO_INCREMENT de la tabla `evento_inscripciones`
--
ALTER TABLE `evento_inscripciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horariosgim`
--
ALTER TABLE `horariosgim`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripgym`
--
ALTER TABLE `inscripgym`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `publicidad`
--
ALTER TABLE `publicidad`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `repencuestas`
--
ALTER TABLE `repencuestas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `encuestas_respuestas`
--
ALTER TABLE `encuestas_respuestas`
  ADD CONSTRAINT `encuestas_respuestas_encuesta_id_foreign` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_dias`
--
ALTER TABLE `evento_dias`
  ADD CONSTRAINT `evento_dias_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_franjas`
--
ALTER TABLE `evento_franjas`
  ADD CONSTRAINT `evento_franjas_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_inscripciones`
--
ALTER TABLE `evento_inscripciones`
  ADD CONSTRAINT `evento_inscripciones_evento_franja_id_foreign` FOREIGN KEY (`evento_franja_id`) REFERENCES `evento_franjas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evento_inscripciones_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_inscripciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `repencuestas`
--
ALTER TABLE `repencuestas`
  ADD CONSTRAINT `repencuestas_encuesta_id_foreign` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

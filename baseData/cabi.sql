-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-02-2026 a las 19:47:37
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
-- Base de datos: `cabi`
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
  `Nombre` varchar(150) NOT NULL,
  `Apellido1` varchar(150) NOT NULL,
  `Apellido2` varchar(150) NOT NULL,
  `ndocumento` int(15) NOT NULL,
  `edad` int(3) NOT NULL,
  `celular` varchar(50) NOT NULL,
  `tipoVinculacion` varchar(150) NOT NULL,
  `Servicio` varchar(150) NOT NULL,
  `emerconta` varchar(150) NOT NULL,
  `correolec` varchar(150) DEFAULT NULL,
  `identificacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripgym`
--

INSERT INTO `inscripgym` (`id`, `Nombre`, `Apellido1`, `Apellido2`, `ndocumento`, `edad`, `celular`, `tipoVinculacion`, `Servicio`, `emerconta`, `correolec`, `identificacion`) VALUES
(1, 'Carlos Jairton', 'Valderrama', 'Orobio', 12345678, 46, '31533632', 'Agesoc', 'Gestión de la Informacion', '3263262', 'carjavalosistem@gmail.com', NULL);

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
(25, '2026_02_26_000025_drop_tipo_from_servicios_vinculaciones', 19);

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
(2, 'PIC', '2026-02-26 23:34:03', '2026-02-26 23:34:03');

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
('e2oMbvZRuG4xDiD7R4KD0Lpd8dTZG48nZvVPfhfh', 2, '192.168.2.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYXdYMWpHSFJxNEpFOVQ2SE9nYWplRTBzUVF5TnNUSjZhTHpaRmFtSCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xOTIuMTY4LjIuMjAwOjgwMDQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1772131561),
('GEHrPFiAKXF86oTJXIS8Lys9YJ4r7NYF0Gw3O2V8', NULL, '192.168.2.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTm10Z0pFcGgwQnNqd0g2ekZlWW1MSGVoYURCU0cxZUpOd1pKaGh0ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xOTIuMTY4LjIuMjAwOjgwMDQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772131594);

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
(2, 'Melany alessandra', NULL, 'Bolaños', NULL, '1109543008', 'PIC', 2, 'Asstracud', 2, 'bolanosalexandra528@gmail.com', 'Operador', NULL, '$2y$12$MtfSY2Yes6rM.xKknHiBa.8wLl0eeaLPaKLxslj6xHj83nWgMr9CW', NULL, '2026-02-26 23:43:51', '2026-02-26 23:43:51'),
(3, 'Kevin David', NULL, 'Chavarro', 'Eraso', '1005966532', 'Gestion de la Informacion', 1, 'Agesoc', 1, 'keviindavid00@gmail.com', 'Super Admin', NULL, '$2y$12$jshyTAJmL10g0B6YabhdFuhM4anAXZnRr8wmT5ZmtMeAxwpS.loZi', NULL, '2026-02-26 23:45:19', '2026-02-26 23:45:19');

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
(2, 'Asstracud', '2026-02-26 23:32:09', '2026-02-26 23:32:09');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

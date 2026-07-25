-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2025 a las 17:34:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

DROP DATABASE IF EXISTS uda;
CREATE DATABASE uda;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abonos`
--

CREATE TABLE `abonos` (
  `id` varchar(36) NOT NULL,
  `fecha` datetime NOT NULL,
  `abonado` varchar(50) NOT NULL,
  `edad` int(10) UNSIGNED NOT NULL,
  `telefono` char(9) NOT NULL,
  `cuenta_bancaria` char(24) NOT NULL,
  `tipo` varchar(36) NOT NULL,
  `asiento` char(12) NOT NULL,
  `precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_abonos`
--

CREATE TABLE `tipo_abonos` (
  `id` varchar(36) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `precio` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_abonos`
--

INSERT INTO `tipo_abonos` (`id`, `descripcion`, `precio`) VALUES
('19d6ffff-b352-11f0-918b-0d6e01bba713', 'Tribuna', 500),
('19d71aa5-b352-11f0-918b-0d6e01bba713', 'Preferencia', 300),
('588b2c10-b352-11f0-918b-0d6e01bba713', 'Fondo', 110);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_fk` (`tipo`);

--
-- Indices de la tabla `tipo_abonos`
--
ALTER TABLE `tipo_abonos`
  ADD PRIMARY KEY (`id`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD CONSTRAINT `tipo_fk` FOREIGN KEY (`tipo`) REFERENCES `tipo_abonos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/* TO 3. Uso de cookies y session */
-- tabla usuarios
CREATE TABLE `usuarios` (
  `id` varchar(36) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

-- usuario especial administrador: crear desde login.php





-- pruebas:

-- antonio40 contrasena
-- uda 1234

-- otro usuario administrador 
INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
('ae9e071d-8f24-46dc-94cb-6e0fde73793c', 'antonio40', '$2y$10$N03dMgAWzvq9nN8FOvE.4OT2Lts1g/LDuPbxVZW1k7tr9huqDf0cO');
-- usuario administrador
INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
('754d60aa-28f0-4ea2-a365-dc3b4663a787', 'uda', '$2y$10$uwdVl0p6wxeGvUMnJObkGeKG9dFwBMXvwNrY0638fB.W27ciFs7oe');
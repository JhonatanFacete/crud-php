-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-09-2024 a las 21:57:51
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
-- Base de datos: `nexura`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_areas`
--

CREATE TABLE `tbl_areas` (
  `fld_id` int(11) NOT NULL,
  `fld_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tbl_areas`
--

INSERT INTO `tbl_areas` (`fld_id`, `fld_nombre`) VALUES
(1, 'Ventas'),
(2, 'Calidad'),
(3, 'Producción'),
(4, 'Administración'),
(5, 'Sistemas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_empleados`
--

CREATE TABLE `tbl_empleados` (
  `fld_id` int(11) NOT NULL,
  `fld_nombre` varchar(255) NOT NULL,
  `fld_email` varchar(255) NOT NULL,
  `fld_sexo` enum('M','F') NOT NULL,
  `fld_IDarea` int(11) NOT NULL,
  `fld_boletin` enum('0','1') NOT NULL COMMENT '0=No, 1=SI',
  `fld_descripcion` text NOT NULL,
  `fld_registro` datetime NOT NULL,
  `fld_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_empleados_rol`
--

CREATE TABLE `tbl_empleados_rol` (
  `fld_IDempleado` int(11) NOT NULL,
  `fld_IDrol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `fld_id` int(10) NOT NULL,
  `fld_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tbl_roles`
--

INSERT INTO `tbl_roles` (`fld_id`, `fld_nombre`) VALUES
(1, 'Profesional de proyectos - Desarrollador'),
(2, 'Gerente estratégico'),
(3, 'Auxiliar Administrativo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_areas`
--
ALTER TABLE `tbl_areas`
  ADD PRIMARY KEY (`fld_id`);

--
-- Indices de la tabla `tbl_empleados`
--
ALTER TABLE `tbl_empleados`
  ADD PRIMARY KEY (`fld_id`),
  ADD UNIQUE KEY `fld_email` (`fld_email`),
  ADD KEY `fld_IDarea` (`fld_IDarea`);

--
-- Indices de la tabla `tbl_empleados_rol`
--
ALTER TABLE `tbl_empleados_rol`
  ADD KEY `fld_IDempleado` (`fld_IDempleado`),
  ADD KEY `fld_IDrol` (`fld_IDrol`);

--
-- Indices de la tabla `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`fld_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_areas`
--
ALTER TABLE `tbl_areas`
  MODIFY `fld_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbl_empleados`
--
ALTER TABLE `tbl_empleados`
  MODIFY `fld_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `fld_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_empleados`
--
ALTER TABLE `tbl_empleados`
  ADD CONSTRAINT `ID area - Empleados` FOREIGN KEY (`fld_IDarea`) REFERENCES `tbl_areas` (`fld_id`);

--
-- Filtros para la tabla `tbl_empleados_rol`
--
ALTER TABLE `tbl_empleados_rol`
  ADD CONSTRAINT `ID empleado - rol` FOREIGN KEY (`fld_IDempleado`) REFERENCES `tbl_empleados` (`fld_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ID rol - empleado` FOREIGN KEY (`fld_IDrol`) REFERENCES `tbl_roles` (`fld_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

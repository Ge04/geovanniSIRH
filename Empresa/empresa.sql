-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-04-2025 a las 16:14:09
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
-- Base de datos: `empresa`
--

-- --------------------------------------------------------



CREATE TABLE `departamentos` (
  `id_departamento` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `departamentos` (`id_departamento`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
(1, 'Recursos Humanos', 'Gestión del personal, contrataciones y desarrollo profesional', '2025-04-28 12:48:32'),
(2, 'Informática', 'Desarrollo de software, soporte técnico y sistemas', '2025-04-28 12:48:32'),
(3, 'Contabilidad', 'Gestión financiera y contable de la empresa', '2025-04-28 12:48:32'),
(4, 'Ventas', 'Gestión de ventas y atención al cliente', '2025-04-28 12:48:32'),
(5, 'Marketing', 'Estrategias de marketing y comunicación', '2025-04-28 12:48:32'),
(6, 'Operaciones', 'Gestión de operaciones y logística', '2025-04-28 12:48:32'),
(7, 'Legal', 'Asesoría legal y cumplimiento normativo', '2025-04-28 12:48:32'),
(8, 'Administración', 'Gestión administrativa y documentación', '2025-04-28 12:48:32'),
(9, 'Producción', 'Gestión de la producción y calidad', '2025-04-28 12:48:32'),
(10, 'Investigación y Desarrollo', 'Innovación y desarrollo de nuevos productos', '2025-04-28 12:48:32');

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_contratacion` date NOT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `salario_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `empleados` (`id_empleado`, `id_departamento`, `nombre`, `apellidos`, `dni`, `fecha_nacimiento`, `direccion`, `telefono`, `email`, `fecha_contratacion`, `fecha_fin_contrato`, `salario_base`) VALUES
(1, 7, 'geovanni', 'merhej', '09ueqw', NULL, NULL, NULL, NULL, '2025-04-02', '2025-04-28', 200000.00),
(2, 5, 'pedro', 'mba', '16dj23ffs', NULL, NULL, NULL, NULL, '2025-04-24', '2028-06-28', 0.00),
(3, 8, 'Maria ', 'nguema', 'sdf15sdj6', NULL, NULL, NULL, NULL, '2025-04-19', '2027-04-02', 0.00),
(4, 10, 'Fermin', 'copuboru', 'jdha8we0w', NULL, NULL, NULL, NULL, '2025-02-07', '2026-10-03', 0.00);


CREATE TABLE `nominas` (
  `id_nomina` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha_nomina` date NOT NULL,
  `salario_base` decimal(10,2) NOT NULL,
  `bonificaciones` decimal(10,2) DEFAULT 0.00,
  `deducciones` decimal(10,2) DEFAULT 0.00,
  `total_neto` decimal(10,2) NOT NULL,
  `fecha_pago` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `nominas` (`id_nomina`, `id_empleado`, `fecha_nomina`, `salario_base`, `bonificaciones`, `deducciones`, `total_neto`, `fecha_pago`) VALUES
(4, 1, '2025-04-30', 800.00, 200.00, 100.00, 900.00, '2025-04-28'),
(5, 4, '2025-04-30', 1200.00, 0.00, 270.00, 930.00, '2025-04-28'),
(6, 2, '2025-04-30', 700.00, 100.00, 120.00, 680.00, '2025-04-28');



CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `tipo_permiso` enum('Vacaciones','Permiso médico','Permiso personal','Otros') DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `comentarios` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `permisos` (`id_permiso`, `id_empleado`, `tipo_permiso`, `fecha_inicio`, `fecha_fin`, `estado`, `comentarios`) VALUES
(4, 3, 'Permiso médico', '2025-04-30', '2025-06-06', 'Aprobado', 'enfermedad covid-19\r\n'),
(5, 1, 'Vacaciones', '2025-05-01', '2025-05-11', 'Rechazado', 'vacaciones anual\r\n'),
(6, 4, 'Permiso personal', '2025-04-28', '2025-04-30', 'Pendiente', 'boda\r\n');


ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`);


ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `idx_empleados_departamento` (`id_departamento`);

ALTER TABLE `nominas`
  ADD PRIMARY KEY (`id_nomina`),
  ADD KEY `idx_nominas_empleado` (`id_empleado`);


ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `idx_permisos_empleado` (`id_empleado`);


ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;


ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `nominas`
  MODIFY `id_nomina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`);


  ADD CONSTRAINT `nominas_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`);


ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-10-2024 a las 18:20:29
-- Versión del servidor: 8.0.39-0ubuntu0.24.04.2
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `parkease_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ESTADO`
--

CREATE TABLE `ESTADO` (
  `ID_ESTADO` int NOT NULL,
  `DESCRIPCION_ESTADO` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ESTADO`
--

INSERT INTO `ESTADO` (`ID_ESTADO`, `DESCRIPCION_ESTADO`) VALUES
(1, 'DISPONILBE'),
(2, 'OCUPADO'),
(3, 'RESERVADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PARQUEADERO`
--

CREATE TABLE `PARQUEADERO` (
  `ID_PARQUEADERO` int NOT NULL,
  `ID_TIPO_VEHICULO_FK` int NOT NULL,
  `ID_PISO_FK` int NOT NULL,
  `LOCACION` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `PARQUEADERO`
--

INSERT INTO `PARQUEADERO` (`ID_PARQUEADERO`, `ID_TIPO_VEHICULO_FK`, `ID_PISO_FK`, `LOCACION`) VALUES
(1, 1, 1, 1),
(2, 1, 1, 2),
(3, 1, 1, 3),
(4, 1, 1, 4),
(5, 1, 1, 5),
(6, 1, 1, 6),
(7, 1, 1, 7),
(8, 1, 1, 8),
(9, 1, 1, 9),
(10, 1, 1, 10),
(11, 1, 1, 11),
(12, 1, 1, 12),
(13, 1, 1, 13),
(14, 1, 1, 14),
(15, 1, 1, 15),
(16, 2, 2, 1),
(17, 2, 2, 2),
(18, 2, 2, 3),
(19, 2, 2, 4),
(20, 2, 2, 5),
(21, 2, 2, 6),
(22, 2, 2, 7),
(23, 2, 2, 8),
(24, 2, 2, 9),
(25, 2, 2, 10),
(26, 2, 2, 11),
(27, 2, 2, 12),
(28, 2, 2, 13),
(29, 2, 2, 14),
(30, 2, 2, 15),
(31, 2, 3, 1),
(32, 2, 3, 2),
(33, 2, 3, 3),
(34, 2, 3, 4),
(35, 2, 3, 5),
(36, 2, 3, 6),
(37, 2, 3, 7),
(38, 2, 3, 8),
(39, 2, 3, 9),
(40, 2, 3, 10),
(41, 2, 3, 11),
(42, 2, 3, 12),
(43, 2, 3, 13),
(44, 2, 3, 14),
(45, 2, 3, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PERSONA`
--

CREATE TABLE `PERSONA` (
  `ID_PERSONA` int NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `APELLIDO` varchar(100) NOT NULL,
  `ID_USUARIO_FK` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `PERSONA`
--

INSERT INTO `PERSONA` (`ID_PERSONA`, `NOMBRE`, `APELLIDO`, `ID_USUARIO_FK`) VALUES
(1, 'Ivan David', 'Moreno Vargas', 1),
(2, 'Administrador', 'U rosario', 2),
(3, 'Personal', 'U rosario', 3),
(4, 'Juan Pablo', 'Arango', 4),
(5, 'Laura', 'Espinosa', 5),
(6, 'Juan Felipe', 'Fajardo', 6),
(7, 'Maria Jose', 'Villalobos', 7),
(8, 'Camilo', 'Perdomo', 8),
(9, 'Joseph Fernando', 'Doqueresana', 9),
(10, 'Antonio', 'Ayala', 10),
(11, 'joahn', 'hernandez', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PISO`
--

CREATE TABLE `PISO` (
  `ID_PISO` int NOT NULL,
  `DESCRIPCION_PISO` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `PISO`
--

INSERT INTO `PISO` (`ID_PISO`, `DESCRIPCION_PISO`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `RESERVA`
--

CREATE TABLE `RESERVA` (
  `ID_RESERVA` int NOT NULL,
  `ID_PARQUEADERO_FK` int NOT NULL,
  `ID_PERSONA_FK_FK` int NOT NULL,
  `ID_ESTADO_FK` int NOT NULL,
  `HORA_RESERVA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TIPO`
--

CREATE TABLE `TIPO` (
  `ID_TIPO` int NOT NULL,
  `TIPO_DESCRIPCION` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `TIPO`
--

INSERT INTO `TIPO` (`ID_TIPO`, `TIPO_DESCRIPCION`) VALUES
(1, 'USUARIO_UR'),
(2, 'ADMINISTRADOR'),
(3, 'PERSONAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TIPO_VEHICULO`
--

CREATE TABLE `TIPO_VEHICULO` (
  `ID_TIPO_VEHICULO` int NOT NULL,
  `DESCRIPCION_TIPO_VEHICULO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `TIPO_VEHICULO`
--

INSERT INTO `TIPO_VEHICULO` (`ID_TIPO_VEHICULO`, `DESCRIPCION_TIPO_VEHICULO`) VALUES
(1, 'MOTO'),
(2, 'CARRO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIO`
--

CREATE TABLE `USUARIO` (
  `ID_USUARIO` int NOT NULL,
  `PASAPORTE_VIRTUAL` varchar(100) NOT NULL,
  `CONTRASEÑA` varchar(100) NOT NULL,
  `ID_TIPO_FK` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `USUARIO`
--

INSERT INTO `USUARIO` (`ID_USUARIO`, `PASAPORTE_VIRTUAL`, `CONTRASEÑA`, `ID_TIPO_FK`) VALUES
(1, 'ivanda.moreno', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(2, 'administrador', '93a31b364bbea09fe318004dccff911248f0c3862aed620d08564ecc151f31d5', 2),
(3, 'personal', '39e58b4e55aeb30519f5b244660055566a0c0880efbfc4d945f2a6ebe8df9189', 3),
(4, 'juanpa.arango', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(5, 'laurava.espinosa', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(6, 'juanf.fajardo', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(7, 'mariajo.villalobos', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(8, 'camiloan.perdomo', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(9, 'joseph.doqueresana', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(10, 'antonio.ayala', '41533f07825e9533126e3e4c02f1bb25669d8557693963d811891981031a2398', 1),
(11, 'joahn.hernandez', '062942f1940f3e9a7b81e701e35c47b34f8795c33957049eaf498129bf461111', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `VEHICULO`
--

CREATE TABLE `VEHICULO` (
  `ID_VEHICULO` int NOT NULL,
  `ID_TIPO_VEHICULO_FK` int NOT NULL,
  `PLACA` varchar(6) NOT NULL,
  `COLOR` varchar(20) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `VEHICULO_PERSONA`
--

CREATE TABLE `VEHICULO_PERSONA` (
  `ID_VEHICULO_PERSONA` int NOT NULL,
  `ID_VEHICULO_FK` int NOT NULL,
  `ID_PERSONA_FK` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ESTADO`
--
ALTER TABLE `ESTADO`
  ADD PRIMARY KEY (`ID_ESTADO`);

--
-- Indices de la tabla `PARQUEADERO`
--
ALTER TABLE `PARQUEADERO`
  ADD PRIMARY KEY (`ID_PARQUEADERO`),
  ADD KEY `ID_TIPO_VEHICULO_FK` (`ID_TIPO_VEHICULO_FK`),
  ADD KEY `ID_PISO_FK` (`ID_PISO_FK`);

--
-- Indices de la tabla `PERSONA`
--
ALTER TABLE `PERSONA`
  ADD PRIMARY KEY (`ID_PERSONA`),
  ADD KEY `ID_USUARIO_FK` (`ID_USUARIO_FK`);

--
-- Indices de la tabla `PISO`
--
ALTER TABLE `PISO`
  ADD PRIMARY KEY (`ID_PISO`);

--
-- Indices de la tabla `RESERVA`
--
ALTER TABLE `RESERVA`
  ADD PRIMARY KEY (`ID_RESERVA`),
  ADD KEY `ID_PARQUEADERO_FK` (`ID_PARQUEADERO_FK`),
  ADD KEY `ID_PERSONA_FK_FK` (`ID_PERSONA_FK_FK`),
  ADD KEY `ID_ESTADO_FK` (`ID_ESTADO_FK`);

--
-- Indices de la tabla `TIPO`
--
ALTER TABLE `TIPO`
  ADD PRIMARY KEY (`ID_TIPO`);

--
-- Indices de la tabla `TIPO_VEHICULO`
--
ALTER TABLE `TIPO_VEHICULO`
  ADD PRIMARY KEY (`ID_TIPO_VEHICULO`);

--
-- Indices de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  ADD PRIMARY KEY (`ID_USUARIO`),
  ADD KEY `ID_TIPO_FK` (`ID_TIPO_FK`);

--
-- Indices de la tabla `VEHICULO`
--
ALTER TABLE `VEHICULO`
  ADD PRIMARY KEY (`ID_VEHICULO`),
  ADD KEY `ID_TIPO_VEHICULO_FK` (`ID_TIPO_VEHICULO_FK`);

--
-- Indices de la tabla `VEHICULO_PERSONA`
--
ALTER TABLE `VEHICULO_PERSONA`
  ADD PRIMARY KEY (`ID_VEHICULO_PERSONA`),
  ADD KEY `ID_VEHICULO_FK` (`ID_VEHICULO_FK`),
  ADD KEY `ID_PERSONA_FK` (`ID_PERSONA_FK`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ESTADO`
--
ALTER TABLE `ESTADO`
  MODIFY `ID_ESTADO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `PARQUEADERO`
--
ALTER TABLE `PARQUEADERO`
  MODIFY `ID_PARQUEADERO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `PERSONA`
--
ALTER TABLE `PERSONA`
  MODIFY `ID_PERSONA` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `PISO`
--
ALTER TABLE `PISO`
  MODIFY `ID_PISO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `RESERVA`
--
ALTER TABLE `RESERVA`
  MODIFY `ID_RESERVA` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TIPO`
--
ALTER TABLE `TIPO`
  MODIFY `ID_TIPO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `TIPO_VEHICULO`
--
ALTER TABLE `TIPO_VEHICULO`
  MODIFY `ID_TIPO_VEHICULO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  MODIFY `ID_USUARIO` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `VEHICULO`
--
ALTER TABLE `VEHICULO`
  MODIFY `ID_VEHICULO` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `VEHICULO_PERSONA`
--
ALTER TABLE `VEHICULO_PERSONA`
  MODIFY `ID_VEHICULO_PERSONA` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `PARQUEADERO`
--
ALTER TABLE `PARQUEADERO`
  ADD CONSTRAINT `PARQUEADERO_ibfk_1` FOREIGN KEY (`ID_TIPO_VEHICULO_FK`) REFERENCES `TIPO_VEHICULO` (`ID_TIPO_VEHICULO`),
  ADD CONSTRAINT `PARQUEADERO_ibfk_2` FOREIGN KEY (`ID_PISO_FK`) REFERENCES `PISO` (`ID_PISO`);

--
-- Filtros para la tabla `PERSONA`
--
ALTER TABLE `PERSONA`
  ADD CONSTRAINT `PERSONA_ibfk_1` FOREIGN KEY (`ID_USUARIO_FK`) REFERENCES `USUARIO` (`ID_USUARIO`);

--
-- Filtros para la tabla `RESERVA`
--
ALTER TABLE `RESERVA`
  ADD CONSTRAINT `RESERVA_ibfk_1` FOREIGN KEY (`ID_PARQUEADERO_FK`) REFERENCES `PARQUEADERO` (`ID_PARQUEADERO`),
  ADD CONSTRAINT `RESERVA_ibfk_2` FOREIGN KEY (`ID_PERSONA_FK_FK`) REFERENCES `VEHICULO_PERSONA` (`ID_PERSONA_FK`),
  ADD CONSTRAINT `RESERVA_ibfk_3` FOREIGN KEY (`ID_ESTADO_FK`) REFERENCES `ESTADO` (`ID_ESTADO`);

--
-- Filtros para la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  ADD CONSTRAINT `USUARIO_ibfk_1` FOREIGN KEY (`ID_TIPO_FK`) REFERENCES `TIPO` (`ID_TIPO`);

--
-- Filtros para la tabla `VEHICULO`
--
ALTER TABLE `VEHICULO`
  ADD CONSTRAINT `VEHICULO_ibfk_1` FOREIGN KEY (`ID_TIPO_VEHICULO_FK`) REFERENCES `TIPO_VEHICULO` (`ID_TIPO_VEHICULO`);

--
-- Filtros para la tabla `VEHICULO_PERSONA`
--
ALTER TABLE `VEHICULO_PERSONA`
  ADD CONSTRAINT `VEHICULO_PERSONA_ibfk_1` FOREIGN KEY (`ID_VEHICULO_FK`) REFERENCES `VEHICULO` (`ID_VEHICULO`),
  ADD CONSTRAINT `VEHICULO_PERSONA_ibfk_2` FOREIGN KEY (`ID_PERSONA_FK`) REFERENCES `PERSONA` (`ID_PERSONA`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

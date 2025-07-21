-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-07-2025 a las 22:38:33
-- Versión del servidor: 9.3.0
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `smartclass`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id` int NOT NULL,
  `curso_id` int NOT NULL,
  `semana` varchar(100) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `link_zoom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clase`
--

INSERT INTO `clase` (`id`, `curso_id`, `semana`, `tema`, `link_zoom`) VALUES
(4, 1, '1', 'hola mundo', 'https://zoom.us/es/signin#/login');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `profesor_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `codigo`, `nombre`, `profesor_id`) VALUES
(1, 'CURS001', 'Java', 1),
(2, 'CURS002', 'SQL', 2),
(3, 'CURS003', 'HTML', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_usuario`
--

CREATE TABLE `curso_usuario` (
  `id` int NOT NULL,
  `curso_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `fecha_matricula` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `curso_usuario`
--

INSERT INTO `curso_usuario` (`id`, `curso_id`, `usuario_id`, `fecha_matricula`) VALUES
(26, 1, 11, '2025-07-20 11:29:56'),
(27, 3, 11, '2025-07-20 11:29:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_personales`
--

CREATE TABLE `detalles_personales` (
  `usuario_id` int NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `biografia` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalles_personales`
--

INSERT INTO `detalles_personales` (`usuario_id`, `fecha_nacimiento`, `estado_civil`, `ocupacion`, `celular`, `biografia`) VALUES
(11, '2004-02-05', 'Soltero', 'Estudiante', '987 654 321', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id` int NOT NULL,
  `semana_id` int NOT NULL,
  `descripcion` text,
  `archivo_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `material`
--

INSERT INTO `material` (`id`, `semana_id`, `descripcion`, `archivo_pdf`) VALUES
(4, 1, 'introduccion', 'https://drive.google.com/file/d/1HUi1VTlfGrC91ylbkLS0GVXH2yJK0YTL/view?usp=drive_link');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `curso_id` int DEFAULT NULL,
  `fecha_matricula` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `id_usuario`, `monto`, `fecha_pago`, `metodo_pago`, `estado_pago`) VALUES
(1, 11, 250.00, '2025-07-19 17:44:44', 'BCP', 'completado'),
(2, 11, 250.00, '2025-07-20 09:39:38', 'BBVA', 'completado'),
(3, 11, 300.00, '2025-07-20 11:29:47', 'Scotiabank', 'completado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id` int NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `descripcion` text,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `nombre`, `descripcion`, `monto`) VALUES
(1, 'Básico', 'Escoges 1 curso', 250.00),
(2, 'Intermedio', 'Escoges 2 cursos', 300.00),
(3, 'Premium', 'Escoges cursos ilimitados', 350.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`id`, `nombre`, `apellido`, `correo`, `contrasena`, `especialidad`, `telefono`, `estado`, `fecha_registro`) VALUES
(1, 'Luis', 'Ramírez', 'luis@gmail.com', '123456', 'Java', '987654321', 'activo', '2025-07-19 02:13:30'),
(2, 'Martín', 'Salazar', 'martin@gmail.com', '123456', 'SQL', '987654322', 'activo', '2025-07-19 02:13:30'),
(3, 'Diego', 'Herrera', 'diego@gmail.com', '123456', 'HTML', '987654323', 'activo', '2025-07-19 02:13:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_zoom`
--

CREATE TABLE `registro_zoom` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `curso_id` int DEFAULT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguridad_usuario`
--

CREATE TABLE `seguridad_usuario` (
  `usuario_id` int NOT NULL,
  `intentos_fallidos` int DEFAULT '0',
  `bloqueo_hasta` datetime DEFAULT NULL,
  `fecha_activacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semana`
--

CREATE TABLE `semana` (
  `id` int NOT NULL,
  `curso_id` int DEFAULT NULL,
  `titulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `semana`
--

INSERT INTO `semana` (`id`, `curso_id`, `titulo`) VALUES
(1, 1, 'Semana 01'),
(2, 1, 'Semana 02'),
(3, 1, 'Semana 03'),
(4, 1, 'Semana 04'),
(5, 1, 'Semana 05'),
(6, 1, 'Semana 06'),
(7, 1, 'Semana 07'),
(8, 1, 'Semana 08'),
(9, 1, 'Semana 09'),
(10, 1, 'Semana 10'),
(11, 1, 'Semana 11'),
(12, 1, 'Semana 12'),
(13, 1, 'Semana 13'),
(14, 1, 'Semana 14'),
(15, 2, 'Semana 01'),
(16, 2, 'Semana 02'),
(17, 2, 'Semana 03'),
(18, 2, 'Semana 04'),
(19, 2, 'Semana 05'),
(20, 2, 'Semana 06'),
(21, 2, 'Semana 07'),
(22, 2, 'Semana 08'),
(23, 2, 'Semana 09'),
(24, 2, 'Semana 10'),
(25, 2, 'Semana 11'),
(26, 2, 'Semana 12'),
(27, 2, 'Semana 13'),
(28, 2, 'Semana 14'),
(29, 3, 'Semana 01'),
(30, 3, 'Semana 02'),
(31, 3, 'Semana 03'),
(32, 3, 'Semana 04'),
(33, 3, 'Semana 05'),
(34, 3, 'Semana 06'),
(35, 3, 'Semana 07'),
(36, 3, 'Semana 08'),
(37, 3, 'Semana 09'),
(38, 3, 'Semana 10'),
(39, 3, 'Semana 11'),
(40, 3, 'Semana 12'),
(41, 3, 'Semana 13'),
(42, 3, 'Semana 14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `plan_id` int DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'inactivo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_activacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `correo`, `contrasena`, `plan_id`, `estado`, `fecha_registro`, `fecha_activacion`) VALUES
(11, 'Natalia', 'Rojas', 'natalia@gmail.com', '123456', 2, 'activo', '2025-07-19 02:24:53', '2025-07-20'),
(12, 'Andrés', 'Gómez', 'andres@gmail.com', '123456', 2, 'activo', '2025-07-19 02:24:53', NULL),
(13, 'Lucía', 'Fernández', 'lucia@gmail.com', '123456', 3, 'activo', '2025-07-19 02:24:53', NULL),
(14, 'Carlos', 'Mendoza', 'carlos@gmail.com', '123456', 1, 'activo', '2025-07-19 02:24:53', NULL),
(15, 'Martha', 'Silva', 'martha@gmail.com', '123456', 2, 'activo', '2025-07-19 02:24:53', NULL),
(16, 'Renzo', 'Caballero', 'renzo@gmail.com', '123456', 3, 'activo', '2025-07-19 02:24:53', NULL),
(17, 'Paola', 'Paredes', 'paola@gmail.com', '123456', 2, 'activo', '2025-07-19 02:24:53', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `curso_usuario`
--
ALTER TABLE `curso_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `detalles_personales`
--
ALTER TABLE `detalles_personales`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `celular` (`celular`);

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semana_id` (`semana_id`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `registro_zoom`
--
ALTER TABLE `registro_zoom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `seguridad_usuario`
--
ALTER TABLE `seguridad_usuario`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `semana`
--
ALTER TABLE `semana`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `plan_id` (`plan_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `curso_usuario`
--
ALTER TABLE `curso_usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `registro_zoom`
--
ALTER TABLE `registro_zoom`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `semana`
--
ALTER TABLE `semana`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesor` (`id`);

--
-- Filtros para la tabla `curso_usuario`
--
ALTER TABLE `curso_usuario`
  ADD CONSTRAINT `curso_usuario_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curso_usuario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalles_personales`
--
ALTER TABLE `detalles_personales`
  ADD CONSTRAINT `detalles_personales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`semana_id`) REFERENCES `semana` (`id`);

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `registro_zoom`
--
ALTER TABLE `registro_zoom`
  ADD CONSTRAINT `registro_zoom_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `registro_zoom_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `seguridad_usuario`
--
ALTER TABLE `seguridad_usuario`
  ADD CONSTRAINT `seguridad_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `semana`
--
ALTER TABLE `semana`
  ADD CONSTRAINT `semana_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

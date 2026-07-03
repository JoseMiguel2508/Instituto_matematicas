-- Archivo SQL generado con todas las tablas del proyecto Instituto de Matemáticas

DROP TABLE IF EXISTS `aula`;
CREATE TABLE `aula` (
  `id_aula` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_aula` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int DEFAULT NULL,
  `ubicacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id_aula`),
  UNIQUE KEY `aula_codigo_aula_unique` (`codigo_aula`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `caja`;
CREATE TABLE `caja` (
  `id_caja` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activa','Inactiva') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_caja`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cargo`;
CREATE TABLE `cargo` (
  `id_cargo` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_jerarquico` int DEFAULT NULL,
  PRIMARY KEY (`id_cargo`),
  UNIQUE KEY `cargo_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `concepto_pago`;
CREATE TABLE `concepto_pago` (
  `id_concepto` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_base` decimal(8,2) DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_obligatorio` tinyint(1) NOT NULL DEFAULT '0',
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_concepto`),
  UNIQUE KEY `concepto_pago_codigo_unique` (`codigo`),
  UNIQUE KEY `concepto_pago_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `curso`;
CREATE TABLE `curso` (
  `id_curso` int unsigned NOT NULL AUTO_INCREMENT,
  `id_especialidad` int unsigned NOT NULL,
  `id_nivel` int unsigned NOT NULL,
  `codigo_curso` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_curso` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creditos` int DEFAULT NULL,
  `duracion_horas` int DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_curso`),
  UNIQUE KEY `curso_codigo_curso_unique` (`codigo_curso`),
  UNIQUE KEY `curso_nombre_curso_unique` (`nombre_curso`),
  KEY `curso_id_especialidad_foreign` (`id_especialidad`),
  KEY `curso_id_nivel_foreign` (`id_nivel`),
  KEY `IX_CURSO_estado` (`estado`),
  CONSTRAINT `curso_id_especialidad_foreign` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`),
  CONSTRAINT `curso_id_nivel_foreign` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `curso_prerrequisito`;
CREATE TABLE `curso_prerrequisito` (
  `id_prerequisito` int unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int unsigned NOT NULL,
  `id_curso_prerequisito` int unsigned NOT NULL,
  `nota_minima` decimal(4,2) NOT NULL DEFAULT '11.00',
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Obligatorio',
  `condicion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aprobado',
  PRIMARY KEY (`id_prerequisito`),
  UNIQUE KEY `uq_curso_prereq` (`id_curso`,`id_curso_prerequisito`),
  KEY `curso_prerrequisito_id_curso_prerequisito_foreign` (`id_curso_prerequisito`),
  CONSTRAINT `curso_prerrequisito_id_curso_foreign` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE,
  CONSTRAINT `curso_prerrequisito_id_curso_prerequisito_foreign` FOREIGN KEY (`id_curso_prerequisito`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `detalle_inscripcion`;
CREATE TABLE `detalle_inscripcion` (
  `id_detalle_inscripcion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_inscripcion` int unsigned NOT NULL,
  `id_grupo` int unsigned NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inscrito',
  PRIMARY KEY (`id_detalle_inscripcion`),
  UNIQUE KEY `uq_det_inscripcion_ins_grp` (`id_inscripcion`,`id_grupo`),
  KEY `IX_DETALLE_INSCRIPCION_grupo` (`id_grupo`),
  KEY `IX_DETALLE_INSCRIPCION_inscripcion` (`id_inscripcion`),
  CONSTRAINT `detalle_inscripcion_id_grupo_foreign` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`),
  CONSTRAINT `detalle_inscripcion_id_inscripcion_foreign` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripcion` (`id_inscripcion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `detalle_pago`;
CREATE TABLE `detalle_pago` (
  `id_detalle_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `id_pago` int unsigned NOT NULL,
  `id_concepto` int unsigned NOT NULL,
  `id_deuda` int unsigned DEFAULT NULL,
  `monto_aplicado` decimal(8,2) NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_detalle_pago`),
  KEY `IX_DETALLE_PAGO_pago` (`id_pago`),
  KEY `IX_DETALLE_PAGO_concepto` (`id_concepto`),
  KEY `detalle_pago_id_deuda_foreign` (`id_deuda`),
  CONSTRAINT `detalle_pago_id_concepto_foreign` FOREIGN KEY (`id_concepto`) REFERENCES `concepto_pago` (`id_concepto`),
  CONSTRAINT `detalle_pago_id_deuda_foreign` FOREIGN KEY (`id_deuda`) REFERENCES `deuda_estudiante` (`id_deuda`) ON DELETE SET NULL,
  CONSTRAINT `detalle_pago_id_pago_foreign` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id_pago`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `deuda_estudiante`;
CREATE TABLE `deuda_estudiante` (
  `id_deuda` int unsigned NOT NULL AUTO_INCREMENT,
  `id_estudiante` int unsigned NOT NULL,
  `id_periodo` int unsigned NOT NULL,
  `id_concepto` int unsigned NOT NULL,
  `monto` decimal(8,2) NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_generacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_deuda`),
  KEY `deuda_estudiante_id_estudiante_foreign` (`id_estudiante`),
  KEY `deuda_estudiante_id_periodo_foreign` (`id_periodo`),
  KEY `deuda_estudiante_id_concepto_foreign` (`id_concepto`),
  CONSTRAINT `deuda_estudiante_id_concepto_foreign` FOREIGN KEY (`id_concepto`) REFERENCES `concepto_pago` (`id_concepto`) ON DELETE CASCADE,
  CONSTRAINT `deuda_estudiante_id_estudiante_foreign` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`) ON DELETE CASCADE,
  CONSTRAINT `deuda_estudiante_id_periodo_foreign` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `docente`;
CREATE TABLE `docente` (
  `id_docente` int unsigned NOT NULL,
  `codigo_docente` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grado_academico` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_docente`),
  UNIQUE KEY `docente_codigo_docente_unique` (`codigo_docente`),
  KEY `IX_DOCENTE_estado` (`estado`),
  CONSTRAINT `docente_id_docente_foreign` FOREIGN KEY (`id_docente`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `docente_especializacion`;
CREATE TABLE `docente_especializacion` (
  `id_docente_especializacion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_docente` int unsigned NOT NULL,
  `id_especializacion_docente` int unsigned NOT NULL,
  `nivel_experiencia` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_docente_especializacion`),
  UNIQUE KEY `uq_doc_esp` (`id_docente`,`id_especializacion_docente`),
  KEY `docente_especializacion_id_especializacion_docente_foreign` (`id_especializacion_docente`),
  CONSTRAINT `docente_especializacion_id_docente_foreign` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE,
  CONSTRAINT `docente_especializacion_id_especializacion_docente_foreign` FOREIGN KEY (`id_especializacion_docente`) REFERENCES `especializacion_docente` (`id_especializacion_docente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `empleado`;
CREATE TABLE `empleado` (
  `id_empleado` int unsigned NOT NULL,
  `codigo_empleado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_cargo` int unsigned NOT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `tipo_contrato` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_empleado`),
  UNIQUE KEY `empleado_codigo_empleado_unique` (`codigo_empleado`),
  KEY `empleado_id_cargo_foreign` (`id_cargo`),
  KEY `IX_EMPLEADO_estado` (`estado`),
  CONSTRAINT `empleado_id_cargo_foreign` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
  CONSTRAINT `empleado_id_empleado_foreign` FOREIGN KEY (`id_empleado`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `especialidad`;
CREATE TABLE `especialidad` (
  `id_especialidad` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activa',
  PRIMARY KEY (`id_especialidad`),
  UNIQUE KEY `especialidad_codigo_unique` (`codigo`),
  UNIQUE KEY `especialidad_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `especializacion_docente`;
CREATE TABLE `especializacion_docente` (
  `id_especializacion_docente` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_especializacion_docente`),
  UNIQUE KEY `especializacion_docente_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `estudiante`;
CREATE TABLE `estudiante` (
  `id_estudiante` int unsigned NOT NULL,
  `codigo_estudiante` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_estudiante`),
  UNIQUE KEY `estudiante_codigo_estudiante_unique` (`codigo_estudiante`),
  KEY `IX_ESTUDIANTE_estado` (`estado`),
  CONSTRAINT `estudiante_id_estudiante_foreign` FOREIGN KEY (`id_estudiante`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `grupo`;
CREATE TABLE `grupo` (
  `id_grupo` int unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int unsigned NOT NULL,
  `id_docente` int unsigned NOT NULL,
  `id_aula` int unsigned NOT NULL,
  `id_periodo` int unsigned NOT NULL,
  `numero_grupo` int NOT NULL,
  `cupo_maximo` int DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Abierto',
  PRIMARY KEY (`id_grupo`),
  UNIQUE KEY `uq_grupo_cur_per_num` (`id_curso`,`id_periodo`,`numero_grupo`),
  KEY `grupo_id_aula_foreign` (`id_aula`),
  KEY `IX_GRUPO_periodo` (`id_periodo`),
  KEY `IX_GRUPO_docente` (`id_docente`),
  KEY `IX_GRUPO_curso` (`id_curso`),
  CONSTRAINT `grupo_id_aula_foreign` FOREIGN KEY (`id_aula`) REFERENCES `aula` (`id_aula`),
  CONSTRAINT `grupo_id_curso_foreign` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`),
  CONSTRAINT `grupo_id_docente_foreign` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  CONSTRAINT `grupo_id_periodo_foreign` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `horario`;
CREATE TABLE `horario` (
  `id_horario` int unsigned NOT NULL AUTO_INCREMENT,
  `id_grupo` int unsigned NOT NULL,
  `dia_semana` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `IX_HORARIO_grupo` (`id_grupo`),
  CONSTRAINT `horario_id_grupo_foreign` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `inscripcion`;
CREATE TABLE `inscripcion` (
  `id_inscripcion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_matricula` int unsigned NOT NULL,
  `fecha_inscripcion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activa',
  `id_usuario_registra` int unsigned NOT NULL,
  PRIMARY KEY (`id_inscripcion`),
  KEY `inscripcion_id_usuario_registra_foreign` (`id_usuario_registra`),
  KEY `IX_INSCRIPCION_matricula` (`id_matricula`),
  CONSTRAINT `inscripcion_id_matricula_foreign` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE CASCADE,
  CONSTRAINT `inscripcion_id_usuario_registra_foreign` FOREIGN KEY (`id_usuario_registra`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `log_actividad`;
CREATE TABLE `log_actividad` (
  `id_log` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `accion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_registro_afectado` int DEFAULT NULL,
  `datos_anteriores` longtext COLLATE utf8mb4_unicode_ci,
  `datos_nuevos` longtext COLLATE utf8mb4_unicode_ci,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `direccion_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modulo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_log`),
  KEY `IX_LOG_ACTIVIDAD_usuario` (`id_usuario`),
  KEY `IX_LOG_ACTIVIDAD_fecha` (`fecha_hora`),
  CONSTRAINT `log_actividad_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `matricula`;
CREATE TABLE `matricula` (
  `id_matricula` int unsigned NOT NULL AUTO_INCREMENT,
  `id_estudiante` int unsigned NOT NULL,
  `id_periodo` int unsigned NOT NULL,
  `id_especialidad` int unsigned NOT NULL,
  `fecha_matricula` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Regular',
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activa',
  `observaciones` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_usuario_registra` int unsigned NOT NULL,
  PRIMARY KEY (`id_matricula`),
  UNIQUE KEY `uq_matricula_est_per` (`id_estudiante`,`id_periodo`),
  KEY `matricula_id_especialidad_foreign` (`id_especialidad`),
  KEY `matricula_id_usuario_registra_foreign` (`id_usuario_registra`),
  KEY `IX_MATRICULA_periodo` (`id_periodo`),
  KEY `IX_MATRICULA_estudiante` (`id_estudiante`),
  CONSTRAINT `matricula_id_especialidad_foreign` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`),
  CONSTRAINT `matricula_id_estudiante_foreign` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `matricula_id_periodo_foreign` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`),
  CONSTRAINT `matricula_id_usuario_registra_foreign` FOREIGN KEY (`id_usuario_registra`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `nivel`;
CREATE TABLE `nivel` (
  `id_nivel` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_nivel`),
  UNIQUE KEY `nivel_nombre_unique` (`nombre`),
  UNIQUE KEY `nivel_orden_unique` (`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `nota_final`;
CREATE TABLE `nota_final` (
  `id_nota_final` int unsigned NOT NULL AUTO_INCREMENT,
  `id_detalle_inscripcion` int unsigned NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_usuario_registra` int unsigned NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_nota_final`),
  UNIQUE KEY `nota_final_id_detalle_inscripcion_unique` (`id_detalle_inscripcion`),
  KEY `nota_final_id_usuario_registra_foreign` (`id_usuario_registra`),
  KEY `IX_NOTA_FINAL_detalle` (`id_detalle_inscripcion`),
  CONSTRAINT `nota_final_id_detalle_inscripcion_foreign` FOREIGN KEY (`id_detalle_inscripcion`) REFERENCES `detalle_inscripcion` (`id_detalle_inscripcion`) ON DELETE CASCADE,
  CONSTRAINT `nota_final_id_usuario_registra_foreign` FOREIGN KEY (`id_usuario_registra`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pago`;
CREATE TABLE `pago` (
  `id_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sesion_caja` bigint unsigned DEFAULT NULL,
  `id_estudiante` int unsigned NOT NULL,
  `id_matricula` int unsigned DEFAULT NULL,
  `id_usuario_registra` int unsigned NOT NULL,
  `numero_comprobante` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_comprobante` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_total` decimal(8,2) NOT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Registrado',
  `observaciones` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pago_id_matricula_foreign` (`id_matricula`),
  KEY `pago_id_usuario_registra_foreign` (`id_usuario_registra`),
  KEY `IX_PAGO_estudiante` (`id_estudiante`),
  KEY `IX_PAGO_fecha` (`fecha_pago`),
  KEY `pago_id_sesion_caja_foreign` (`id_sesion_caja`),
  CONSTRAINT `pago_id_estudiante_foreign` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `pago_id_matricula_foreign` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE SET NULL,
  CONSTRAINT `pago_id_sesion_caja_foreign` FOREIGN KEY (`id_sesion_caja`) REFERENCES `sesion_caja` (`id_sesion_caja`),
  CONSTRAINT `pago_id_usuario_registra_foreign` FOREIGN KEY (`id_usuario_registra`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `periodo_academico`;
CREATE TABLE `periodo_academico` (
  `id_periodo` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Planificado',
  PRIMARY KEY (`id_periodo`),
  UNIQUE KEY `periodo_academico_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `persona`;
CREATE TABLE `persona` (
  `id_persona` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_persona`),
  UNIQUE KEY `persona_numero_documento_unique` (`numero_documento`),
  KEY `IX_PERSONA_apellidos` (`apellidos`,`nombres`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `rol`;
CREATE TABLE `rol` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `rol_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sesion_caja`;
CREATE TABLE `sesion_caja` (
  `id_sesion_caja` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_caja` bigint unsigned NOT NULL,
  `id_usuario_apertura` int unsigned NOT NULL,
  `id_usuario_cierre` int unsigned DEFAULT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `monto_final_esperado` decimal(10,2) DEFAULT NULL,
  `monto_final_real` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observaciones_apertura` text COLLATE utf8mb4_unicode_ci,
  `observaciones_cierre` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('Abierta','Cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Abierta',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_sesion_caja`),
  KEY `sesion_caja_id_caja_foreign` (`id_caja`),
  KEY `sesion_caja_id_usuario_apertura_foreign` (`id_usuario_apertura`),
  KEY `sesion_caja_id_usuario_cierre_foreign` (`id_usuario_cierre`),
  CONSTRAINT `sesion_caja_id_caja_foreign` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id_caja`),
  CONSTRAINT `sesion_caja_id_usuario_apertura_foreign` FOREIGN KEY (`id_usuario_apertura`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `sesion_caja_id_usuario_cierre_foreign` FOREIGN KEY (`id_usuario_cierre`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int unsigned NOT NULL AUTO_INCREMENT,
  `id_persona` int unsigned DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  `intentos_fallidos` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario_username_unique` (`username`),
  KEY `usuario_id_persona_foreign` (`id_persona`),
  KEY `IX_USUARIO_estado` (`estado`),
  CONSTRAINT `usuario_id_persona_foreign` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuario_rol`;
CREATE TABLE `usuario_rol` (
  `id_usuario_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `id_rol` int unsigned NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario_rol`),
  UNIQUE KEY `usuario_rol_id_usuario_id_rol_unique` (`id_usuario`,`id_rol`),
  KEY `usuario_rol_id_rol_foreign` (`id_rol`),
  CONSTRAINT `usuario_rol_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE,
  CONSTRAINT `usuario_rol_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


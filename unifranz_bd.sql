-- MySQL dump 10.13  Distrib 8.0.28, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: unifranz_db
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dimensiones_id` int NOT NULL,
  `tipo_area_id` int NOT NULL,
  `criterio_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dimensiones_id` (`dimensiones_id`),
  KEY `tipo_area_id` (`tipo_area_id`),
  KEY `fk_criterio_id` (`criterio_id`),
  CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`dimensiones_id`) REFERENCES `dimensiones` (`id`),
  CONSTRAINT `categorias_ibfk_2` FOREIGN KEY (`tipo_area_id`) REFERENCES `tipo_area` (`id`),
  CONSTRAINT `fk_criterio_id` FOREIGN KEY (`criterio_id`) REFERENCES `criterio` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Características de la Carrera y su inserción institucional',1,'2025-04-07 00:32:36',1,1,11),(2,'Organización, gobierno, gestión y administración de la carrera',1,'2025-04-07 00:32:36',1,1,2),(3,'Sistema de evaluación del proceso de gestión',1,'2025-04-07 00:32:36',1,1,3),(4,'Procesos de admisión y de incorporación',1,'2025-04-07 00:32:36',1,1,4),(5,'Políticas y programas de bienestar institucional',1,'2025-04-07 00:32:36',1,1,5),(6,'Proceso de autoevaluación',1,'2025-04-07 00:32:36',1,1,6),(7,'Plan de estudios',1,'2025-04-07 00:32:36',2,1,7),(8,'Investigación y desarrollo tecnológico',1,'2025-04-07 00:32:36',2,1,8),(9,'Vinculación, Extensión y Cooperación',1,'2025-04-07 00:32:36',2,1,9),(10,'Estudiantes',1,'2025-04-07 00:32:36',3,1,10),(11,'Graduados',1,'2025-04-07 00:32:36',3,1,1),(12,'Docentes',1,'2025-04-07 00:32:36',3,1,2),(13,'Personal de apoyo',1,'2025-04-07 00:32:36',3,1,3),(14,'Infraestructura Física y Logística',1,'2025-04-07 00:32:36',4,1,4),(15,'Bibliotecas',1,'2025-04-07 00:32:36',4,1,5),(16,'Instalaciones Especiales y Laboratorio',1,'2025-04-07 00:32:36',4,1,6),(17,'Características de la Carrera y su inserción institucional',1,'2025-04-07 00:47:05',1,2,7),(18,'Organización, gobierno, gestión y administración de la carrera',1,'2025-04-07 00:47:05',1,2,8),(19,'Sistema de evaluación del proceso de gestión',1,'2025-04-07 00:47:05',1,2,9),(20,'Procesos de admisión',1,'2025-04-07 00:47:05',1,2,10),(21,'Políticas y programas de bienestar institucional',1,'2025-04-07 00:47:05',1,2,1),(22,'Proceso de autoevaluación',1,'2025-04-07 00:47:05',1,2,2),(23,'Plan de estudios',1,'2025-04-07 00:47:05',2,2,3),(24,'Procesos de Enseñanza/Aprendizaje',1,'2025-04-07 00:47:05',2,2,4),(25,'Investigación y desarrollo tecnológico',1,'2025-04-07 00:47:05',2,2,5),(26,'Extensión e Interacción social',1,'2025-04-07 00:47:05',2,2,6),(27,'Vinculación y cooperación',1,'2025-04-07 00:47:05',2,2,7),(28,'Divulgación de la producción académica',1,'2025-04-07 00:47:05',2,2,8),(29,'Estudiantes',1,'2025-04-07 00:47:05',3,2,9),(30,'Graduados',1,'2025-04-07 00:47:05',3,2,10),(31,'Docentes',1,'2025-04-07 00:47:05',3,2,1),(32,'Personal de apoyo',1,'2025-04-07 00:47:05',3,2,2),(33,'Infraestructura Física y Logística',1,'2025-04-07 00:47:05',4,2,3),(34,'Clínicas de Atención',1,'2025-04-07 00:47:05',4,2,4),(35,'Biblioteca y Hemeroteca',1,'2025-04-07 00:47:05',4,2,5),(36,'Laboratorios e Instalaciones especiales',1,'2025-04-07 00:47:05',4,2,6),(37,'Accesibilidad y circulación',1,'2025-04-07 00:47:05',4,2,7),(38,'prueba',1,'2025-04-09 17:06:23',1,2,2);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criterio`
--

DROP TABLE IF EXISTS `criterio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `criterio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(350) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterio`
--

LOCK TABLES `criterio` WRITE;
/*!40000 ALTER TABLE `criterio` DISABLE KEYS */;
INSERT INTO `criterio` VALUES (1,'La carrera dicatada en ambiente de docencia, investigación y extensión/vinculación con el medio.',1,'2025-04-07 17:17:00'),(2,'La misión, la visión, los objetivos y los planes de desarrollo de la institución y la carrera son explícitos, con metas a corto, mediano y largo plazo, son coherentes entre sí y están aprobados por las instancias institucionales correspondientes',1,'2025-04-07 17:19:02'),(3,'Mecanismos de participación de la comunidad universitaria en el desarrollo y rediseño del plan o de las orientaciones estratégicas',1,'2025-04-07 17:20:18'),(4,'Programas y proyectos de investigación y extensión/vinculación con el medio de acuerdo con políticas y lineamientos definidos por la institución y/o por la carrera.',1,'2025-04-07 17:20:31'),(5,'La institución debe desarrollar programas de postítulo o posgrado.',1,'2025-04-07 17:20:45'),(6,'Coherencia entre las formas de gobierno, la estructura organizacional y administrativa, los mecanismos de participación de la comunidad universitaria, los objetivos y los logros del proyecto académico.',1,'2025-04-07 17:21:08'),(7,'Formalización de los procedimientos para la elección, selección, designación y evaluación de autoridades, directivos y funcionarios de la institución y de la carrera',1,'2025-04-07 17:21:37'),(8,'El presupuesto debe ser conocido y los mecanismos de asignación interna de recursos deben ser explícitos',1,'2025-04-07 17:21:52'),(9,'El financiamiento de las actividades académicas, del personal técnico y administrativo y para el desarrollo de los planes de mantenimiento y expansión de infraestructura, laboratorios y biblioteca debe estar garantizado para, al menos, el término de duración de las cohortes actuales de la carrera.',1,'2025-04-07 17:22:22'),(10,'Deben implementarse mecanismos de evaluación continua de la gestión, con participación de todos los estamentos de la comunidad universitaria, los que deben ser, a su vez, periódicamente evaluados.',1,'2025-04-07 17:22:35'),(11,'Los procesos de admisión deben estar explicitados y ser conocidos por los postulantes',1,'2025-04-07 17:22:55'),(12,'Nuevo Criterio',0,'2025-04-09 17:14:18'),(13,'prueba',0,'2025-04-09 18:11:51'),(14,'prueba2',0,'2025-04-09 18:45:56'),(15,'prueba2',0,'2025-04-09 18:45:56'),(16,'prueba3',0,'2025-04-09 19:08:22');
/*!40000 ALTER TABLE `criterio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dimensiones`
--

DROP TABLE IF EXISTS `dimensiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dimensiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `usuario_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `dimensiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dimensiones`
--

LOCK TABLES `dimensiones` WRITE;
/*!40000 ALTER TABLE `dimensiones` DISABLE KEYS */;
INSERT INTO `dimensiones` VALUES (1,'Documentación Legal',1,1,'2025-04-07 00:13:09'),(2,'Documentación Académica',1,1,'2025-04-07 00:13:09'),(3,'Comunidad Estudiantil',1,1,'2025-04-07 00:13:09'),(4,'Infraestructura',1,1,'2025-04-07 00:13:09'),(5,'Prueba',0,1,'2025-04-09 17:19:19');
/*!40000 ALTER TABLE `dimensiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `formato` varchar(350) DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `codigo_documento` varchar(50) NOT NULL,
  `link_documento` varchar(350) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_documento` (`codigo_documento`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
INSERT INTO `documentos` VALUES (3,'Estatuto Orgánico','Estatuto organico','Físico',1,1,'D1_1.1.1_A1','https://drive.google.com/file/d/1dde_UbBkrBL1bcSox_kxfb2BnYHjck43/view?pli=1','2025-04-07 01:11:04','2025-04-16 05:52:58','2025-04-15 04:00:00',1,'2025-04-07 01:11:04'),(4,'Estatuto Orgánico','Estatuto Orgánico','Pdf',1,17,'D1_1.1.1_1','https://drive.google.com/file/d/1izzajDiLcgmKcLi-RbMyff2aaqnNhmFZ/view?usp=drive_link','2025-04-07 01:14:59','2025-04-15 19:21:02','2025-04-15 04:00:00',1,'2025-04-07 01:14:59'),(5,'Resolución Ministerial N° 890-16 - Aprobación de Estatuto Orgánico','Resolución Ministerial N° 890-16 - Aprobación de Estatuto Orgánico','Fisico',1,1,'D1_1.1.1_A2','https://drive.google.com/open?id=1yKwYd47bnxRoXxBiFE5T0vv1MKBSBVHJ&usp=drive_copy','2025-04-07 01:43:32','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:43:32'),(6,'Reglamento General De Universidades Privadas','Reglamento General De Universidades Privadas','Fisico',1,1,'D1_1.1.1_A3','https://drive.google.com/open?id=1pRKPlRBAh_7E1_AoIOVFFdPNkaSAVEny&usp=drive_copy','2025-04-07 01:44:43','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:44:43'),(7,'Resolución 221-99 - Autorización de Apertura de la Carrera de Medicina','Resolución 221-99 - Autorización de Apertura de la Carrera de Medicina','Fisico',1,1,'D1_1.1.1_A4','https://drive.google.com/open?id=1N0ua301WisoJlc55NO-5BVpxn1HRF2Qa&usp=drive_copy','2025-04-07 01:53:15','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:53:15'),(8,'Resolución Ministerial Nro 891 - 22 de diciembre del 2016 - Aprobación de Rediseño Curricular','Resolución Ministerial Nro 891 - 22 de diciembre del 2016 - Aprobación de Rediseño Curricular','Fisico',1,1,'D1_1.1.1_A5','https://drive.google.com/open?id=1otG2_yKiG3EcNytA0laG47C07WTeNIrV&usp=drive_copy','2025-04-07 01:53:57','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:53:57'),(9,'Organigrama Institucional Sede La Paz','Organigrama Institucional Sede La Paz','Fisico',1,1,'D1_1.1.1_A6','https://drive.google.com/open?id=1PofxsdPl1i--bXMiiy-ZCbVqiPvBwZ90&usp=drive_copy','2025-04-07 01:54:39','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:54:39'),(10,'Organigrama Académico_Sede La Paz','Organigrama Académico_Sede La Paz','Fisico',1,1,'D1_1.1.1_A7','https://drive.google.com/open?id=1IIFgcBmLx3NyLbRo5GutMfWbh-U4eu4C&usp=drive_copy','2025-04-07 01:55:16','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:55:16'),(11,'Organigrama A._Investigación','Organigrama A._Investigación','Fisico',1,1,'D1_1.1.1_A8','https://drive.google.com/open?id=1ZTODWQRIRSIK8ldb0VNUWMqVmJo0tiWd&usp=drive_copy','2025-04-07 01:56:07','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:56:07'),(12,'Reglamento de Investigación','Reglamento de Investigación','Fisico',1,1,'D1_1.1.1_A9','https://drive.google.com/open?id=19_Hvy86ng-MXnYiCym5UVjDRg8OJnF_t&usp=drive_copy','2025-04-07 01:56:38','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:56:38'),(13,'Políticas de Investigación','Políticas de Investigación','Fisico',1,1,'D1_1.1.1_A10','https://drive.google.com/open?id=1IOGniRD6PBtyO5sf0vUVgodwPUoFUVMf&usp=drive_copy','2025-04-07 01:57:18','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:57:18'),(14,'Procedimientos de Investigación','Procedimientos de Investigación','Fisico',1,1,'D1_1.1.1_A11','https://drive.google.com/open?id=1UKxNE6xA2bhmyFRD_klm6MFC4Y6ydNFb&usp=drive_copy','2025-04-07 01:57:55','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:57:55'),(15,'Organigrama A._Vinculación Social y Extensión.pdf','Organigrama A._Vinculación Social y Extensión.pdf','Fisico',1,1,'D1_1.1.1_A12','https://drive.google.com/open?id=1iXPi4t-DRU48h-C8qSWG9DOejBM583uk&usp=drive_copy','2025-04-07 01:58:52','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:58:52'),(16,'Política de Interacción Social','Política de Interacción Social','Fisico',1,1,'D1_1.1.1_A13','https://drive.google.com/open?id=10MZo5y9VU4t21f8UTSUY5af4mTDI5_kC&usp=drive_copy','2025-04-07 01:59:12','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:59:12'),(17,'Política de Interacción Social para el Área de Salud','Política de Interacción Social para el Área de Salud','Fisico',1,1,'D1_1.1.1_A14','https://drive.google.com/open?id=1jLIU7rimxCNONw723b8rxXq35BVx5P59&usp=drive_copy','2025-04-07 01:59:38','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 01:59:38'),(18,'Política de Extensión Universitaria','Política de Extensión Universitaria','Fisico',1,1,'D1_1.1.1_A15','https://drive.google.com/open?id=1Okke4RdOqwVQPG9z3TvPoFRyGWcqAspJ&usp=drive_copy','2025-04-07 02:00:18','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:00:18'),(19,'Política de Cooperación Interinstitucional','Política de Cooperación Interinstitucional','Fisico',1,1,'D1_1.1.1_A16','https://drive.google.com/open?id=1UwiINCl46ARMn4SfE8yQ0l4CMUOLebh3&usp=drive_copy','2025-04-07 02:01:11','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:01:11'),(20,'Reglamento de Interacción Social y Difusión Cultural','Reglamento de Interacción Social y Difusión Cultural','Fisico',1,1,'D1_1.1.1_A17','https://drive.google.com/open?id=1rmuvIWPGjeosaFca5tFQ3-BvUmsGAOLV&usp=drive_copy','2025-04-07 02:02:16','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:02:16'),(21,'Procedimientos de Actividades de Extensión y Vinculación con el Medio','Procedimientos de Actividades de Extensión y Vinculación con el Medio','Digital',1,1,'D1_1.1.1_A18','https://drive.google.com/open?id=18ZENw96Vg1UC_dFtAKwJi8ZIY4V-FlAg&usp=drive_copy','2025-04-07 02:02:51','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:02:51'),(22,'Reglamento Institucional','Reglamento Institucional','Fisico',1,1,'D1_1.1.1_A19','https://drive.google.com/open?id=1gMjPp0y4rFIM92GNFE3qOECe6iBo_9jc&usp=drive_copy','2025-04-07 02:03:56','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:03:56'),(23,'Reglamento de Autoevaluación','Reglamento de Autoevaluación','Fisico',1,1,'D1_1.1.1_A20','https://drive.google.com/open?id=1-eEPChltp_1w0Dusg6ZUZOrt5EP0_Kqu&usp=drive_copy','2025-04-07 02:18:18','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:18:18'),(24,'APROBACIÓN REGLAMENTO DE AUTOEVALUACIÓN EN ACREDITACIÓN CON FIRMA RECTOR','APROBACIÓN REGLAMENTO DE AUTOEVALUACIÓN EN ACREDITACIÓN CON FIRMA RECTOR','Fisico',1,1,'D1_1.1.1_A21','https://drive.google.com/open?id=1SkfcOrUhXAFI0EXAEcoIwfczJnl2cH_r&usp=drive_copy','2025-04-07 02:24:52','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:24:52'),(25,'Reglamento Docente','Reglamento Docente','Fisico',1,1,'D1_1.1.1_A22','https://drive.google.com/open?id=1KLs3WDyQ7okGiP0dQpW3ViEwQWGVrIkd&usp=drive_copy','2025-04-07 02:25:45','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:25:45'),(26,'Reglamento de Graduación','Reglamento de Graduación','Fisico',1,1,'D1_1.1.1_A23','https://drive.google.com/open?id=1PFtHoFb91Olo7cy1N0qKaE6HJEBObs_-&usp=drive_copy','2025-04-07 02:26:13','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:26:13'),(27,'Reglamento Estudiantil','Reglamento Estudiantil','Fisico',1,1,'D1_1.1.1_A24','https://drive.google.com/open?id=1l0jP_DfVfVvoy3agJlWEieVL-sWxWN5k&usp=drive_copy','2025-04-07 02:26:35','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:26:35'),(28,'Reglamento de Becas','Reglamento de Becas','Fisico',1,1,'D1_1.1.1_A25','https://drive.google.com/open?id=1N13yjw2-Xy9GZwwBybLyVavAtnadHKZc&usp=drive_copy','2025-04-07 02:26:56','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:26:56'),(29,'Reglamento de Personal Administrativo','Reglamento de Personal Administrativo','Fisico',1,1,'D1_1.1.1_A26','https://drive.google.com/open?id=1fj9jZdPLYTlP3lFeD6q8xzVZvI2ffg46&usp=drive_copyde Personal Administrativo','2025-04-07 02:27:35','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:27:35'),(30,'Plan de Desarrollo Institucional PDI 2017-2022','Plan de Desarrollo Institucional PDI 2017-2022','Digital',1,1,'D1_1.1.2_A1','https://drive.google.com/open?id=1IvSPe3Nui6AiroLsv0TaKecyUqkuey9EXLCJ071AooY&usp=drive_copy','2025-04-07 02:29:24','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:29:24'),(31,'Plan-Estratégico-2023-2028','Plan-Estratégico-2023-2028','Fisico',1,1,'D1_1.1.2_A2','https://drive.google.com/open?id=1aESXbrGFjkGqU8Sifz6hW8X46j4qwH2v&usp=drive_copy','2025-04-07 02:29:58','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:29:58'),(32,'Despliegue & Alineación PDI_OKR_2023-2028','Despliegue & Alineación PDI_OKR_2023-2028','Digital',1,1,'D1_1.1.2_A3','https://drive.google.com/open?id=12GtkJX6fbH3CRwXeHeOnrMi2Xo6TchLs&usp=drive_copy','2025-04-07 02:31:10','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:31:10'),(33,'Resolución de Directorio No. 01/2016 - Nueva filosofía y visión institucional','Resolución de Directorio No. 01/2016 - Nueva filosofía y visión institucional','Fisico',1,1,'D1_1.1.2_A4','https://drive.google.com/open?id=1-Z4X36yAbFVejHNKg8Fkh9n9zlEufQHE&usp=drive_copy','2025-04-07 02:35:39','2025-04-16 05:52:58','2025-04-06 04:00:00',1,'2025-04-07 02:35:39'),(34,'ESE 2022-2024','ESE 2022-2024','Fisico',1,2,'D1_1.1.3_A4','https://drive.google.com/open?id=1PV5-m6NKx3tOMT1361wN8CRa0nEhJA8Qi6Y_zO_wVI4&usp=drive_copy','2025-04-07 17:54:49','2025-04-16 05:52:58','2025-04-07 04:00:00',1,'2025-04-07 17:54:49'),(35,'Resolución Ministerial 890-2016-Aprobación de Estatuto Orgánico','Resolución Ministerial 890-2016-Aprobación de Estatuto Orgánico','Fisico',1,17,'D1_1.1.1_2','https://drive.google.com/file/d/1Ka-CmUmktOR5huiqqDnyfYpQBUav5DAX/view?usp=drive_link','2025-04-07 18:23:42','2025-04-15 19:21:02','2025-04-07 04:00:00',1,'2025-04-07 18:23:42'),(36,'Reglamento General Universidades Privadas','Reglamento General Universidades Privadas','Fisico',1,18,'D1_1.1.1_3','https://drive.google.com/file/d/1kk6rVfxnY-zNZ_EW_AdFosApcSZrEpLS/view?usp=drive_link','2025-04-08 16:36:19','2025-04-15 19:21:02','2025-04-08 04:00:00',1,'2025-04-08 16:36:19'),(37,'Admisión de Estudiantes Nuevos - Pregrado','Admisión de Estudiantes Nuevos - Pregrado','Digital',1,10,'D3_3.1.1_A1','https://drive.google.com/open?id=1WCFZwUrveUJETcNrY6qzoMJwcBqDMh-R&usp=drive_copy','2025-04-15 06:23:33','2025-04-16 05:52:58','2025-04-15 04:00:00',1,'2025-04-15 06:23:33'),(38,'Planos unifranz','Planos unifranz','Digital',1,14,'D4_4.1.1_A1','https://drive.google.com/open?id=1ACUKg4XDc1VDqsFRWZgTGFkoEarz0Y55&usp=drive_copy','2025-04-15 06:24:58','2025-04-16 05:52:58','2025-04-15 04:00:00',1,'2025-04-15 06:24:58'),(39,'Formulario de Control de Asistencia a las Prácticas Hospitalarias','Formulario de Control de Asistencia a las Prácticas Hospitalarias','Digital',1,16,'D4_4.3.2_A3','https://drive.google.com/open?id=1VTYyQfWS5kHLgrFKeZUaMY1ymJ1kuNYL&usp=drive_copy','2025-04-15 17:35:50','2025-04-16 05:52:58','2025-04-15 04:00:00',1,'2025-04-15 17:35:50'),(40,'Lista de Teléfonos de Emergencia 1.0.xlsx','Lista de Teléfonos de Emergencia 1.0.xlsx','Excel',1,37,'D4_4.5.1_6','https://docs.google.com/spreadsheets/d/16YSgJEd-Ncfz6eK6_P-VwmzvE9S9nCh-/edit?usp=drive_link&ouid=104074304421145384942&rtpof=true&sd=true','2025-04-15 17:37:41','2025-04-15 19:21:02','2025-04-15 04:00:00',1,'2025-04-15 17:37:41'),(41,'Formulario de Seguimiento a Prácticas Hospitalaria','Formulario de Seguimiento a Prácticas Hospitalarias','Digital',1,16,'D4_4.3.2_A2','https://drive.google.com/open?id=1Zub8hMwti2jO6H8WDMeFrag9jTO75NE_&usp=drive_copy','2025-04-15 18:07:42','2025-04-16 05:52:58','2025-04-15 18:07:42',1,'2025-04-15 18:07:42'),(42,'Análisis de concordancia del perfil de egreso de la carrera con el perfil de egreso definido por ArcuSur','Análisis de concordancia del perfil de egreso de la carrera con el perfil de egreso definido por ArcuSur','Digital',1,7,'D2_2.1.1_A1','https://drive.google.com/open?id=1BQYJlium4GtJK4dBoVzH4i6rcdYLX2OW4rDT49VXJZY&usp=drive_copy','2025-04-16 05:23:55','2025-04-16 05:52:58','2025-04-16 05:23:55',1,'2025-04-16 05:23:55');
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Decano'),(2,'Docente'),(3,'Pares'),(4,'Administrativos'),(5,'Directores');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_area`
--

DROP TABLE IF EXISTS `tipo_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_area` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_area`
--

LOCK TABLES `tipo_area` WRITE;
/*!40000 ALTER TABLE `tipo_area` DISABLE KEYS */;
INSERT INTO `tipo_area` VALUES (1,'Medicina',1),(2,'Odontologia',1);
/*!40000 ALTER TABLE `tipo_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) NOT NULL,
  `telefono` int DEFAULT NULL,
  `apellidos` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Juan Pérez',123456789,'Pérez','juan.perez@correo.com','password123',1,1,'2025-04-07 00:09:37'),(2,'Ana Gómez',987654321,'Gómez','ana.gomez@correo.com','password123',2,1,'2025-04-07 00:09:37'),(3,'Carlos Martínez',112233445,'Martínez','carlos.martinez@correo.com','password123',3,1,'2025-04-07 00:09:37'),(4,'Lucía Rodríguez',556677889,'Rodríguez','lucia.rodriguez@correo.com','password123',4,1,'2025-04-07 00:09:37'),(5,'Pedro Sánchez',998877665,'Sánchez','pedro.sanchez@correo.com','password123',5,1,'2025-04-07 00:09:37'),(6,'Manuel',NULL,'Paye ','manuelpaye.13@gmail.com','$2y$10$jtBCZUDew2319/mrQDVW6O8gg.0iXq.IaZxR2g7ACOyHKIEkCxEW.',2,1,'2025-04-12 04:32:51'),(7,'Alejandra Nicole',NULL,'Apaza Cori de Paye','Alejandra@gmail.com','$2y$10$gK18LgcvGnnUxC415h6E..hz6bZ4H6UwJmYX5UUT3MuA2mPB72iKe',3,1,'2025-04-12 05:12:24');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-16  1:58:50

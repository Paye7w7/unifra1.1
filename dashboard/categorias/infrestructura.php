<?php
include '../../includes/header.php';
include_once '../../includes/conexion.php';

$conexion = obtenerConexion();

// Parámetros desde GET
$categoriaSeleccionada = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$codigoSeleccionado = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$busqueda = isset($_GET['buscador']) ? trim($_GET['buscador']) : null;

// Consulta SQL base
$sql = "SELECT c.id, c.nombre AS categoria, 'Infraestructura' AS dimension
        FROM categorias c
        WHERE c.activo = 1 AND c.dimensiones_id = 4";

// Array para condiciones adicionales
$conditions = [];

if ($categoriaSeleccionada) {
    $conditions[] = "c.id = " . (int)$categoriaSeleccionada;
}

if ($codigoSeleccionado) {
    $codigoEscapado = $conexion->real_escape_string($codigoSeleccionado);
    $conditions[] = "EXISTS (
        SELECT 1 FROM documentos d 
        WHERE d.categoria_id = c.id 
        AND d.codigo_documento = '$codigoEscapado'
    )";
}

if ($busqueda) {
    $busquedaEscapada = $conexion->real_escape_string($busqueda);
    $conditions[] = "(c.nombre LIKE '%$busquedaEscapada%' 
                     OR EXISTS (
                         SELECT 1 FROM documentos d 
                         WHERE d.categoria_id = c.id 
                         AND d.activo = 1
                         AND (d.titulo LIKE '%$busquedaEscapada%' 
                              OR d.descripcion LIKE '%$busquedaEscapada%' 
                              OR d.codigo_documento LIKE '%$busquedaEscapada%')
                     ))";
}

// Agregar condiciones si existen
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

// Ejecutar consulta principal
$resultado = $conexion->query($sql);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

// Obtener todas las categorías para el select
$sql_categorias = "SELECT c.id, c.nombre AS categoria 
                   FROM categorias c 
                   WHERE c.activo = 1 AND c.dimensiones_id = 4";
$resultado_categorias = $conexion->query($sql_categorias);

// Consulta para llenar el select de códigos (filtrado por dimensión 4)
$sql_codigos = "SELECT DISTINCT d.codigo_documento 
                FROM documentos d
                JOIN categorias c ON d.categoria_id = c.id
                WHERE d.codigo_documento IS NOT NULL 
                AND d.codigo_documento != '' 
                AND d.activo = 1
                AND c.dimensiones_id = 4
                ORDER BY d.codigo_documento";
$resultado_codigos = $conexion->query($sql_codigos);

// Estilos e íconos
$colorDimension = 'card-green'; // Infraestructura
$iconos = ['fa-building', 'fa-wifi', 'fa-laptop', 'fa-flask'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infraestructura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/categorias.css">
    <style>
        .breadcrumbs-container {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 100px;
        }

        .breadcrumbs {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item {
            font-size: 20px;
        }

        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb-item a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .breadcrumb-separator {
            color: #6c757d;
            padding: 0 8px;
        }

        .sugerencias-codigos {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            width: calc(100% - 22px);
            display: none;
        }

        .sugerencias-codigos div {
            padding: 8px 12px;
            cursor: pointer;
        }

        .sugerencias-codigos div:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <!-- Breadcrumbs -->
        <div class="breadcrumbs-container">
            <nav aria-label="Ruta de navegación">
                <ol class="breadcrumbs">
                    <li class="breadcrumb-item"><a href="/dashboard/dimensiones/dimensiones.php">Dimensiones</a></li>
                    <li class="breadcrumb-separator">/</li>
                    <li class="breadcrumb-item active" aria-current="page">Infraestructura</li>
                </ol>
            </nav>
        </div>

        <div class="TITULOYHERRAMIENTAS">
            <div class="titulo-con-linea">
                <h1>INFRAESTRUCTURA</h1>
            </div>
            <div class="barra-de-herramientas">
                <!-- Botón para borrar filtros -->
                <button type="button" onclick="borrarFiltros()" class="btn-borrar-filtros">
                    <i class="fas fa-times-circle"></i> Borrar filtros
                </button>

                <!-- Filtro de código -->
                <div class="select-container">
                    <select id="codigo" name="codigo">
                        <option value="" <?= !$codigoSeleccionado ? 'selected' : '' ?> disabled>Selecciona un código</option>
                        <?php 
                        if ($resultado_codigos->num_rows > 0):
                            while ($row = $resultado_codigos->fetch_assoc()):
                                $codigo = $row['codigo_documento'];
                        ?>
                            <option value="<?= htmlspecialchars($codigo) ?>" <?= $codigoSeleccionado == $codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($codigo) ?>
                            </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <option disabled>No hay códigos existentes</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtro de categorías -->
                <div class="select-container">
                    <select id="categoria" name="categoria">
                        <option value="0" <?= !$categoriaSeleccionada ? 'selected' : '' ?>>Todas las categorías</option>
                        <?php while ($cat = $resultado_categorias->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoriaSeleccionada == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['categoria']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Buscador -->
                <div style="position: relative; flex-grow: 1;">
                    <input type="text" id="buscador" name="buscador" class="input" placeholder="Buscar por nombre, descripción o código..." value="<?= htmlspecialchars($busqueda) ?>">
                    <div id="sugerencias-codigos" class="sugerencias-codigos"></div>
                </div>
            </div>
        </div>

        <?php include '../../includes/cardscategorias.php'; ?>
    </div>
    <script src="../../assets/js/categorias.js"></script>
</body>
</html>
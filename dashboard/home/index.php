<?php
//include '../../includes/header2.php';
include_once '../../includes/conexion.php';

$conexion = obtenerConexion();

if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = trim($_GET['busqueda']);
    $busquedaEscapada = $conexion->real_escape_string($busqueda);

    // Buscar por código, título o descripción del documento
    $sql_doc = "SELECT id FROM documentos 
                WHERE codigo_documento LIKE '%$busquedaEscapada%' 
                   OR titulo LIKE '%$busquedaEscapada%' 
                   OR descripcion LIKE '%$busquedaEscapada%' 
                LIMIT 1";
    $res_doc = $conexion->query($sql_doc);
    if ($res_doc && $res_doc->num_rows > 0) {
        header("Location: ../documentos/documentos.php?busqueda=" . urlencode($busqueda));
        exit;
    }

    // Buscar en dimensiones
    $sql_dim = "SELECT id FROM dimensiones WHERE nombres LIKE '%$busquedaEscapada%' LIMIT 1";
    $res_dim = $conexion->query($sql_dim);
    if ($res_dim && $res_dim->num_rows > 0) {
        header("Location: ../dimensiones/dimensiones.php?busqueda=" . urlencode($busqueda));
        exit;
    }

    // Buscar en criterios
    $sql_criterio = "SELECT id FROM criterio WHERE nombre LIKE '%$busquedaEscapada%' LIMIT 1";
    $res_criterio = $conexion->query($sql_criterio);
    if ($res_criterio && $res_criterio->num_rows > 0) {
        header("Location: ../categorias/doclegal.php?criterio=" . urlencode($busqueda));
        exit;
    }

    // Buscar en categorías
    $sql_categoria = "SELECT nombre FROM categorias WHERE nombre LIKE '%$busquedaEscapada%' LIMIT 1";
    $res_categoria = $conexion->query($sql_categoria);
    if ($res_categoria && $res_categoria->num_rows > 0) {
        $cat = strtolower($res_categoria->fetch_assoc()['nombre']);
        switch (true) {
            case str_contains($cat, 'comunidad'):
                header("Location: ../categorias/comunidad.php?categoria=" . urlencode($busqueda));
                break;
            case str_contains($cat, 'docacad'):
            case str_contains($cat, 'acad'):
                header("Location: ../categorias/docacademica.php?categoria=" . urlencode($busqueda));
                break;
            case str_contains($cat, 'infra') || str_contains($cat, 'estructura'):
                header("Location: ../categorias/infrestructura.php?categoria=" . urlencode($busqueda));
                break;
            default:
                header("Location: ../categorias/doclegal.php?categoria=" . urlencode($busqueda));
                break;
        }
        exit;
    }

    // Redirección por defecto
    header("Location: ../categorias/doclegal.php?busqueda=" . urlencode($busqueda));
    exit;
}
?>

<!-- HTML como ya lo tenías abajo del PHP -->


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador Unifranz</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
</head>
<body>
    <div class="background-circles">
        <div class="circle circle1"></div>
        <div class="circle circle2"></div>
        <div class="circle circle3"></div>
        <div class="circle circle4"></div>
    </div>

    <div class="container">
        <div class="logo">UNIFRANZ</div>

        <form class="search-area" method="GET">
            <input type="text" class="search-bar" name="busqueda" placeholder="Buscar en Unifranz..." required>
            <button type="submit" class="search-iconn" style="background:none;border:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
            </button>
            <div class="mic-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/>
                    <path d="M10 8a2 2 0 1 1-4 0V3a2 2 0 1 1 4 0v5zM8 0a3 3 0 0 0-3 3v5a3 3 0 0 0 6 0V3a3 3 0 0 0-3-3z"/>
                </svg>
            </div>
        </form>

        <div class="slogan">
            <span>Repositorio de Documentos</span>
            <span>Encuentra lo que necesites al instante</span>
        </div>
    </div>

    <script src="../../assets/js/home.js"></script>
</body>
</html>
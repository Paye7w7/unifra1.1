<?php
include '../../includes/header.php';
include_once '../../includes/conexion.php';

$conexion = obtenerConexion();

$categoriaId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$codigoSeleccionado = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$busquedaLibre = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : null;

$nombreCategoria = "Búsqueda de código";
$nombreDimension = "Documentos";
$colorDimension = 'card-orange'; // Valor por defecto

if ($busquedaLibre) {
    $busquedaEscapada = "%" . $conexion->real_escape_string($busquedaLibre) . "%";
    $sqlDocumentos = "SELECT d.*, c.nombre AS categoria_nombre, di.nombres AS dimension_nombre
                      FROM documentos d
                      JOIN categorias c ON d.categoria_id = c.id
                      JOIN dimensiones di ON c.dimensiones_id = di.id
                      WHERE (d.titulo LIKE ? OR d.descripcion LIKE ? OR d.codigo_documento LIKE ?) 
                      AND d.activo = 1 ORDER BY d.titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
    $stmtDocumentos->bind_param("sss", $busquedaEscapada, $busquedaEscapada, $busquedaEscapada);
} elseif ($codigoSeleccionado) {
    $sqlDocumentos = "SELECT d.*, c.nombre AS categoria_nombre, di.nombres AS dimension_nombre
                      FROM documentos d
                      JOIN categorias c ON d.categoria_id = c.id
                      JOIN dimensiones di ON c.dimensiones_id = di.id
                      WHERE d.codigo_documento = ? AND d.activo = 1 ORDER BY d.titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
    $stmtDocumentos->bind_param("s", $codigoSeleccionado);
} elseif ($categoriaId) {
    $sqlDocumentos = "SELECT d.*, c.nombre AS categoria_nombre, di.nombres AS dimension_nombre
                      FROM documentos d
                      JOIN categorias c ON d.categoria_id = c.id
                      JOIN dimensiones di ON c.dimensiones_id = di.id
                      WHERE d.categoria_id = ? AND d.activo = 1 ORDER BY d.titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
    $stmtDocumentos->bind_param("i", $categoriaId);
} else {
    // MOSTRAR TODOS LOS DOCUMENTOS DE TODAS LAS ÁREAS
    $sqlDocumentos = "SELECT d.*, c.nombre AS categoria_nombre, di.nombres AS dimension_nombre
                      FROM documentos d
                      JOIN categorias c ON d.categoria_id = c.id
                      JOIN dimensiones di ON c.dimensiones_id = di.id
                      WHERE d.activo = 1 ORDER BY d.titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
}

if (isset($stmtDocumentos)) {
    $stmtDocumentos->execute();
    $resultadoDocumentos = $stmtDocumentos->get_result();
}

if ($categoriaId) {
    $sqlCategoria = "SELECT c.nombre, d.nombres as nombre_dimension 
                     FROM categorias c 
                     JOIN dimensiones d ON c.dimensiones_id = d.id 
                     WHERE c.id = ?";
    $stmtCategoria = $conexion->prepare($sqlCategoria);
    $stmtCategoria->bind_param("i", $categoriaId);
    $stmtCategoria->execute();
    $resultadoCategoria = $stmtCategoria->get_result();

    if ($resultadoCategoria->num_rows > 0) {
        $categoria = $resultadoCategoria->fetch_assoc();
        $nombreCategoria = htmlspecialchars($categoria['nombre']);
        $nombreDimension = htmlspecialchars($categoria['nombre_dimension']);

        switch ($nombreDimension) {
            case 'Documentación Académica':
                $colorDimension = 'card-pink';
                break;
            case 'Comunidad Estudiantil':
                $colorDimension = 'card-blue';
                break;
            case 'Infraestructura':
                $colorDimension = 'card-green';
                break;
            default:
                $colorDimension = 'card-orange';
        }
    }

    $sqlCodigos = "SELECT DISTINCT codigo_documento FROM documentos WHERE categoria_id = ? AND activo = 1 ORDER BY codigo_documento";
    $stmtCodigos = $conexion->prepare($sqlCodigos);
    $stmtCodigos->bind_param("i", $categoriaId);
    $stmtCodigos->execute();
    $resultadoCodigos = $stmtCodigos->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos - <?= $nombreDimension ?></title>
    <link rel="stylesheet" href="../../assets/css/documentos.css">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <!-- Estilos para breadcrumbs -->
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
    </style>
</head>

<body>
    

    <div class="container">
        <!-- Breadcrumbs dinámicos -->
    <div class="breadcrumbs-container">
        <nav aria-label="Ruta de navegación">
            <ol class="breadcrumbs">
                <li class="breadcrumb-item">
                    <a href="/dashboard/dimensiones/dimensiones.php">Dimensiones</a>
                </li>
                <li class="breadcrumb-separator">/</li>
                <?php if ($categoriaId && isset($nombreDimension)): ?>
                    <li class="breadcrumb-item">
                        <?= $nombreDimension ?> <!-- Eliminado el enlace <a> -->
                    </li>
                    <li class="breadcrumb-separator">/</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= $nombreCategoria ?>
                    </li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        Documentos
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
        <div class="TITULOYHERRAMIENTAS">
            <div class="titulo-con-linea">
                <h1><?= $nombreDimension ?></h1>
            </div>
            <div class="barra-de-herramientas">
                <button type="button" onclick="borrarFiltros()" class="btn-borrar-filtros">
                    <i class="fas fa-times-circle"></i> Borrar filtros
                </button>
                <?php if ($categoriaId): ?>
                    <div class="select-container">
                        <select id="codigo" name="codigo" onchange="filtrarDocumentos()">
                            <option value="">Todos los códigos</option>
                            <?php while ($codigo = $resultadoCodigos->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($codigo['codigo_documento']) ?>" <?= $codigoSeleccionado == $codigo['codigo_documento'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($codigo['codigo_documento']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <input type="text" id="buscador" name="buscador" class="input" placeholder="Buscar..." onkeyup="filtrarDocumentos()" value="<?= htmlspecialchars($busquedaLibre) ?>">
            </div>
        </div>

        <h2>Categoría: <?= $nombreCategoria ?></h2>

        <?php if ($resultadoDocumentos->num_rows > 0): ?>
            <div class="cards-container">
                <?php while ($doc = $resultadoDocumentos->fetch_assoc()): ?>
                    <a href="<?= htmlspecialchars($doc['link_documento']) ?>" target="_blank" class="card <?= $colorDimension ?>">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($doc['titulo']) ?></h3>
                            <p><?= htmlspecialchars($doc['descripcion']) ?></p>
                            <p style="font-family: Archivo Black, sans-serif;"><?= htmlspecialchars($doc['dimension_nombre']) ?></p>
                            <p><strong>Categoría:</strong> <?= htmlspecialchars($doc['categoria_nombre']) ?></p>
                            <p><strong>Código:</strong> <span class="codigo"><?= htmlspecialchars($doc['codigo_documento']) ?></span></p>
                            <p><strong>Formato:</strong> <?= htmlspecialchars($doc['formato']) ?></p>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                No se encontraron documentos.
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filtrarDocumentos() {
            const codigo = document.getElementById('codigo')?.value || '';
            const texto = document.getElementById('buscador').value.toLowerCase();
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const titulo = card.querySelector('h3').textContent.toLowerCase();
                const descripcion = card.querySelector('p').textContent.toLowerCase();
                const cod = card.querySelector('.codigo')?.textContent || '';
                const coincideTexto = titulo.includes(texto) || descripcion.includes(texto) || cod.includes(texto);
                const coincideCodigo = !codigo || cod === codigo;
                card.style.display = coincideTexto && coincideCodigo ? 'block' : 'none';
            });
        }

        function borrarFiltros() {
            document.getElementById('buscador').value = '';
            if (document.getElementById('codigo')) {
                document.getElementById('codigo').selectedIndex = 0;
                window.location.href = 'documentos.php?id=<?= $categoriaId ?>';
            } else {
                filtrarDocumentos();
            }
        }
    </script>
</body>

</html>

<?php
// Cerrar conexiones
if (isset($stmtDocumentos)) $stmtDocumentos->close();
if (isset($stmtCategoria)) $stmtCategoria->close();
if (isset($stmtCodigos)) $stmtCodigos->close();
$conexion->close();
?>
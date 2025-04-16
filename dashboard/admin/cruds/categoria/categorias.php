<?php
require '../../../../includes/conexion.php';
$conn = obtenerConexion();

// Función para activar/desactivar todas las categorías de un tipo de área
function toggleTipoArea($conn, $tipoAreaId, $accion)
{
    $activo = ($accion === 'activar') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE categorias SET activo = ? WHERE tipo_area_id = ?");
    $stmt->bind_param("ii", $activo, $tipoAreaId);
    return $stmt->execute();
}

// Manejar activación/desactivación masiva
if (isset($_GET['accion_masiva'])) {
    $accion = $_GET['accion_masiva'];
    $tipoArea = $_GET['tipo_area'];

    if (in_array($accion, ['activar', 'desactivar']) && in_array($tipoArea, ['medicina', 'odontologia'])) {
        $tipoAreaId = ($tipoArea === 'medicina') ? 1 : 2;

        if (toggleTipoArea($conn, $tipoAreaId, $accion)) {
            header("Location: categorias.php?mass_action_success=true&tipo_area=" . $tipoArea);
            exit;
        }
    }
}
// CREAR o ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'];
    $dimensiones_id = $_POST['dimensiones_id'];
    $tipo_area_id = $_POST['tipo_area_id'];
    $criterio_id = $_POST['criterio_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE categorias SET nombre=?, dimensiones_id=?, tipo_area_id=?, criterio_id=? WHERE id=?");
        $stmt->bind_param("siiii", $nombre, $dimensiones_id, $tipo_area_id, $criterio_id, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, dimensiones_id, tipo_area_id, criterio_id, activo, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("siii", $nombre, $dimensiones_id, $tipo_area_id, $criterio_id);
    }

    $stmt->execute();
    header("Location: categorias.php?success=true");
    exit;
}

// ACTIVAR / DESACTIVAR
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $accion = $_GET['accion'];
    $id = intval($_GET['id']);
    $activo = ($accion === 'activar') ? 1 : 0;

    $stmt = $conn->prepare("UPDATE categorias SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $activo, $id);
    $stmt->execute();

    header("Location: categorias.php?action_success=true");
    exit;
}

// CARGAR CATEGORÍA PARA EDITAR
$editando = false;
$categoria = ['id' => '', 'nombre' => '', 'dimensiones_id' => '', 'tipo_area_id' => '', 'criterio_id' => ''];
if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $res = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
    $res->bind_param("i", $id);
    $res->execute();
    $resultado = $res->get_result();
    if ($resultado->num_rows > 0) {
        $categoria = $resultado->fetch_assoc();
    }
}

// OBTENER DATOS PARA SELECTS
$dimensiones = $conn->query("SELECT id, nombres FROM dimensiones WHERE activo = 1");
$tipo_areas = $conn->query("SELECT id, nombre FROM tipo_area WHERE activo = 1");
$criterios = $conn->query("SELECT id, nombre FROM criterio WHERE activo = 1");

// LISTADO DE CATEGORÍAS
$sql = "SELECT c.id, c.nombre, c.activo, d.nombres AS dimension, t.nombre AS tipo_area, cr.nombre AS criterio
        FROM categorias c
        JOIN dimensiones d ON c.dimensiones_id = d.id
        JOIN tipo_area t ON c.tipo_area_id = t.id
        JOIN criterio cr ON c.criterio_id = cr.id
        ORDER BY c.id DESC";
$categorias = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías | UNIFRANZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../../assets/css/criterio.css">
</head>

<body data-editando="<?= $editando ? 'true' : 'false' ?>" data-edit-id="<?= $editando ? $categoria['id'] : '0' ?>">
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="UNIFRANZ" class="logo">

            </div>
            <nav class="nav-menu">
                <a href="../../cruds/inicio/Inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="categorias.php" class="active"><i class="fas fa-layer-group"></i> Categorías</a>
                <a href="../dimensiones/dimension.php"><i class="fas fa-cube"></i> Dimensiones</a>
                <a href="../tipoarea/tipo_area.php"><i class="fas fa-map-signs"></i> Tipos de Área</a>
                <a href="../criterio/criterio.php"><i class="fas fa-check-square"></i> Criterios</a>
                <a href="../../documento/documento.php"><i class="fas fa-file-alt"></i> Documentos</a>
                <div class="logout-container">
                    <a href="#" class="logout-btn" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                    <div class="logout-confirm" id="logoutConfirm">
                        <p>¿Seguro que deseas salir?</p>
                        <div class="logout-actions">
                            <a href="../../../../logout.php" class="logout-yes">Sí</a>
                            <button class="logout-no" id="logoutNo">No</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <h1 class="site-title">Categoria</h1>
        <!-- Nuevos botones de acciones masivas -->
        <section class="card mass-actions">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i> Acciones Masivas
            </h2>
            <div class="mass-buttons">
                <div class="mass-button-group">
                    <h3>Medicina</h3>
                    <a href="categorias.php?accion_masiva=activar&tipo_area=medicina" class="btn-activar" onclick="return confirm('¿Activar TODAS las categorías de Medicina?')">
                        <i class="fas fa-toggle-on"></i> Activar Medicina
                    </a>
                    <a href="categorias.php?accion_masiva=desactivar&tipo_area=medicina" class="btn-desactivar" onclick="return confirm('¿Desactivar TODAS las categorías de Medicina?')">
                        <i class="fas fa-toggle-off"></i> Desactivar Medicina
                    </a>
                </div>

                <div class="mass-button-group">
                    <h3>Odontología</h3>
                    <a href="categorias.php?accion_masiva=activar&tipo_area=odontologia" class="btn-activar" onclick="return confirm('¿Activar TODAS las categorías de Odontología?')">
                        <i class="fas fa-toggle-on"></i> Activar Odontología
                    </a>
                    <a href="categorias.php?accion_masiva=desactivar&tipo_area=odontologia" class="btn-desactivar" onclick="return confirm('¿Desactivar TODAS las categorías de Odontología?')">
                        <i class="fas fa-toggle-off"></i> Desactivar Odontología
                    </a>
                </div>
            </div>
        </section>
        <!-- Formulario de categorías -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-<?= $editando ? 'edit' : 'plus-circle' ?>"></i>
                <?= $editando ? 'Editar Categoría' : 'Crear Nueva Categoría' ?>
            </h2>
            <form method="post" action="categorias.php" id="categoriaForm">
                <input type="hidden" name="id" value="<?= $categoria['id'] ?>">

                <div class="form-group">
                    <label for="nombre">Nombre de la Categoría:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="dimensiones_id">Dimensión:</label>
                    <select id="dimensiones_id" name="dimensiones_id" required>
                        <option value="">Seleccione una dimensión</option>
                        <?php while ($row = $dimensiones->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= $row['id'] == $categoria['dimensiones_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nombres']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_area_id">Tipo de Área:</label>
                    <select id="tipo_area_id" name="tipo_area_id" required>
                        <option value="">Seleccione un tipo de área</option>
                        <?php while ($row = $tipo_areas->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= $row['id'] == $categoria['tipo_area_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="criterio_id">Criterio:</label>
                    <select id="criterio_id" name="criterio_id" required>
                        <option value="">Seleccione un criterio</option>
                        <?php while ($row = $criterios->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= $row['id'] == $categoria['criterio_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-<?= $editando ? 'save' : 'plus' ?>"></i>
                        <?= $editando ? 'Actualizar Categoría' : 'Guardar Categoría' ?>
                    </button>
                    <?php if ($editando): ?>
                        <a href="categorias.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Tabla de categorías -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Listado de Categorías
            </h2>

            <div class="table-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar categoría...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Dimensión</th>
                            <th>Tipo Área</th>
                            <th>Criterio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($categorias->num_rows === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay categorías registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = $categorias->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['dimension']) ?></td>
                                    <td><?= htmlspecialchars($row['tipo_area']) ?></td>
                                    <td><?= htmlspecialchars($row['criterio']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $row['activo'] ? 'status-active' : 'status-inactive' ?>">
                                            <?= $row['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="categorias.php?editar=<?= $row['id'] ?>" class="action-btn edit" data-tooltip="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($row['activo']): ?>
                                            <a href="categorias.php?accion=desactivar&id=<?= $row['id'] ?>" class="action-btn deactivate" data-tooltip="Desactivar" onclick="return confirm('¿Está seguro que desea desactivar esta categoría?')">
                                                <i class="fas fa-toggle-off"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="categorias.php?accion=activar&id=<?= $row['id'] ?>" class="action-btn activate" data-tooltip="Activar" onclick="return confirm('¿Está seguro que desea activar esta categoría?')">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <style>
        /* Estilos para los nuevos botones */
        .mass-actions {
            margin-bottom: 20px;
        }

        .mass-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .mass-button-group {
            flex: 1;
            min-width: 250px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mass-button-group h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .btn-activar,
        .btn-desactivar {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px 0;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-activar {
            background-color: #28a745;
            color: white;
        }

        .btn-activar:hover {
            background-color: #218838;
        }

        .btn-desactivar {
            background-color: #dc3545;
            color: white;
        }

        .btn-desactivar:hover {
            background-color: #c82333;
        }
    </style>
    <footer>
        <div class="footer-content">
            <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="UNIFRANZ" class="footer-logo">
            <div class="footer-links">
                <a href="#">Términos y Condiciones</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Contacto</a>
            </div>
            <div class="copyright">
                &copy; <?= date('Y') ?> UNIFRANZ. Todos los derechos reservados.
            </div>
        </div>
    </footer>


    <script src="../../../../assets/js/unifranz-admin.js"></script>
    <script src="../../../../assets/js/inicio.js"></script>
</body>

</html>
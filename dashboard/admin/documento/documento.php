<?php
require '../../../includes/conexion.php';
$conn = obtenerConexion();

// Determinar acción (listar, crear/editar, guardar, cambiar estado)
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$tipo_area = $_GET['tipo_area'] ?? null;
$toggle_all = $_GET['toggle_all'] ?? null; // Nueva acción para activar/desactivar todos

// Procesar activar/desactivar todos los documentos de un área
if ($action === 'toggle_all' && $tipo_area) {
    $new_status = $toggle_all === 'activate' ? 1 : 0;
    $query = "UPDATE documentos d
              JOIN categorias c ON d.categoria_id = c.id
              SET d.activo = ?, d.fecha_modificacion = NOW()
              WHERE c.tipo_area_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_status, $tipo_area);
    $stmt->execute();
    header("Location: documento.php?action=list&tipo_area=$tipo_area");
    exit();
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    // Recoger datos del formulario (sin fecha_resolucion)
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $formato = $_POST['formato'] ?? '';
    $usuario_id = $_POST['usuario_id'] ?? null;
    $categoria_id = $_POST['categoria_id'] ?? null;
    $codigo_documento = $_POST['codigo_documento'] ?? '';
    $link_documento = $_POST['link_documento'] ?? '';
    $activo = 1; // Siempre activo como acordamos

    if ($id) {
        // Actualizar documento existente
        $query = "UPDATE documentos SET 
                 titulo = ?, descripcion = ?, formato = ?, usuario_id = ?, categoria_id = ?, 
                 codigo_documento = ?, link_documento = ?, fecha_modificacion = NOW()
                 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssiissi",
            $titulo,
            $descripcion,
            $formato,
            $usuario_id,
            $categoria_id,
            $codigo_documento,
            $link_documento,
            $id
        );
    } else {
        // Insertar nuevo documento
        $query = "INSERT INTO documentos 
                 (titulo, descripcion, formato, usuario_id, categoria_id, codigo_documento, 
                  link_documento, activo, fecha_creacion, fecha_modificacion, fecha_resolucion, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssiiss",
            $titulo,
            $descripcion,
            $formato,
            $usuario_id,
            $categoria_id,
            $codigo_documento,
            $link_documento
        );
    }

    if ($stmt->execute()) {
        header("Location: documento.php?action=list");
        exit();
    } else {
        $error = "Error al guardar el documento: " . $stmt->error;
    }
}

// Procesar cambio de estado
if ($action === 'toggle_status' && $id) {
    $new_status = $_GET['status'] === 'activate' ? 1 : 0;
    $query = "UPDATE documentos SET activo = ?, fecha_modificacion = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_status, $id);
    $stmt->execute();
    header("Location: documento.php?action=list");
    exit();
}

// Obtener datos para formulario de edición/creación
if (($action === 'edit' || $action === 'create') && !isset($_POST['id'])) {
    $documento = null;
    $tipo_area_documento = $tipo_area;

    if ($id) {
        $query = "SELECT d.*, c.tipo_area_id FROM documentos d
                 JOIN categorias c ON d.categoria_id = c.id
                 WHERE d.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $documento = $stmt->get_result()->fetch_assoc();
        $tipo_area_documento = $documento['tipo_area_id'];
    }

    // Obtener categorías según tipo de área
    $queryCategorias = "SELECT id, nombre FROM categorias WHERE activo = 1 AND tipo_area_id = ?";
    $stmtCategorias = $conn->prepare($queryCategorias);
    $stmtCategorias->bind_param("i", $tipo_area_documento);
    $stmtCategorias->execute();
    $categorias = $stmtCategorias->get_result();

    // Obtener usuarios activos
    $queryUsuarios = "SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM usuario WHERE activo = 1";
    $usuarios = $conn->query($queryUsuarios);
}

// Obtener listado de documentos
if ($action === 'list') {
    $query = "SELECT d.*, u.nombres, u.apellidos, c.nombre AS categoria, 
                     t.nombre AS tipo_area, t.id AS tipo_area_id
              FROM documentos d
              JOIN usuario u ON d.usuario_id = u.id
              JOIN categorias c ON d.categoria_id = c.id
              JOIN tipo_area t ON c.tipo_area_id = t.id";

    if ($tipo_area) {
        $query .= " WHERE t.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tipo_area);
        $stmt->execute();
        $documentos = $stmt->get_result();

        // Verificar si hay documentos activos/inactivos en esta área
        $queryStatus = "SELECT COUNT(*) AS total, 
                        SUM(d.activo = 1) AS activos 
                        FROM documentos d
                        JOIN categorias c ON d.categoria_id = c.id
                        WHERE c.tipo_area_id = ?";
        $stmtStatus = $conn->prepare($queryStatus);
        $stmtStatus->bind_param("i", $tipo_area);
        $stmtStatus->execute();
        $statusResult = $stmtStatus->get_result()->fetch_assoc();
        $todosActivos = ($statusResult['activos'] == $statusResult['total']);
    } else {
        $documentos = $conn->query($query);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos | UNIFRANZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../../assets/css/criterio.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="UNIFRANZ" class="logo">
            </div>
            <nav class="nav-menu">
                <a href="../cruds/inicio/Inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../cruds/categoria/categorias.php"><i class="fas fa-layer-group"></i> Categorías</a>
                <a href="../cruds/dimensiones/dimension.php"><i class="fas fa-cube"></i> Dimensiones</a>
                <a href="../cruds/tipoarea/tipo_area.php"><i class="fas fa-map-signs"></i> Tipos de Área</a>
                <a href="../cruds/criterio/criterio.php"><i class="fas fa-check-square"></i> Criterios</a>
                <a href="documento.php?action=list" class="active"><i class="fas fa-file-alt"></i> Documentos</a>
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
        <?php if ($action === 'list'): ?>
            <h1 class="site-title">Documentos</h1>

            <!-- Filtros y acciones -->
            <section class="card">
                <h2 class="section-title">
                    <i class="fas fa-filter"></i> Filtros y Acciones

                    <a href="createdoc.php" class="btn btn-primary" style="margin-left: 10px;">
                        <i class="fas fa-plus-circle"></i> Crear documentos
                    </a>
                </h2>

                <div class="filter-actions">
                    <form method="GET" action="documento.php" class="filter-form">
                        <input type="hidden" name="action" value="list">
                        <div class="form-group">
                            <label for="tipo_area">Tipo de Área:</label>
                            <select name="tipo_area" id="tipo_area" onchange="this.form.submit()">
                                <option value="">Todos los tipos</option>
                                <option value="1" <?= $tipo_area == 1 ? 'selected' : '' ?>>Medicina</option>
                                <option value="2" <?= $tipo_area == 2 ? 'selected' : '' ?>>Odontología</option>
                            </select>
                        </div>
                    </form>

                    <div class="action-buttons">
                        <a href="documento.php?action=create&tipo_area=1" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Medicina
                        </a>
                        <a href="documento.php?action=create&tipo_area=2" class="btn btn-secondary">
                            <i class="fas fa-plus-circle"></i> Odontología
                        </a>

                        <!-- Nuevo botón para activar/desactivar todos (solo visible cuando hay filtro) -->
                        <?php if ($tipo_area): ?>
                            <?php if ($todosActivos): ?>
                                <a href="documento.php?action=toggle_all&tipo_area=<?= $tipo_area ?>&toggle_all=deactivate"
                                    class="action-btn deactivate"
                                    onclick="return confirm('¿Seguro que deseas DESACTIVAR TODOS los documentos de esta área?')">
                                    <i class="fas fa-toggle-off"></i> Desactivar Todos
                                </a>
                            <?php else: ?>
                                <a href="documento.php?action=toggle_all&tipo_area=<?= $tipo_area ?>&toggle_all=activate"
                                    class="action-btn activate"
                                    onclick="return confirm('¿Seguro que deseas ACTIVAR TODOS los documentos de esta área?')">
                                    <i class="fas fa-toggle-on"></i> Activar Todos
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Tabla de documentos -->
            <section class="card">
                <h2 class="section-title">
                    <i class="fas fa-list"></i> Listado de Documentos
                </h2>

                <div class="table-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Buscar documento...">
                        <i></i>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Tipo de Área</th>
                                <th>Formato</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($documentos->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay documentos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($doc = $documentos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($doc['codigo_documento']) ?></td>
                                        <td><?= htmlspecialchars($doc['titulo']) ?></td>
                                        <td><?= htmlspecialchars($doc['categoria']) ?></td>
                                        <td><?= htmlspecialchars($doc['tipo_area']) ?></td>
                                        <td><?= htmlspecialchars($doc['formato']) ?></td>
                                        <td>
                                            <span class="status-badge <?= $doc['activo'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $doc['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons2">
                                            <a href="documento.php?action=edit&id=<?= $doc['id'] ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <?php if ($doc['activo']): ?>
                                                <a href="documento.php?action=toggle_status&id=<?= $doc['id'] ?>&status=deactivate"
                                                    class="action-btn deactivate"
                                                    onclick="return confirm('¿Seguro que deseas desactivar este documento?')">
                                                    <i class="fas fa-toggle-off"></i> Desactivar
                                                </a>
                                            <?php else: ?>
                                                <a href="documento.php?action=toggle_status&id=<?= $doc['id'] ?>&status=activate"
                                                    class="action-btn activate"
                                                    onclick="return confirm('¿Seguro que deseas activar este documento?')">
                                                    <i class="fas fa-toggle-on"></i> Activar
                                                </a>
                                            <?php endif; ?>
                                            <button class="view-btn" onclick="viewDocument(<?= htmlspecialchars(json_encode($doc), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($action === 'edit' || $action === 'create'): ?>
            <h1 class="site-title">Documentos</h1>

            <section class="card">
                <h2 class="section-title">
                    <i class="fas fa-<?= $id ? 'edit' : 'plus-circle' ?>"></i>
                    <?= $id ? 'Editar Documento' : 'Nuevo Documento' ?>
                </h2>

                <form method="post" action="documento.php?action=save">
                    <input type="hidden" name="id" value="<?= $documento['id'] ?? '' ?>">
                    <input type="hidden" name="tipo_area" value="<?= $tipo_area_documento ?>">
                    <input type="hidden" name="activo" value="1"> <!-- Documento siempre activo -->

                    <div class="form-group">
                        <label for="titulo">Título:</label>
                        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($documento['titulo'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" id="descripcion" name="descripcion" value="<?= htmlspecialchars($documento['descripcion'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="formato">Formato:</label>
                        <select id="formato" name="formato" class="form-control" required>
                            <option value="Físico" <?= (isset($documento['formato']) && $documento['formato'] == 'Físico') ? 'selected' : '' ?>>Físico</option>
                            <option value="Digital" <?= (isset($documento['formato']) && $documento['formato'] == 'Digital') ? 'selected' : '' ?>>Digital</option>
                            <option value="Mixto" <?= (isset($documento['formato']) && $documento['formato'] == 'Mixto') ? 'selected' : '' ?>>Mixto</option>
                            <option value="PDF" <?= (isset($documento['formato']) && $documento['formato'] == 'PDF') ? 'selected' : '' ?>>PDF</option>
                            <option value="Word" <?= (isset($documento['formato']) && $documento['formato'] == 'Word') ? 'selected' : '' ?>>Word</option>
                            <option value="Excel" <?= (isset($documento['formato']) && $documento['formato'] == 'Excel') ? 'selected' : '' ?>>Excel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usuario_id">Usuario:</label>
                        <select id="usuario_id" name="usuario_id" required>
                            <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                <option value="<?= $usuario['id'] ?>" <?= isset($documento['usuario_id']) && $documento['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($usuario['nombre_completo']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">Categoría:</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <?php while ($categoria = $categorias->fetch_assoc()): ?>
                                <option value="<?= $categoria['id'] ?>" <?= isset($documento['categoria_id']) && $documento['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="codigo_documento">Código Documento:</label>
                        <input type="text" id="codigo_documento" name="codigo_documento" value="<?= htmlspecialchars($documento['codigo_documento'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="link_documento">Link Documento:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-link"></i>
                            <input type="url" id="link_documento" name="link_documento"
                                value="<?= htmlspecialchars($documento['link_documento'] ?? '') ?>"
                                placeholder="https://ejemplo.com/documento"
                                <?= !isset($documento['id']) ? 'required' : '' ?>>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?= $id ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="documento.php?action=list" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>

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

    <!-- Modal para ver detalles del documento -->
    <div id="documentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Detalles del Documento</h2>
            <div class="document-details" id="documentDetails">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        // Funciones para el modal de visualización
        const modal = document.getElementById('documentModal');
        const closeBtn = document.querySelector('.close-modal');

        function viewDocument(doc) {
            const detailsContainer = document.getElementById('documentDetails');

            // Formatear la fecha para mostrarla mejor
            const formatDate = (dateString) => {
                if (!dateString) return 'No especificada';
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            };

            // Estado con color
            const statusBadge = doc.activo ?
                '<span style="color: #28a745;">● Activo</span>' :
                '<span style="color: #dc3545;">● Inactivo</span>';

            // Construir el HTML con todos los detalles en formato de grid
            detailsContainer.innerHTML = `
                <dl>
                    <dt>Código:</dt>
                    <dd>${doc.codigo_documento || 'No especificado'}</dd>
                    
                    <dt>Título:</dt>
                    <dd>${doc.titulo}</dd>
                    
                    <dt>Descripción:</dt>
                    <dd>${doc.descripcion || 'No especificada'}</dd>
                    
                    <dt>Formato:</dt>
                    <dd>${doc.formato}</dd>
                    
                    <dt>Categoría:</dt>
                    <dd>${doc.categoria}</dd>
                    
                    <dt>Tipo de Área:</dt>
                    <dd>${doc.tipo_area}</dd>
                    
                    <dt>Responsable:</dt>
                    <dd>${doc.nombres} ${doc.apellidos}</dd>
                    
                    <dt>Enlace:</dt>
                    <dd>${
                        doc.link_documento 
                        ? `<a href="${doc.link_documento}" target="_blank" rel="noopener">${doc.link_documento}</a>`
                        : 'No especificado'
                    }</dd>
                    
                    <dt>Estado:</dt>
                    <dd>${statusBadge}</dd>
                    
                    <dt>Creado:</dt>
                    <dd>${formatDate(doc.fecha_creacion)}</dd>
                    
                    <dt>Modificado:</dt>
                    <dd>${formatDate(doc.fecha_modificacion)}</dd>
                    
                    <dt>Resolución:</dt>
                    <dd>${formatDate(doc.fecha_resolucion)}</dd>
                </dl>
                
                <style>
                    #documentDetails dl {
                        display: grid;
                        grid-template-columns: 150px 1fr;
                        gap: 10px;
                    }
                    
                    #documentDetails dt {
                        font-weight: 600;
                        color: #555;
                    }
                    
                    #documentDetails dd {
                        margin: 0;
                        padding: 5px 0;
                    }
                </style>
            `;

            modal.style.display = 'block';
        }

        // Cerrar modal al hacer clic en la X
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    <script src="../../../../assets/js/unifranz-admin.js"></script>
    <script src="../../../../assets/js/inicio.js"></script>
</body>

</html>
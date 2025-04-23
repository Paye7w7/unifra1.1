<?php
require_once '../../../includes/conexion.php';

// Start session for message handling
session_start();

// Por esta (ruta absoluta desde la raíz del proyecto):
$directorio_uploads = $_SERVER['DOCUMENT_ROOT'] . '/uploads/documentos/';
$extensiones_permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
$tamano_maximo = 5 * 1024 * 1024; // 5MB

// Crear directorio si no existe
if (!file_exists($directorio_uploads)) {
    mkdir($directorio_uploads, 0777, true);
}

// Obtener conexión
$conexion = obtenerConexion();

// Initialize message variables
$mensaje = '';
$tipo_mensaje = '';
$mostrar_mensaje = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preparar datos escapando valores
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $formato = $conexion->real_escape_string($_POST['formato']);
    $codigo_documento = $conexion->real_escape_string($_POST['codigo_documento']);
    $usuario_id = $conexion->real_escape_string($_POST['usuario_id']);
    $categoria_id = $conexion->real_escape_string($_POST['categoria_id']);
    $criterio_id = $conexion->real_escape_string($_POST['criterio_id']);

    // Inicializar ruta_archivo
    $ruta_archivo = 'NULL';

    // Procesar archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['archivo'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validar archivo
        if (!in_array($extension, $extensiones_permitidas)) {
            $_SESSION['mensaje'] = "Tipo de archivo no permitido. Solo se aceptan: " . implode(', ', $extensiones_permitidas);
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: createdoc.php");
            exit();
        } elseif ($file['size'] > $tamano_maximo) {
            $_SESSION['mensaje'] = "El archivo es demasiado grande. Tamaño máximo: " . ($tamano_maximo / 1024 / 1024) . "MB";
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: createdoc.php");
            exit();
        } else {
            // Generar nombre único para el archivo
            $nombre_archivo = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file['name']);
            $ruta_archivo_temp = $directorio_uploads . $nombre_archivo;

            if (move_uploaded_file($file['tmp_name'], $ruta_archivo_temp)) {
                $ruta_relativa = '/uploads/documentos/' . $nombre_archivo;
                $ruta_archivo = "'" . $conexion->real_escape_string($ruta_relativa) . "'";
            } else {
                $_SESSION['mensaje'] = "Error al subir el archivo";
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: createdoc.php");
                exit();
            }
        }
    } elseif (isset($_POST['archivo_actual']) && !empty($_POST['archivo_actual'])) {
        $ruta_archivo = "'" . $conexion->real_escape_string($_POST['archivo_actual']) . "'";
    }

    // En la sección donde procesas el formulario POST
    if (isset($_POST['crear'])) {
        // Crear nuevo documento (siempre activo al crear)
        $query = "INSERT INTO documentos (titulo, descripcion, formato, codigo_documento, ruta_archivo, usuario_id, categoria_id, criterio_id, activo, fecha_creacion, fecha_modificacion, fecha_resolucion, link_documento) 
                  VALUES ('$titulo', '$descripcion', '$formato', '$codigo_documento', $ruta_archivo, '$usuario_id', '$categoria_id', '$criterio_id', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL)";

        if ($conexion->query($query)) {
            $_SESSION['mensaje'] = "Documento creado correctamente";
            $_SESSION['tipo_mensaje'] = 'success';
            header("Location: createdoc.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al crear documento: " . $conexion->error;
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: createdoc.php");
            exit();
        }
    } elseif (isset($_POST['editar'])) {
        // Actualizar documento (mantener el estado actual)
        $id = $conexion->real_escape_string($_POST['id']);
        $query = "UPDATE documentos SET 
                  titulo='$titulo', 
                  descripcion='$descripcion', 
                  formato='$formato', 
                  codigo_documento='$codigo_documento', 
                  ruta_archivo=$ruta_archivo, 
                  usuario_id='$usuario_id', 
                  categoria_id='$categoria_id', 
                  criterio_id='$criterio_id',
                  fecha_modificacion=CURRENT_TIMESTAMP 
                  WHERE id=$id";

        if ($conexion->query($query)) {
            $_SESSION['mensaje'] = "Documento actualizado correctamente";
            $_SESSION['tipo_mensaje'] = 'success';
            header("Location: createdoc.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al actualizar documento: " . $conexion->error;
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: createdoc.php");
            exit();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['eliminar'])) {
        // Eliminar documento y su archivo
        $id = $conexion->real_escape_string($_GET['eliminar']);
        $query = "SELECT ruta_archivo FROM documentos WHERE id=$id";
        $result = $conexion->query($query);

        if ($result && $result->num_rows > 0) {
            $documento = $result->fetch_assoc();
            if ($documento['ruta_archivo'] && file_exists($documento['ruta_archivo'])) {
                unlink($documento['ruta_archivo']);
            }
        }

        $query = "DELETE FROM documentos WHERE id=$id";
        if ($conexion->query($query)) {
            $_SESSION['mensaje'] = "Documento eliminado correctamente";
            $_SESSION['tipo_mensaje'] = 'success';
            header("Location: createdoc.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al eliminar documento: " . $conexion->error;
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: createdoc.php");
            exit();
        }
    } elseif (isset($_GET['cambiar_estado'])) {
        // Cambiar estado (activar/desactivar)
        $id = $conexion->real_escape_string($_GET['cambiar_estado']);

        // Obtenemos el estado actual
        $query = "SELECT activo FROM documentos WHERE id=$id";
        $result = $conexion->query($query);

        if ($result && $result->num_rows > 0) {
            $documento = $result->fetch_assoc();
            $nuevo_estado = $documento['activo'] ? 0 : 1;

            $query = "UPDATE documentos SET activo=$nuevo_estado, fecha_modificacion=CURRENT_TIMESTAMP WHERE id=$id";
            if ($conexion->query($query)) {
                $_SESSION['mensaje'] = $nuevo_estado ? "Documento activado correctamente" : "Documento desactivado correctamente";
                $_SESSION['tipo_mensaje'] = 'success';
                header("Location: createdoc.php");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al cambiar estado del documento: " . $conexion->error;
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: createdoc.php");
                exit();
            }
        }
    }
}

// Check for session messages
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    $mostrar_mensaje = true;
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// Obtener documentos para la lista
$query = "SELECT d.*, 
          CONCAT(u.nombres, ' ', u.apellidos) as usuario_nombre,
          c.nombre as categoria_nombre,
          cr.nombre as criterio_nombre,
          DATE_FORMAT(d.fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion_formatted,
          DATE_FORMAT(d.fecha_modificacion, '%d/%m/%Y %H:%i') as fecha_modificacion_formatted,
          DATE_FORMAT(d.fecha_resolucion, '%d/%m/%Y %H:%i') as fecha_resolucion_formatted
          FROM documentos d
          LEFT JOIN usuario u ON d.usuario_id = u.id
          LEFT JOIN categorias c ON d.categoria_id = c.id
          LEFT JOIN criterio cr ON d.criterio_id = cr.id
          ORDER BY d.created_at DESC";
$result = $conexion->query($query);
$documentos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documentos[] = $row;
    }
}

// Obtener documento para edición si existe el parámetro
$documento_editar = null;
if (isset($_GET['editar'])) {
    $id = $conexion->real_escape_string($_GET['editar']);
    $query = "SELECT * FROM documentos WHERE id=$id";
    $result = $conexion->query($query);
    if ($result && $result->num_rows > 0) {
        $documento_editar = $result->fetch_assoc();
    }
}

// Obtener lista de usuarios, categorías y criterios para los select
$usuarios = [];
$categorias = [];
$criterios = [];

$query_usuarios = "SELECT id, CONCAT(nombres, ' ', apellidos) as nombre_completo FROM usuario ORDER BY nombres";
$query_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre";
$query_criterios = "SELECT id, nombre FROM criterio WHERE activo = 1 ORDER BY nombre";

$result_usuarios = $conexion->query($query_usuarios);
if ($result_usuarios) {
    while ($row = $result_usuarios->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

$result_categorias = $conexion->query($query_categorias);
if ($result_categorias) {
    while ($row = $result_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$result_criterios = $conexion->query($query_criterios);
if ($result_criterios) {
    while ($row = $result_criterios->fetch_assoc()) {
        $criterios[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Documentos | UNIFRANZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../assets/css/criterio.css">
    
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
                <a href="documento.php" class="active"><i class="fas fa-file-alt"></i> Documentos</a>
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
        <h1 class="site-title">Documentos</h1>
        
        <!-- Formulario de documentos -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-<?= $documento_editar ? 'edit' : 'plus-circle' ?>"></i>
                <?= $documento_editar ? 'Editar Documento' : 'Nuevo Documento' ?>
            </h2>
            <form method="POST" enctype="multipart/form-data" id="documentoForm">
                <?php if ($documento_editar): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($documento_editar['id']) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($documento_editar['titulo'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($documento_editar['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="formato">Formato:</label>
                    <select id="formato" name="formato" required>
                        <option value="">Seleccione un formato</option>
                        <option value="PDF" <?= isset($documento_editar['formato']) && $documento_editar['formato'] == 'PDF' ? 'selected' : '' ?>>PDF</option>
                        <option value="Word" <?= isset($documento_editar['formato']) && $documento_editar['formato'] == 'Word' ? 'selected' : '' ?>>Word</option>
                        <option value="Excel" <?= isset($documento_editar['formato']) && $documento_editar['formato'] == 'Excel' ? 'selected' : '' ?>>Excel</option>
                        <option value="PowerPoint" <?= isset($documento_editar['formato']) && $documento_editar['formato'] == 'PowerPoint' ? 'selected' : '' ?>>PowerPoint</option>
                        <option value="Texto" <?= isset($documento_editar['formato']) && $documento_editar['formato'] == 'Texto' ? 'selected' : '' ?>>Texto</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="codigo_documento">Código del Documento:</label>
                    <input type="text" id="codigo_documento" name="codigo_documento" required value="<?= htmlspecialchars($documento_editar['codigo_documento'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="usuario_id">Usuario:</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">Seleccione un usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>" <?= isset($documento_editar['usuario_id']) && $documento_editar['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usuario['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="categoria_id">Categoría:</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>" <?= isset($documento_editar['categoria_id']) && $documento_editar['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="criterio_id">Criterio:</label>
                    <select id="criterio_id" name="criterio_id" required>
                        <option value="">Seleccione un criterio</option>
                        <?php foreach ($criterios as $criterio): ?>
                            <option value="<?= $criterio['id'] ?>" <?= isset($documento_editar['criterio_id']) && $documento_editar['criterio_id'] == $criterio['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($criterio['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="archivo">Archivo:</label>
                    <input type="file" id="archivo" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                    <?php if ($documento_editar && $documento_editar['ruta_archivo']): ?>
                        <div class="document-preview">
                            <strong>Documento actual:</strong>
                            <div class="file-info">
                                <i class="fas fa-file"></i>
                                <a href="<?= htmlspecialchars($documento_editar['ruta_archivo']) ?>" target="_blank">Ver documento actual</a>
                            </div>
                            <input type="hidden" name="archivo_actual" value="<?= htmlspecialchars($documento_editar['ruta_archivo']) ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" name="<?= $documento_editar ? 'editar' : 'crear' ?>" class="btn-primary">
                        <i class="fas fa-<?= $documento_editar ? 'save' : 'plus' ?>"></i>
                        <?= $documento_editar ? 'Actualizar Documento' : 'Guardar Documento' ?>
                    </button>
                    <?php if ($documento_editar): ?>
                        <a href="createdoc.php" class="btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Tabla de documentos -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Listado de Documentos
            </h2>

            <div class="table-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar documentos...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Código</th>
                            <th>Formato</th>
                            <th>Usuario</th>
                            <th>Categoría</th>
                            <th>Criterio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documentos)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay documentos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documentos as $doc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($doc['id']) ?></td>
                                    <td><?= htmlspecialchars($doc['titulo']) ?></td>
                                    <td><?= htmlspecialchars($doc['codigo_documento']) ?></td>
                                    <td><?= htmlspecialchars($doc['formato']) ?></td>
                                    <td><?= htmlspecialchars($doc['usuario_nombre'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($doc['categoria_nombre'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($doc['criterio_nombre'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="status-badge <?= $doc['activo'] ? 'status-active' : 'status-inactive' ?>">
                                            <?= $doc['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="createdoc.php?editar=<?= $doc['id'] ?>" class="action-btn edit">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <?php if ($doc['activo']): ?>
                                            <a href="createdoc.php?cambiar_estado=<?= $doc['id'] ?>" class="action-btn deactivate">
                                                <i class="fas fa-toggle-off"></i> Desactivar
                                            </a>
                                        <?php else: ?>
                                            <a href="createdoc.php?cambiar_estado=<?= $doc['id'] ?>" class="action-btn activate">
                                                <i class="fas fa-toggle-on"></i> Activar
                                            </a>
                                        <?php endif; ?>
                                        <a href="createdoc.php?eliminar=<?= $doc['id'] ?>" class="action-btn delete" onclick="return confirm('¿Estás seguro de eliminar este documento?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
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

    <script src="../../../assets/js/unifranz-admin.js"></script>
    <script src="../../../assets/js/inicio.js"></script>
    <script>
        // Función para búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Función para ocultar mensajes después de 3 segundos
        setTimeout(function() {
            const mensaje = document.getElementById('mensaje-flotante');
            if (mensaje) {
                mensaje.style.opacity = '0';
                setTimeout(() => mensaje.remove(), 500);
            }
        }, 3000);
    </script>
</body>

</html>
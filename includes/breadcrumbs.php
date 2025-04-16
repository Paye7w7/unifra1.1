<?php
// breadcrumbs.php
function generarBreadcrumbs() {
    $breadcrumbs = [];
    
    // Página inicial (siempre presente)
    $breadcrumbs[] = [
        'nombre' => 'Inicio',
        'url' => '../../index.php'
    ];
    
    // Dependiendo de la página actual, añadimos niveles adicionales
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Mapeo de páginas a sus nombres y rutas
    $paginas = [
        'doclegal.php' => ['nombre' => 'Documentación Legal', 'url' => 'doclegal.php'],
        'doacademica.php' => ['nombre' => 'Documentación Académica', 'url' => 'doacademica.php'],
        'comunidad.php' => ['nombre' => 'Comunidad Estudiantil', 'url' => 'comunidad.php'],
        'infraestructura.php' => ['nombre' => 'Infraestructura', 'url' => 'infraestructura.php'],
        'documentos.php' => ['nombre' => 'Documentos', 'url' => 'documentos.php']
    ];
    
    // Añadir la página principal si corresponde
    if (array_key_exists($current_page, $paginas)) {
        $breadcrumbs[] = $paginas[$current_page];
    }
    
    // Para documentos.php, añadir categoría si está disponible
    if ($current_page === 'documentos.php' && isset($_GET['id'])) {
        include_once '../../includes/conexion.php';
        $conexion = obtenerConexion();
        
        $categoriaId = (int)$_GET['id'];
        $sql = "SELECT c.nombre, d.nombres as dimension 
                FROM categorias c 
                JOIN dimensiones d ON c.dimensiones_id = d.id 
                WHERE c.id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $categoriaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $breadcrumbs[] = [
                'nombre' => $row['dimension'],
                'url' => strtolower(str_replace(' ', '', $row['dimension'])).'.php'
            ];
            $breadcrumbs[] = [
                'nombre' => $row['nombre'],
                'url' => ''
            ];
        }
        $stmt->close();
        $conexion->close();
    }
    
    // Generar el HTML
    $html = '<div class="breadcrumbs-container">';
    $html .= '<nav aria-label="Ruta de navegación">';
    $html .= '<ol class="breadcrumbs">';
    
    foreach ($breadcrumbs as $index => $crumb) {
        $is_last = ($index === count($breadcrumbs) - 1);
        
        if (!$is_last && !empty($crumb['url'])) {
            $html .= '<li class="breadcrumb-item">';
            $html .= '<a href="'.$crumb['url'].'">'.$crumb['nombre'].'</a>';
            $html .= '</li>';
            $html .= '<li class="breadcrumb-separator">/</li>';
        } else {
            $html .= '<li class="breadcrumb-item active" aria-current="page">';
            $html .= $crumb['nombre'];
            $html .= '</li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    $html .= '</div>';
    
    return $html;
}
?>
<?php
session_start();
include_once __DIR__ . '/conexion.php';

$conexion = obtenerConexion();
$nombre_usuario = "Invitad@";

// Verificar si hay un usuario logueado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $conexion->prepare("SELECT nombres FROM usuario WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $fila = $resultado->fetch_assoc()) {
        $nombre_usuario = $fila['nombres'];
    }
    $stmt->close();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Iconos Font Awesome -->
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    
    <style>
        /* Estilos para el tooltip de cierre de sesión */
        .profile-tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
            font-family: "Inter", sans-serif;
        }
        
        .profile-tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
            transition-delay: 0.3s; /* Retraso antes de mostrarse */
            transition-duration: 0.5s; /* Transición más lenta */
        }
        
        .tooltiptext {
            visibility: hidden;
            width: 150px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 10px;
            position: absolute;
            z-index: 1000;
            top: 125%;
            right: 0;
            opacity: 0;
            transition: opacity 0.5s;
            transition-delay: 0.2s; /* Retraso antes de ocultarse */
        }
        
        .tooltiptext::after {
            content: "";
            position: absolute;
            top: -10px;
            right: 15px;
            border-width: 10px;
            border-style: solid;
            border-color: transparent transparent #333 transparent;
        }
        
        .tooltiptext a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 8px 0;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .tooltiptext a:hover {
            color: #4caf50;
        }
        
        .tooltiptext i {
            margin-right: 5px;
        }
        
        /* Clase para mantener activo el tooltip cuando se hace clic */
        .tooltip-active .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="../home/index.php">
                    <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="Logo Unifranz">
                </a>
            </div>
            <div class="hamburger" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="navbar">
                <ul class="nav-links">
                    <li class="dropdown">
                        <a href="../dimensiones/dimensiones.php">Dimensiones <i class="fas fa-chevron-down dropdown-icon"></i></a>
                        <ul class="submenu">
                            <?php
                            $query = "SELECT id, nombres FROM dimensiones WHERE activo = 1";
                            $resultado = $conexion->query($query);

                            if ($resultado && $resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    $nombre = htmlspecialchars($fila['nombres']);
                                    $id = (int)$fila['id'];

                                    $icono = '';
                                    $enlace = '../dimensiones/dimensiones.php';

                                    switch (strtolower($nombre)) {
                                        case 'documentación legal':
                                            $icono = 'fas fa-gavel';
                                            $enlace = '../categorias/doclegal.php?id=' . $id;
                                            break;
                                        case 'documentación académica':
                                            $icono = 'fas fa-graduation-cap';
                                            $enlace = '../categorias/docacademica.php?id=' . $id;
                                            break;
                                        case 'comunidad estudiantil':
                                            $icono = 'fas fa-users';
                                            $enlace = '../categorias/comunidad.php?id=' . $id;
                                            break;
                                        case 'infraestructura':
                                            $icono = 'fas fa-building';
                                            $enlace = '../categorias/infrestructura.php?id=' . $id;
                                            break;
                                        default:
                                            $icono = 'fas fa-folder';
                                            $enlace = '../dimensiones/dimensiones.php?id=' . $id;
                                            break;
                                    }

                                    echo "<li><a href='$enlace'><i class='$icono'></i> $nombre</a></li>";
                                }
                            } else {
                                echo "<li><a href='#'>Sin dimensiones</a></li>";
                            }
                            ?>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#">Páginas <i class="fas fa-chevron-down dropdown-icon"></i></a>
                        <ul class="submenu">
                            <li><a href="https://tools.unifranz.edu.bo/">Tools Unifranz</a></li>
                            <li><a href="#">Página 2</a></li>
                            <li><a href="#">Página 3</a></li>
                        </ul>
                    </li>
                </ul>

            
            </nav>
            <!--es para poner boton cerrar sesion-->
            <div class="nav-right">
                <div class="profile-tooltip" id="profileTooltip">
                    <div class="profile-container">
                        <img src="../../assets/img/fotoperfil.jpg" alt="Profile" class="profile-img">
                        <div class="status-indicator"></div>
                        <div class="user-info">
                            <span>Bienvenid@</span>
                            <span class="name"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                        </div>
                    </div>
                    <div class="tooltiptext">

                        <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i>Iniciar Sesion</a>
                    </div>
                </div>
            </div>
            <!--fin poner boton cerrar sesion-->
        </header>
    </div>
    <script src="../../assets/js/header.js"></script>
    
    <script>
        // Script para manejar el tooltip
        document.addEventListener('DOMContentLoaded', function() {
            const profileTooltip = document.getElementById('profileTooltip');
            let isTooltipActive = false;
            
            // Toggle para activar/desactivar el tooltip con clic
            profileTooltip.addEventListener('click', function(e) {
                if (isTooltipActive) {
                    profileTooltip.classList.remove('tooltip-active');
                    isTooltipActive = false;
                } else {
                    profileTooltip.classList.add('tooltip-active');
                    isTooltipActive = true;
                    e.stopPropagation(); // Evita que el clic se propague al documento
                }
            });
            
            // Cierra el tooltip si se hace clic en cualquier parte fuera de él
            document.addEventListener('click', function(e) {
                if (!profileTooltip.contains(e.target) && isTooltipActive) {
                    profileTooltip.classList.remove('tooltip-active');
                    isTooltipActive = false;
                }
            });
            
            // Previene que el tooltip se cierre cuando se hace clic dentro de él
            const tooltiptext = profileTooltip.querySelector('.tooltiptext');
            tooltiptext.addEventListener('click', function(e) {
                // Solo detener la propagación si no es un enlace
                if (!e.target.closest('a')) {
                    e.stopPropagation();
                }
            });
        });
    </script>
</body>

</html>
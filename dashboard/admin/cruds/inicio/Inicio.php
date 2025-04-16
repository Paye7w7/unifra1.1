<?php
require '../../../../includes/conexion.php';
$conn = obtenerConexion();


    $saludo = "Hola como estas!!"; // Tarde: 12:00 pm - 6:59 pm

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | UNIFRANZ</title>
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
                <a href="Inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a>
                <a href="../categoria/categorias.php"><i class="fas fa-layer-group"></i> Categorías</a>
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
        <div class="greeting"><?= $saludo ?>. ¿Que quieres hacer hoy?</div>

        <div class="dashboard-widgets">
            <!-- Widget de Reloj -->
            <div class="widget">
                <div class="widget-header">
                    <div class="widget-icon"><i class="fas fa-clock"></i></div>
                    <div class="widget-title">Reloj</div>
                </div>
                <div class="clock" id="clock">00:00:00</div>
                <div class="date" id="date">Lunes, 1 de enero de 2023</div>
            </div>

            <!-- Widget de Calendario -->
            <div class="widget">
                <div class="widget-header">
                    <div class="widget-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="widget-title">Calendario</div>
                </div>
                <div id="calendar-container">
                    <?php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

                    $hoy = getdate();
                    $mes = $hoy['mon'];
                    $año = $hoy['year'];

                    $primerDia = getdate(mktime(0, 0, 0, $mes, 1, $año));
                    $ultimoDia = getdate(mktime(0, 0, 0, $mes + 1, 0, $año));

                    $diasEnMes = $ultimoDia['mday'];
                    $diaInicioSemana = $primerDia['wday'];
                    ?>

                    <h3 class="calendar-title"><?= $meses[$mes - 1] . ' ' . $año ?></h3>

                    <table class="calendar">
                        <thead>
                            <tr>
                                <?php foreach ($diasSemana as $dia): ?>
                                    <th><?= $dia ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                // Espacios vacíos para los días antes del primer día del mes
                                for ($i = 0; $i < $diaInicioSemana; $i++) {
                                    echo '<td class="other-month">&nbsp;</td>';
                                }

                                $diaActual = 1;
                                $celdaActual = $diaInicioSemana;

                                while ($diaActual <= $diasEnMes) {
                                    if ($celdaActual == 7) {
                                        echo '</tr><tr>';
                                        $celdaActual = 0;
                                    }

                                    $clase = ($diaActual == $hoy['mday'] && $mes == $hoy['mon'] && $año == $hoy['year']) ? 'today' : '';
                                    echo '<td class="' . $clase . '">' . $diaActual . '</td>';

                                    $diaActual++;
                                    $celdaActual++;
                                }

                                // Espacios vacíos para completar la última fila
                                while ($celdaActual < 7) {
                                    echo '<td class="other-month">&nbsp;</td>';
                                    $celdaActual++;
                                }
                                ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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

    <script src="../../../../assets/js/inicio.js"></script>
</body>
</html>
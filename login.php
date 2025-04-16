<?php
require 'includes/auth_functions.php';
include 'includes/loader.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar entradas
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'] ?? '';

    // Validaciones básicas
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor ingresa un correo electrónico válido";
    } elseif (empty($contrasena)) {
        $error = "La contraseña no puede estar vacía";
    } elseif (strlen($contrasena) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        // Intento de inicio de sesión
        if (iniciarSesion($correo, $contrasena)) {
            redirigirSegunRol();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Unifranz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .error-message {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .password-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /*ojito*/

        .password-input-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }

        .toggle-password:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
            <div class="success-message" style="color: #00cc66; margin-bottom: 15px;">
                ¡Registro exitoso! Por favor inicia sesión.
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form" id="loginForm">
            <div class="form-group">
                <input type="email" id="correo" name="correo"
                    placeholder="Ingresa tu correo"
                    required
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
            </div>

            <div class="form-group">
                <div class="password-input-container">
                    <input type="password" id="contrasena" name="contrasena"
                        placeholder="Ingresa tu contraseña"
                        required
                        minlength="8">
                    <span class="toggle-password" onclick="togglePasswordVisibility('contrasena', 'eyeIconContrasena')">
                        <i class="fa fa-eye" id="eyeIconContrasena"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="login-button">Ingresar</button>
            <hr>
            <div class="register-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
            </div>
        </form>
    </div>

    <script>
        // Validación básica del formulario antes de enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contrasena').value;

            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }

            return true;
        });
    </script>

    <script>
        //ojito js 
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
// inicio.js - Versión corregida y optimizada

document.addEventListener('DOMContentLoaded', function() {
    // ==================== RELOJ DIGITAL ====================
    function updateClock() {
        const now = new Date();
        
        // Formatear hora (HH:MM:SS)
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        
        // Formatear fecha (Ej: "Lunes, 15 de abril de 2024")
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const dateStr = now.toLocaleDateString('es-ES', options);
        document.getElementById('date').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
        
        // Actualizar cada segundo
        setTimeout(updateClock, 1000);
    }

    // ==================== SISTEMA DE LOGOUT ====================
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutContainer = document.querySelector('.logout-container');
    const logoutConfirm = document.getElementById('logoutConfirm');
    const logoutNo = document.getElementById('logoutNo');

    if (logoutBtn && logoutContainer && logoutConfirm && logoutNo) {
        // Mostrar/ocultar confirmación
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutConfirm.classList.toggle('show');
            this.classList.add('vibrate');
        });

        // Eliminar animación al terminar
        logoutBtn.addEventListener('animationend', function() {
            this.classList.remove('vibrate');
        });

        // Cancelar logout
        logoutNo.addEventListener('click', function(e) {
            e.preventDefault();
            logoutConfirm.classList.remove('show');
        });

        

        // Efecto hover para el botón de logout
        logoutBtn.addEventListener('mouseenter', function() {
            if (!this.classList.contains('vibrate')) {
                this.classList.add('vibrate');
            }
        });
    } else {
        console.error('No se encontraron todos los elementos del logout');
    }

    // ==================== INICIALIZACIONES ====================
    updateClock(); // Iniciar el reloj

    // Resaltar el día actual en el calendario
    const todayCells = document.querySelectorAll('.today');
    todayCells.forEach(cell => {
        cell.innerHTML = `<strong>${cell.textContent}</strong>`;
    });

    // ==================== FUNCIONES AUXILIARES ====================
    console.log('Sistema iniciado correctamente');
});


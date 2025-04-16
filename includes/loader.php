<div class="loader-overlay" id="loaderOverlay">
    <div class="load-row">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<style>
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #fff; /* Fondo sólido en lugar de transparente */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.load-row {
    width: 100px;
    height: 50px;
    line-height: 50px;
    text-align: center;
}

.load-row span {
    display: inline-block;
    width: 12px;
    height: 12px;
    background: #e74c3c;
    border-radius: 50px;
    animation: up-down6 0.8s ease-in-out infinite alternate;
    margin: 0 4px;
}

.load-row span:nth-child(2) {
    background: #2c3e50;
    animation-delay: 0.2s;
}

.load-row span:nth-child(3) {
    background: #e74c3c;
    animation-delay: 0.4s;
}

.load-row span:nth-child(4) {
    background: #2c3e50;
    animation-delay: 0.6s;
}

@keyframes up-down6 {
    0% {
        transform: translateY(-12px);
    }
    100% {
        transform: translateY(12px);
    }
}

/* Ocultar el contenido principal inicialmente */
body > *:not(.loader-overlay) {
    opacity: 0;
    transition: opacity 0.5s ease-in;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar loader inmediatamente
    const loader = document.getElementById('loaderOverlay');
    const bodyContent = document.querySelectorAll('body > *:not(.loader-overlay)');
    
    // Ocultar todo el contenido excepto el loader
    bodyContent.forEach(el => {
        el.style.opacity = '0';
    });
    
    // Configurar temporizador para 1 segundo exacto
    setTimeout(() => {
        // Ocultar loader con animación
        loader.style.opacity = '0';
        
        // Mostrar contenido después de que el loader desaparezca
        setTimeout(() => {
            loader.style.display = 'none';
            bodyContent.forEach(el => {
                el.style.opacity = '1';
            });
        }, 500); // Medio segundo para la transición de fade out
    }, 1000); // 1 segundo de duración total
});

// Manejar navegación entre páginas
window.addEventListener('beforeunload', function() {
    document.getElementById('loaderOverlay').style.display = 'flex';
    document.getElementById('loaderOverlay').style.opacity = '1';
});
</script>
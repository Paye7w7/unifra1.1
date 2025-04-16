// Función para actualizar URL con los parámetros
function actualizarURL() {
    const params = new URLSearchParams();
    const categoriaId = document.getElementById('categoria')?.value;
    const codigoValue = document.getElementById('codigo')?.value;
    const buscadorValue = document.getElementById('buscador')?.value.trim();

    // Si hay código seleccionado, redirigir directamente
    if (codigoValue) {
        window.location.href = '../../dashboard/documentos/documentos.php?codigo=' + encodeURIComponent(codigoValue);
        return;
    }

    // Si hay categoría seleccionada (y no es "Todas")
    if (categoriaId && categoriaId !== '0') {
        params.set('cat', categoriaId);
    }

    // Si hay texto de búsqueda
    if (buscadorValue) {
        params.set('buscador', buscadorValue);
    }

    // Construir URL final
    const nuevaURL = window.location.pathname + (params.toString() ? `?${params.toString()}` : '');
    window.location.href = nuevaURL;
}

// Evento al cambiar categoría
document.getElementById('categoria')?.addEventListener('change', function() {
    document.getElementById('codigo').selectedIndex = 0;
    document.getElementById('buscador').value = '';
    actualizarURL();
});

// Evento al cambiar código
document.getElementById('codigo')?.addEventListener('change', function() {
    document.getElementById('buscador').value = '';
    actualizarURL();
});

// Buscador se activa al presionar Enter
document.getElementById('buscador')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('codigo').selectedIndex = 0;
        actualizarURL();
    }
});

// Función para borrar filtros
function borrarFiltros() {
    window.location.href = window.location.pathname;
}

// Autocompletado de códigos
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const codigoSeleccionado = urlParams.get('codigo');
    const buscador = document.getElementById('buscador');
    const sugerencias = document.getElementById('sugerencias-codigos');
    const selectCodigo = document.getElementById('codigo');

    // Marcar código seleccionado si viene en la URL
    if (codigoSeleccionado && selectCodigo) {
        for (let i = 0; i < selectCodigo.options.length; i++) {
            if (selectCodigo.options[i].value === codigoSeleccionado) {
                selectCodigo.selectedIndex = i;
                break;
            }
        }
    }

    // Configurar autocompletado si existen los elementos
    if (buscador && sugerencias && selectCodigo) {
        // Obtener todos los códigos disponibles
        const codigosDisponibles = [];
        for (let i = 0; i < selectCodigo.options.length; i++) {
            if (selectCodigo.options[i].value) {
                codigosDisponibles.push(selectCodigo.options[i].value);
            }
        }

        buscador.addEventListener('input', function() {
            const texto = this.value.trim().toUpperCase();
            sugerencias.innerHTML = '';

            if (texto.length > 1) {
                const coincidencias = codigosDisponibles.filter(codigo => 
                    codigo.includes(texto)
                );

                if (coincidencias.length > 0) {
                    sugerencias.style.display = 'block';
                    coincidencias.forEach(codigo => {
                        const div = document.createElement('div');
                        div.textContent = codigo;
                        div.addEventListener('click', function() {
                            window.location.href = '../../dashboard/documentos/documentos.php?codigo=' + encodeURIComponent(codigo);
                        });
                        sugerencias.appendChild(div);
                    });
                } else {
                    sugerencias.style.display = 'none';
                }
            } else {
                sugerencias.style.display = 'none';
            }
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target !== buscador && e.target !== sugerencias) {
                sugerencias.style.display = 'none';
            }
        });
    }
});
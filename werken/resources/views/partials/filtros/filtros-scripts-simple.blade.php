{{-- Script para funcionalidad automática de filtros en Búsqueda Simple --}}
<script>
    // Aplicar filtros automáticamente cuando cambian los checkboxes
    function aplicarFiltrosAutomatico() {
        console.log('Aplicando filtros automáticamente...');
        document.getElementById('filtros-form').submit();
    }

    // Configurar listeners para los filtros automáticos
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Configurando filtros automáticos para Búsqueda Simple...');
        
        // Seleccionar todos los checkboxes de filtros
        const checkboxes = document.querySelectorAll('#filtros-form input[type="checkbox"]');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                console.log('Checkbox cambiado:', this.name, this.value, this.checked);
                
                // Aplicar filtros automáticamente con un pequeño delay
                setTimeout(function() {
                    aplicarFiltrosAutomatico();
                }, 100);
            });
        });

        // Funcionalidad de filtros colapsables
        const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
        
        collapsibleHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const filter = this.closest('.collapsible-filter');
                filter.classList.toggle('expanded');
            });
        });
    });

    // Función para filtrar opciones en tiempo real
    function filterOptions(filterType, searchValue) {
        const options = document.querySelectorAll(`#options-${filterType} .filter-option`);
        const noResultsMessage = document.querySelector(`#no-results-${filterType}`);
        let hasVisibleOptions = false;

        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            const matches = text.includes(searchValue.toLowerCase());
            option.style.display = matches ? 'flex' : 'none';
            if (matches) hasVisibleOptions = true;
        });

        if (noResultsMessage) {
            noResultsMessage.style.display = hasVisibleOptions ? 'none' : 'block';
        }
    }
</script>

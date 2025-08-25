{{-- 
Scripts JavaScript para funcionalidad de filtros
Este archivo contiene todas las funciones JS relacionadas con:
- Filtrado de opciones
- Toggle de filtros colapsables  
- Exportación múltiple
- Manejo de checkboxes
--}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle collapsible filters
        document.querySelectorAll('.collapsible-header').forEach(header => {
            header.addEventListener('click', function() {
                const filter = this.closest('.collapsible-filter');
                filter.classList.toggle('expanded');
                
                // Enfocar automáticamente el campo de búsqueda cuando se abre un filtro
                setTimeout(() => {
                    if (filter.classList.contains('expanded')) {
                        const searchInput = filter.querySelector('.filter-search-input');
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }
                }, 300);
            });
        });

        // Inicializar campos de búsqueda
        const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
        filterTypes.forEach(type => {
            const searchInput = document.getElementById(`search-${type}`);
            if (searchInput) {
                // Guardar placeholder original
                const originalPlaceholder = searchInput.getAttribute('placeholder');
                searchInput.setAttribute('data-original-placeholder', originalPlaceholder);
            }
        });

        // Permitir usar Enter para buscar
        document.querySelectorAll('.filter-search-input').forEach(input => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
            
            // Mostrar contador de opciones visibles en tiempo real
            const originalPlaceholder = input.getAttribute('placeholder');
            input.setAttribute('data-original-placeholder', originalPlaceholder);
            
            input.addEventListener('input', function() {
                const filterType = this.id.replace('search-', '');
                const optionsContainer = document.getElementById(`options-${filterType}`);
                if (optionsContainer) {
                    const visibleCount = optionsContainer.querySelectorAll('.filter-option:not(.hidden)').length;
                    
                    if (this.value.trim() !== '') {
                        this.setAttribute('placeholder', `${visibleCount} resultado(s) encontrado(s)`);
                    } else {
                        this.setAttribute('placeholder', originalPlaceholder);
                    }
                }
            });
        });

        // Inicializar checkboxes para aplicación automática
        const form = document.getElementById('filtros-form');
        if (form) {
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    setTimeout(aplicarFiltrosAutomatico, 300);
                });
            });
        }

        // Event listeners para exportación múltiple
        const selectAllCheckbox = document.getElementById('selectAll');
        const exportForm = document.getElementById('exportForm');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', toggleSelectAll);
        }
        
        if (exportForm) {
            exportForm.addEventListener('submit', handleExportSubmit);
        }
        
        // Inicializar estado del botón
        updateExportButton();
    });

    // Función para normalizar texto (UTF-8 safe)
    function normalizeText(text) {
        return text.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remover acentos
            .trim();
    }

    // Función para filtrar opciones dentro de cada filtro (UTF-8 safe)
    function filterOptions(filterType, searchValue) {
        const optionsContainer = document.getElementById(`options-${filterType}`);
        const filterOptions = optionsContainer.querySelectorAll('.filter-option');
        const noResultsMessage = document.getElementById(`no-results-${filterType}`);
        
        let visibleCount = 0;
        const normalizedSearch = normalizeText(searchValue);
        
        filterOptions.forEach(option => {
            // Buscar el texto en el label - adaptado a la nueva estructura
            let optionText = '';
            
            // Buscar primero por clase filter-checkbox-label (estructura nueva)
            let labelElement = option.querySelector('.filter-checkbox-label');
            if (!labelElement) {
                // Si no existe, buscar label genérico (estructura actual)
                labelElement = option.querySelector('label');
            }
            
            if (labelElement) {
                optionText = labelElement.textContent.trim();
            } else {
                // Fallback: buscar cualquier texto en el div
                optionText = option.textContent.trim();
                // Limpiar texto de elementos extra como checkboxes
                optionText = optionText.replace(/^\s*\[\s*\]\s*/, '').trim();
            }
            
            const normalizedOptionText = normalizeText(optionText);
            const shouldShow = normalizedOptionText.includes(normalizedSearch);
            
            if (shouldShow) {
                option.classList.remove('hidden');
                option.style.display = '';
                visibleCount++;
            } else {
                option.classList.add('hidden');
                option.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar mensaje de "no resultados"
        if (visibleCount === 0 && normalizedSearch !== '') {
            noResultsMessage.style.display = 'block';
        } else {
            noResultsMessage.style.display = 'none';
        }
    }

    // Función para aplicar filtros automáticamente al cambiar checkboxes
    function aplicarFiltrosAutomatico() {
        const form = document.getElementById('filtros-form');
        if (form) {
            form.submit();
        }
    }
    
    // Función para limpiar todas las búsquedas de filtros
    function clearAllFilterSearches() {
        const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
        
        filterTypes.forEach(filterType => {
            const searchInput = document.getElementById(`search-${filterType}`);
            if (searchInput) {
                searchInput.value = '';
                // Restaurar placeholder original
                const originalPlaceholder = searchInput.getAttribute('data-original-placeholder') || 
                                          searchInput.getAttribute('placeholder');
                searchInput.setAttribute('placeholder', originalPlaceholder);
                filterOptions(filterType, '');
            }
        });
    }

    // Función para contar filtros activos
    function contarFiltrosActivos() {
        const form = document.getElementById('filtros-form');
        if (!form) return 0;
        
        const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
        return checkboxes.length;
    }
    
    // Función para mostrar confirmación antes de limpiar filtros
    function confirmarLimpiarFiltros() {
        const totalActivos = contarFiltrosActivos();
        if (totalActivos > 0) {
            return confirm(`¿Estás seguro de que quieres limpiar los ${totalActivos} filtro(s) activo(s)?`);
        }
        return true;
    }

    // Función para mostrar u ocultar filtros en dispositivos móviles
    function toggleMobileFilters() {
        const filterSection = document.getElementById('mobile-filters');
        filterSection.classList.toggle('hidden');
    }

    // Funciones para manejo de exportación múltiple
    function updateExportButton() {
        const checkboxes = document.querySelectorAll('input[name="resource_checkbox"]:checked');
        const exportButton = document.getElementById('exportButton');
        const selectedCount = document.getElementById('selectedCount');
        const selectAllCheckbox = document.getElementById('selectAll');
        const totalCheckboxes = document.querySelectorAll('input[name="resource_checkbox"]');
        
        // Actualizar contador
        selectedCount.textContent = checkboxes.length;
        
        // Habilitar/deshabilitar botón de exportación
        if (checkboxes.length > 0) {
            exportButton.classList.add('enabled');
        } else {
            exportButton.classList.remove('enabled');
        }
        
        // Actualizar estado del checkbox "Seleccionar todos"
        if (checkboxes.length === totalCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const resourceCheckboxes = document.querySelectorAll('input[name="resource_checkbox"]');
        
        resourceCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateExportButton();
    }

    function handleExportSubmit(event) {
        const checkboxes = document.querySelectorAll('input[name="resource_checkbox"]:checked');
        
        if (checkboxes.length === 0) {
            event.preventDefault();
            alert('Por favor, selecciona al menos un recurso para exportar.');
            return false;
        }
        
        // Agregar los números de control seleccionados al formulario
        const form = document.getElementById('exportForm');
        
        // Limpiar inputs anteriores
        form.querySelectorAll('input[name="nro_controles[]"]').forEach(input => {
            input.remove();
        });
        
        // Agregar nuevos inputs
        checkboxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'nro_controles[]';
            hiddenInput.value = checkbox.value;
            form.appendChild(hiddenInput);
        });
        
        // Mostrar mensaje de carga
        const exportButton = document.getElementById('exportButton');
        const originalText = exportButton.innerHTML;
        exportButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando archivos...';
        exportButton.disabled = true;
        
        // Restaurar botón después de un tiempo
        setTimeout(() => {
            exportButton.innerHTML = originalText;
            exportButton.disabled = false;
        }, 5000);
        
        return true;
    }
</script>

{{-- Las funciones JavaScript de filtros se moverán aquí en el siguiente paso --}}
<script>
    // Funcionalidad adicional de filtros se puede agregar aquí
</script>

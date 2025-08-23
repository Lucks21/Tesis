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
        console.log('Inicializando filtros...');
        
        // Toggle collapsible filters
        document.querySelectorAll('.collapsible-header').forEach(header => {
            header.addEventListener('click', function() {
                const filter = this.closest('.collapsible-filter');
                filter.classList.toggle('expanded');
            });
        });

        // Verificar que existen los campos de búsqueda
        const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
        filterTypes.forEach(type => {
            const searchInput = document.getElementById(`search-${type}`);
            const optionsContainer = document.getElementById(`options-${type}`);
            const noResultsMessage = document.getElementById(`no-results-${type}`);
            
            console.log(`Filtro ${type}:`, {
                searchInput: !!searchInput,
                optionsContainer: !!optionsContainer, 
                noResultsMessage: !!noResultsMessage
            });
            
            if (searchInput) {
                // Guardar placeholder original
                const originalPlaceholder = searchInput.getAttribute('placeholder');
                searchInput.setAttribute('data-original-placeholder', originalPlaceholder);
                console.log(`Placeholder guardado para ${type}: ${originalPlaceholder}`);
            }
        });

        // Enfocar automáticamente el campo de búsqueda cuando se abre un filtro
        document.querySelectorAll('.collapsible-header').forEach(header => {
            header.addEventListener('click', function() {
                const filter = this.closest('.collapsible-filter');
                setTimeout(() => {
                    if (filter.classList.contains('expanded')) {
                        const searchInput = filter.querySelector('.filter-search-input');
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }
                }, 300); // Esperar a que termine la animación de expansión
            });
        });

        // Permitir usar Enter para buscar
        document.querySelectorAll('.filter-search-input').forEach(input => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    // El filtrado ya se aplica automáticamente con onkeyup
                }
            });
        });

        // Mostrar contador de opciones visibles en tiempo real
        document.querySelectorAll('.filter-search-input').forEach(input => {
            // Guardar placeholder original
            const originalPlaceholder = input.getAttribute('placeholder');
            input.setAttribute('data-original-placeholder', originalPlaceholder);
            
            input.addEventListener('input', function() {
                const filterType = this.id.replace('search-', '');
                const optionsContainer = document.getElementById(`options-${filterType}`);
                if (optionsContainer) {
                    const visibleCount = optionsContainer.querySelectorAll('.filter-option:not(.hidden)').length;
                    
                    // Actualizar el placeholder dinámicamente
                    if (this.value.trim() !== '') {
                        this.setAttribute('placeholder', `${visibleCount} resultado(s) encontrado(s)`);
                    } else {
                        this.setAttribute('placeholder', originalPlaceholder);
                    }
                }
            });
        });

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
        
        console.log('Filtros inicializados correctamente');
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
        
        // Debug para ayudar con troubleshooting
        console.log(`Filtro ${filterType}: buscando "${searchValue}", encontrados ${visibleCount} resultados`);
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

    // Función para mostrar estadísticas de filtros (UTF-8 safe)
    function showFilterStats() {
        const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
        let stats = 'Estadísticas de filtros:\n\n';
        
        filterTypes.forEach(filterType => {
            const optionsContainer = document.getElementById(`options-${filterType}`);
            if (optionsContainer) {
                const totalOptions = optionsContainer.querySelectorAll('.filter-option').length;
                const visibleOptions = optionsContainer.querySelectorAll('.filter-option:not(.hidden)').length;
                const checkedOptions = optionsContainer.querySelectorAll('.filter-option input[type="checkbox"]:checked').length;
                
                stats += `${filterType.charAt(0).toUpperCase() + filterType.slice(1)}:\n`;
                stats += `  - Total: ${totalOptions}\n`;
                stats += `  - Visibles: ${visibleOptions}\n`;
                stats += `  - Seleccionados: ${checkedOptions}\n\n`;
            }
        });
        
        alert(stats);
    }

    // Función de debug para verificar texto de opciones
    function debugFilterOptions(filterType) {
        const optionsContainer = document.getElementById(`options-${filterType}`);
        const options = optionsContainer.querySelectorAll('.filter-option');
        
        console.log(`Debug para filtro: ${filterType}`);
        options.forEach((option, index) => {
            // Adaptado a la nueva estructura
            let labelElement = option.querySelector('.filter-checkbox-label');
            if (!labelElement) {
                labelElement = option.querySelector('label');
            }
            
            let text = 'Sin texto';
            if (labelElement) {
                text = labelElement.textContent.trim();
            } else {
                text = option.textContent.trim().replace(/^\s*\[\s*\]\s*/, '').trim();
            }
            
            const normalized = normalizeText(text);
            console.log(`${index + 1}. Original: "${text}" | Normalizado: "${normalized}"`);
        });
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
</script>cripts JavaScript para funcionalidad de filtros
    Este archivo contiene todas las funciones JS relacionadas con:
    - Filtrado de opciones
    - Toggle de filtros colapsables
    - Exportación múltiple
    - Manejo de checkboxes
--}}

<script>
    // Las funciones JavaScript de filtros se moverán aquí en el siguiente paso
</script>

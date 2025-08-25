{{-- 
    Estilos CSS específicos para los filtros de búsqueda
    Este archivo contiene todos los estilos relacionados con:
    - Filtros colapsables
    - Búsqueda dentro de filtros
    - Opciones de filtros
    - Controles de exportación
--}}

<style>
    /* Filter section base styles */
    .filter-section {
        background: #ffffff;
        border: 1px solid rgba(0, 56, 118, 0.1);
        border-radius: 0.5rem;
    }
    
    .filter-section h2 {
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: bold;
    }

    /* Form elements for filters */
    .form-checkbox {
        color: #003876;
        border-color: #003876;
        accent-color: #003876;
    }
    
    .form-checkbox:checked {
        background-color: #003876;
    }

    /* Filter buttons */
    .filter-button {
        background-color: #003876;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: bold;
        min-width: 160px;
        text-align: center;
    }

    .filter-button i {
        margin-right: 0.5rem;
    }

    .filter-button:hover {
        background-color: #002b5c;
    }

    .remove-filter {
        background-color: #dc2626;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.2s;
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: bold;
    }
    
    .remove-filter:hover {
        background-color: #b91c1c;
    }

    /* Collapsible filter styles */
    .collapsible-filter {
        border: 1px solid rgba(0, 56, 118, 0.1);
        border-radius: 0.5rem;
        overflow: hidden;
        background: #ffffff;
    }

    .collapsible-header {
        background: #003876;
        color: white;
        padding: 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s;
        user-select: none;
    }

    .collapsible-header:hover {
        background: #002b5c;
    }

    .collapsible-header h2 {
        margin: 0;
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: bold;
        font-size: 1.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .collapsible-toggle {
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }

    .collapsible-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background: white;
    }

    .collapsible-inner {
        padding: 1rem;
    }

    /* When expanded */
    .collapsible-filter.expanded .collapsible-content {
        max-height: 500px;
    }

    .collapsible-filter.expanded .collapsible-toggle {
        transform: rotate(180deg);
    }

    /* Active filter indicator */
    .collapsible-filter.has-active-filter .collapsible-header {
        background: #1d4ed8;
    }

    .collapsible-filter.has-active-filter .collapsible-header:hover {
        background: #1e40af;
    }

    /* Filter search styles */
    .filter-search-container {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .filter-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
    }

    .filter-search-input:focus {
        outline: none;
        border-color: #003876;
    }

    .filter-options-container {
        max-height: 300px;
        overflow-y: auto;
        padding: 0.75rem;
    }

    .filter-options-container::-webkit-scrollbar {
        width: 6px;
    }

    .filter-options-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .filter-options-container::-webkit-scrollbar-thumb {
        background: #003876;
        border-radius: 3px;
    }

    .filter-options-container::-webkit-scrollbar-thumb:hover {
        background: #002b5c;
    }

    .filter-option {
        display: flex;
        align-items: center;
        padding: 0.375rem 0;
        margin-bottom: 0.25rem;
        transition: background-color 0.2s;
        border-radius: 0.25rem;
    }

    .filter-option:hover {
        background-color: #f3f4f6;
    }

    .filter-option.hidden {
        display: none;
    }

    .filter-count {
        font-size: 0.75rem;
        color: #6b7280;
        margin-left: 0.5rem;
    }

    .no-results-message {
        text-align: center;
        color: #6b7280;
        font-style: italic;
        padding: 1rem;
    }

    /* Export controls styles */
    .export-controls {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .export-button {
        background-color: #059669;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: bold;
        min-width: 180px;
        text-align: center;
        opacity: 0.5;
        pointer-events: none;
    }

    .export-button.enabled {
        opacity: 1;
        pointer-events: auto;
    }

    .export-button:hover.enabled {
        background-color: #047857;
    }

    .export-button i {
        margin-right: 0.5rem;
    }

    .select-all-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .select-all-checkbox {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: #003876;
        cursor: pointer;
    }

    .select-all-label {
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .selection-counter {
        font-family: 'Tipo-UBB', sans-serif;
        font-weight: 600;
        color: #059669;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }

    .resource-checkbox {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: #003876;
        cursor: pointer;
    }

    .resource-checkbox:checked + td {
        background-color: #f0f7ff;
    }

    tr:has(.resource-checkbox:checked) {
        background-color: #f0f7ff;
        border-left: 3px solid #003876;
    }

    tr:has(.resource-checkbox:checked):hover {
        background-color: #e0f2fe;
    }

    /* Layout for filters and results */
    .filters-sidebar {
        width: 220px;
        flex-shrink: 0;
    }
    
    .results-main {
        flex: 1;
        min-width: 0; /* Permite que el flex funcione correctamente */
    }
    
    /* Para pantallas móviles */
    @media (max-width: 1023px) {
        .filters-sidebar {
            width: 100%;
            order: 2;
        }
        
        .results-main {
            width: 100%;
            order: 1;
        }
    }
</style>

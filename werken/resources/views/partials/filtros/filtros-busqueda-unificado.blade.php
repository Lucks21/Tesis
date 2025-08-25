{{-- 
    Filtros de búsqueda con UN SOLO FORMULARIO
    Solucionando el problema de filtros combinados
    
    Variables esperadas del controlador:
    - $autores (Collection)
    - $editoriales (Collection) 
    - $materias (Collection)
    - $series (Collection)
    - $campuses (Collection)
--}}

<div class="filters-sidebar space-y-3">
    {{-- UN SOLO FORMULARIO PARA TODOS LOS FILTROS --}}
    <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" id="filtros-form" class="space-y-3">
        {{-- Campos hidden para mantener parámetros de búsqueda --}}
        <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
        <input type="hidden" name="criterio" value="{{ request('criterio') }}">
        <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
        <input type="hidden" name="titulo" value="{{ request('titulo') }}">

        {{-- Filtro por Autor --}}
        <div class="collapsible-filter {{ request()->filled('autor') ? 'has-active-filter expanded' : '' }}">
            <div class="collapsible-header">
                <h2>
                    <i class="fas fa-user-edit mr-2"></i>Filtrar por Autor
                    @if(request()->filled('autor'))
                        @php
                            $autorActivos = is_array(request('autor')) 
                                ? array_filter(request('autor'), function($value) { 
                                    return !empty(trim($value)); 
                                })
                                : (request('autor') ? [request('autor')] : []);
                        @endphp
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($autorActivos) }} activo(s)
                        </span>
                    @endif
                </h2>
                <i class="fas fa-chevron-down collapsible-toggle"></i>
            </div>
            <div class="collapsible-content">
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search-input" 
                           placeholder="Buscar autor..." 
                           id="search-autor"
                           onkeyup="filterOptions('autor', this.value)">
                </div>
                <div class="collapsible-inner">
                    <div class="filter-options-container" id="options-autor">
                        @foreach ($autores as $autor)
                            <div class="filter-option">
                                <input type="checkbox" name="autor[]" id="autor_{{ $loop->index }}"
                                       value="{{ $autor }}" {{ is_array(request('autor')) && in_array($autor, request('autor')) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <label for="autor_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                    {{ $autor }}
                                </label>
                            </div>
                        @endforeach
                        <div class="no-results-message" id="no-results-autor" style="display: none;">
                            No se encontraron autores con ese criterio.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtro por Editorial --}}
        <div class="collapsible-filter {{ request()->filled('editorial') ? 'has-active-filter expanded' : '' }}">
            <div class="collapsible-header">
                <h2>
                    <i class="fas fa-building mr-2"></i>Filtrar por Editorial
                    @if(request()->filled('editorial'))
                        @php
                            $editorialActivos = is_array(request('editorial')) 
                                ? array_filter(request('editorial'), function($value) { 
                                    return !empty(trim($value)); 
                                })
                                : (request('editorial') ? [request('editorial')] : []);
                        @endphp
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($editorialActivos) }} activo(s)
                        </span>
                    @endif
                </h2>
                <i class="fas fa-chevron-down collapsible-toggle"></i>
            </div>
            <div class="collapsible-content">
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search-input" 
                           placeholder="Buscar editorial..." 
                           id="search-editorial"
                           onkeyup="filterOptions('editorial', this.value)">
                </div>
                <div class="collapsible-inner">
                    <div class="filter-options-container" id="options-editorial">
                        @foreach ($editoriales as $editorial)
                            <div class="filter-option">
                                <input type="checkbox" name="editorial[]" id="editorial_{{ $loop->index }}"
                                       value="{{ $editorial }}" {{ is_array(request('editorial')) && in_array($editorial, request('editorial')) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <label for="editorial_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                    {{ $editorial }}
                                </label>
                            </div>
                        @endforeach
                        <div class="no-results-message" id="no-results-editorial" style="display: none;">
                            No se encontraron editoriales con ese criterio.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtro por Campus --}}
        <div class="collapsible-filter {{ request()->filled('campus') ? 'has-active-filter expanded' : '' }}">
            <div class="collapsible-header">
                <h2>
                    <i class="fas fa-university mr-2"></i>Filtrar por Campus
                    @if(request()->filled('campus'))
                        @php
                            $campusActivos = is_array(request('campus')) 
                                ? array_filter(request('campus'), function($value) { 
                                    return !empty(trim($value)); 
                                })
                                : (request('campus') ? [request('campus')] : []);
                        @endphp
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($campusActivos) }} activo(s)
                        </span>
                    @endif
                </h2>
                <i class="fas fa-chevron-down collapsible-toggle"></i>
            </div>
            <div class="collapsible-content">
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search-input" 
                           placeholder="Buscar campus..." 
                           id="search-campus"
                           onkeyup="filterOptions('campus', this.value)">
                </div>
                <div class="collapsible-inner">
                    <div class="filter-options-container" id="options-campus">
                        @foreach ($campuses as $campus)
                            <div class="filter-option">
                                <input type="checkbox" name="campus[]" id="campus_{{ $loop->index }}"
                                       value="{{ $campus }}" {{ is_array(request('campus')) && in_array($campus, request('campus')) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <label for="campus_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                    {{ $campus }}
                                </label>
                            </div>
                        @endforeach
                        <div class="no-results-message" id="no-results-campus" style="display: none;">
                            No se encontraron campus con ese criterio.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtro por Materia --}}
        <div class="collapsible-filter {{ request()->filled('materia') ? 'has-active-filter expanded' : '' }}">
            <div class="collapsible-header">
                <h2>
                    <i class="fas fa-book mr-2"></i>Filtrar por Materia
                    @if(request()->filled('materia'))
                        @php
                            $materiaActivos = is_array(request('materia')) 
                                ? array_filter(request('materia'), function($value) { 
                                    return !empty(trim($value)); 
                                })
                                : (request('materia') ? [request('materia')] : []);
                        @endphp
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($materiaActivos) }} activo(s)
                        </span>
                    @endif
                </h2>
                <i class="fas fa-chevron-down collapsible-toggle"></i>
            </div>
            <div class="collapsible-content">
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search-input" 
                           placeholder="Buscar materia..." 
                           id="search-materia"
                           onkeyup="filterOptions('materia', this.value)">
                </div>
                <div class="collapsible-inner">
                    <div class="filter-options-container" id="options-materia">
                        @foreach ($materias as $materia)
                            <div class="filter-option">
                                <input type="checkbox" name="materia[]" id="materia_{{ $loop->index }}"
                                       value="{{ $materia }}" {{ is_array(request('materia')) && in_array($materia, request('materia')) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <label for="materia_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                    {{ $materia }}
                                </label>
                            </div>
                        @endforeach
                        <div class="no-results-message" id="no-results-materia" style="display: none;">
                            No se encontraron materias con ese criterio.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtro por Serie --}}
        <div class="collapsible-filter {{ request()->filled('serie') ? 'has-active-filter expanded' : '' }}">
            <div class="collapsible-header">
                <h2>
                    <i class="fas fa-layer-group mr-2"></i>Filtrar por Serie
                    @if(request()->filled('serie'))
                        @php
                            $serieActivos = is_array(request('serie')) 
                                ? array_filter(request('serie'), function($value) { 
                                    return !empty(trim($value)); 
                                })
                                : (request('serie') ? [request('serie')] : []);
                        @endphp
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($serieActivos) }} activo(s)
                        </span>
                    @endif
                </h2>
                <i class="fas fa-chevron-down collapsible-toggle"></i>
            </div>
            <div class="collapsible-content">
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search-input" 
                           placeholder="Buscar serie..." 
                           id="search-serie"
                           onkeyup="filterOptions('serie', this.value)">
                </div>
                <div class="collapsible-inner">
                    <div class="filter-options-container" id="options-serie">
                        @foreach ($series as $serie)
                            <div class="filter-option">
                                <input type="checkbox" name="serie[]" id="serie_{{ $loop->index }}"
                                       value="{{ $serie }}" {{ is_array(request('serie')) && in_array($serie, request('serie')) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <label for="serie_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                    {{ $serie }}
                                </label>
                            </div>
                        @endforeach
                        <div class="no-results-message" id="no-results-serie" style="display: none;">
                            No se encontraron series con ese criterio.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de Control del Formulario --}}
        <div class="filter-controls-section">
            <div class="pt-4 border-t border-gray-200 space-y-3">
                
                
                <button type="submit" class="filter-button w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Aplicar Filtros
                </button>
                
                @if(request()->hasAny(['autor', 'editorial', 'campus', 'materia', 'serie']))
                    <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except(['autor', 'editorial', 'campus', 'materia', 'serie']))) }}"
                       class="remove-filter w-full text-center block bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar Todos los Filtros
                    </a>
                @endif
                
                <div class="text-center text-sm text-gray-600">
                    @php
                        $totalFiltrosActivos = 0;
                        if(request()->filled('autor')) $totalFiltrosActivos += count(request('autor', []));
                        if(request()->filled('editorial')) $totalFiltrosActivos += count(request('editorial', []));
                        if(request()->filled('campus')) $totalFiltrosActivos += count(request('campus', []));
                        if(request()->filled('materia')) $totalFiltrosActivos += count(request('materia', []));
                        if(request()->filled('serie')) $totalFiltrosActivos += count(request('serie', []));
                    @endphp
                    
                    @if($totalFiltrosActivos > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                            <i class="fas fa-filter mr-1"></i>
                            {{ $totalFiltrosActivos }} filtro(s) activo(s)
                        </span>
                    @else
                        <span class="text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sin filtros aplicados
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

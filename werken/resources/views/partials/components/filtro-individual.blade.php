{{-- 
    Componente reutilizable para filtros individuales
    
    Parámetros esperados:
    @param string $tipo - Tipo de filtro (autor, editorial, materia, serie, campus)
    @param string $titulo - Título a mostrar en el header del filtro
    @param Collection $opciones - Opciones disponibles para filtrar
    @param array $seleccionados - Valores actualmente seleccionados
    
    Uso:
    @include('partials.components.filtro-individual', [
        'tipo' => 'autor',
        'titulo' => 'Filtrar por Autor',
        'opciones' => $autores,
        'seleccionados' => request('autor', [])
    ])
--}}

<div class="collapsible-filter" id="filter-{{ $tipo }}">
    <div class="collapsible-header">
        <h4>{{ $titulo }}</h4>
        <i class="fas fa-chevron-down"></i>
    </div>
    
    <div class="collapsible-content">
        <div class="filter-search-container">
            <input 
                type="text" 
                id="search-{{ $tipo }}" 
                class="filter-search-input" 
                placeholder="Buscar {{ strtolower($titulo) }}..." 
                onkeyup="filterOptions('{{ $tipo }}', this.value)"
            >
            <i class="fas fa-search filter-search-icon"></i>
        </div>
        
        <div class="filter-options-container" id="options-{{ $tipo }}">
            @if(isset($opciones) && $opciones && $opciones->count() > 0)
                @foreach($opciones as $opcion)
                    @php
                        // Determinar el campo a usar basado en el tipo
                        $campo = match($tipo) {
                            'autor' => 'nombre_autor',
                            'editorial' => 'nombre_editorial', 
                            'campus' => 'nombre_campus',
                            'materia' => 'nombre_materia',
                            'serie' => 'nombre_serie',
                            default => 'nombre'
                        };
                        
                        // Obtener el valor del campo
                        $valor = $opcion->$campo ?? $opcion->nombre ?? '';
                        
                        // Verificar si está seleccionado
                        $seleccionados = $seleccionados ?? [];
                        $estaSeleccionado = in_array($valor, $seleccionados);
                    @endphp
                    
                    <div class="filter-option">
                        <label class="filter-checkbox-label">
                            <input 
                                type="checkbox" 
                                name="{{ $tipo }}[]" 
                                value="{{ $valor }}"
                                @if($estaSeleccionado) checked @endif
                                onchange="updateExportButton()"
                            >
                            <span class="checkmark"></span>
                            {{ $valor }}
                        </label>
                    </div>
                @endforeach
            @else
                <div class="no-options-message">
                    <i class="fas fa-info-circle"></i>
                    No hay {{ strtolower(str_replace('Filtrar por ', '', $titulo)) }} disponibles
                </div>
            @endif
            
            <div id="no-results-{{ $tipo }}" class="no-results-message" style="display: none;">
                <i class="fas fa-search"></i>
                No se encontraron resultados
            </div>
        </div>
    </div>
</div>

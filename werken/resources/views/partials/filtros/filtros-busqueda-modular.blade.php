{{-- 
    Filtros de búsqueda usando componentes reutilizables
    Esta es una versión modular que utiliza el componente filtro-individual
--}}

<div class="search-filters">
    {{-- Filtro de Autor --}}
    @include('partials.components.filtro-individual', [
        'tipo' => 'autor',
        'titulo' => 'Filtrar por Autor',
        'opciones' => $autores,
        'seleccionados' => request('autor', [])
    ])

    {{-- Filtro de Editorial --}}
    @include('partials.components.filtro-individual', [
        'tipo' => 'editorial',
        'titulo' => 'Filtrar por Editorial',
        'opciones' => $editoriales,
        'seleccionados' => request('editorial', [])
    ])

    {{-- Filtro de Biblioteca --}}
    @include('partials.components.filtro-individual', [
        'tipo' => 'campus',
        'titulo' => 'Filtrar por Biblioteca',
        'opciones' => $campuses,
        'seleccionados' => request('campus', [])
    ])

    {{-- Filtro de Materia --}}
    @include('partials.components.filtro-individual', [
        'tipo' => 'materia',
        'titulo' => 'Filtrar por Materia',
        'opciones' => $materias,
        'seleccionados' => request('materia', [])
    ])

    {{-- Filtro de Serie --}}
    @include('partials.components.filtro-individual', [
        'tipo' => 'serie',
        'titulo' => 'Filtrar por Serie',
        'opciones' => $series,
        'seleccionados' => request('serie', [])
    ])
</div>

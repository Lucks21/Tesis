<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheManager
{
    public function handle(Request $request, Closure $next)
    {
        // esto es para limpiar el cache expirado automaticamente en cada request
        if (config('app.cache_enabled', true)) {
            $this->limpiarCacheExpirado();
        }
        
        $response = $next($request);
        
        if ($request->expectsJson()) {
            $cache_info = $this->obtenerInfoCache();
            $response->headers->set('X-Cache-Status', $cache_info['status']);
            $response->headers->set('X-Cache-Age', $cache_info['age']);
        }
        
        return $response;
    }
    
    // esto es para limpiar el cache expirado automaticamente
    private function limpiarCacheExpirado()
    {
        $cache_ttl = config('app.cache_ttl', 1800);
        $current_time = time();
    
        $main_timestamp = session('cache_timestamp', 0);
        if (($current_time - $main_timestamp) > $cache_ttl && session()->has('busqueda')) {
            $this->limpiarCachePrincipal();
        }
        
        // se verifican los filtros
        $this->limpiarFiltrosExpirados();
    }
    
    // esto es para limpiar el cache principal expirado
    private function limpiarCachePrincipal()
    {
        $keys_principales = [
            'busqueda', 'tipo_busqueda', 'texto_busqueda', 
            'nav_pagina', 'busq_numrows', 'ind_busqueda',
            'cache_timestamp', 'cache_version', 'query_execution_time'
        ];
        
        foreach ($keys_principales as $key) {
            session()->forget($key);
        }
    }
    
    //Limpiar filtros expirados
    private function limpiarFiltrosExpirados()
    {
        $filter_ttl = config('app.cache_filters_ttl', 3600);
        $current_time = time();
        $session_data = session()->all();
        
        foreach ($session_data as $key => $value) {
            if (strpos($key, 'filtros_') === 0 && strpos($key, '_timestamp') !== false) {
                $timestamp = $value;
                if (($current_time - $timestamp) > $filter_ttl) {
                    $filter_key = str_replace('_timestamp', '', $key);
                    session()->forget($key);
                    session()->forget($filter_key);
                }
            }
        }
    }
    
    // se obtiene la informacion del estado del cache
    private function obtenerInfoCache()
    {
        $cache_timestamp = session('cache_timestamp', 0);
        $current_time = time();
        $age = $current_time - $cache_timestamp;
        
        $status = 'miss';
        if (session()->has('busqueda')) {
            $status = $age > config('app.cache_ttl', 1800) ? 'expired' : 'hit';
        }
        
        return [
            'status' => $status,
            'age' => $age,
            'timestamp' => $cache_timestamp
        ];
    }
}

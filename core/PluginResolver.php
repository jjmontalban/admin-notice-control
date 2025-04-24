<?php

defined( 'ABSPATH' ) || exit;

class PluginResolver {

    /**
     * Determina si un callback proviene de un plugin y devuelve su slug
     *
     * @param string $callback
     * @return string|null
     */
    public function resolve( string $callback ): ?string {
        try {
            if ( strpos( $callback, '->' ) !== false ) {
                [$class, $method] = explode( '->', $callback );
                $reflector = new ReflectionClass( $class );
            } elseif ( strpos( $callback, '::' ) !== false ) {
                [$class, $method] = explode( '::', $callback );
                $reflector = new ReflectionClass( $class );
            } else {
                $reflector = new ReflectionFunction( $callback );
            }

            $file = $reflector->getFileName();

            if ( ! $file ) {
                return null;
            }
            
            // Normaliza las rutas para compatibilidad en Windows
            $file_normalized = wp_normalize_path( $file );
            $plugin_dir_normalized = wp_normalize_path( WP_PLUGIN_DIR );
            
            if ( strpos( $file_normalized, $plugin_dir_normalized ) !== 0 ) {
                return null;
            }
            
            $relative = str_replace( $plugin_dir_normalized . '/', '', $file_normalized );
            


            $parts = explode( '/', $relative );
            $plugin_dir = $parts[0];

            return 'plugin: ' . $plugin_dir;
        } catch ( ReflectionException $e ) {
            return null;
        }
    }
}
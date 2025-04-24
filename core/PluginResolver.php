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

            if ( ! $file || strpos( $file, WP_PLUGIN_DIR ) !== 0 ) {
                return null;
            }

            $relative = str_replace( WP_PLUGIN_DIR . '/', '', $file );
            $parts = explode( '/', $relative );
            $plugin_dir = $parts[0];

            return 'plugin: ' . $plugin_dir;
        } catch ( ReflectionException $e ) {
            return null;
        }
    }
}
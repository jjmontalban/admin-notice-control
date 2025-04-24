<?php

defined( 'ABSPATH' ) || exit;

class ThemeResolver {

    /**
     * Determina si un callback proviene del tema activo (padre o hijo)
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

            $template_dir  = get_template_directory();
            $stylesheet_dir = get_stylesheet_directory();

            if ( strpos( $file, $stylesheet_dir ) === 0 ) {
                return 'theme: ' . basename( $stylesheet_dir );
            }

            if ( strpos( $file, $template_dir ) === 0 ) {
                return 'theme: ' . basename( $template_dir );
            }

            return null;
        } catch ( ReflectionException $e ) {
            return null;
        }
    }
}
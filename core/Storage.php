<?php

defined( 'ABSPATH' ) || exit;

class Storage {

    private string $option_name = 'anc_hidden_sources';

    /**
     * Devuelve todos los orígenes ocultos (plugins/temas)
     *
     * @return array
     */
    public function get_all(): array {
        return get_option( $this->option_name, [] );
    }

    /**
     * Comprueba si un origen está oculto
     *
     * @param string $source
     * @return bool
     */
    public function is_hidden( string $source ): bool {
        $hidden = $this->get_all();
        return in_array( $source, $hidden, true );
    }

    /**
     * Oculta un origen
     *
     * @param string $source
     */
    public function hide( string $source ): void {
        $hidden = $this->get_all();
        if ( ! in_array( $source, $hidden, true ) ) {
            $hidden[] = $source;
            update_option( $this->option_name, $hidden );
        }
    }

    /**
     * Muestra un origen previamente oculto
     *
     * @param string $source
     */
    public function unhide( string $source ): void {
        $hidden = $this->get_all();
        $filtered = array_filter( $hidden, fn( $s ) => $s !== $source );
        update_option( $this->option_name, $filtered );
    }
}
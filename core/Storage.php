<?php

defined( 'ABSPATH' ) || exit;

class AdminNCStorage {

    private string $option_name = 'adminnc_hidden_sources';

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

    /**
     * Guarda los callbacks activos al momento de ocultar
     */
    public function save_callbacks_snapshot( string $source, array $callbacks ): void {
        update_option( "adminnc_callbacks_snapshot_{$source}", $callbacks );
    }
    

    /**
     * Devuelve el snapshot de callbacks desactivados para un origen
     */
    public function get_callbacks_snapshot( string $source ): array {
        return get_option( "adminnc_callbacks_snapshot_{$source}", [] );
    }

}
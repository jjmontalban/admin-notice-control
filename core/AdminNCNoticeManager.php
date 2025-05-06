<?php

defined( 'ABSPATH' ) || exit;

class AdminNCNoticeManager {

    /**
     * Desactiva los avisos de los orígenes (plugins/temas) ocultos
     */
    public function disable_hidden_callbacks(): void {
        global $wp_filter;

        $hooks = [ 'admin_notices', 'all_admin_notices' ];
        $storage = new AdminNCStorage();
        $hidden_sources = $storage->get_all();

        $pluginResolver = new AdminNCPluginResolver();
        $themeResolver  = new AdminNCThemeResolver();

        foreach ( $hooks as $hook ) {
            if ( ! isset( $wp_filter[ $hook ] ) || ! is_a( $wp_filter[ $hook ], 'WP_Hook' ) ) {
                continue;
            }

            foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
                foreach ( $callbacks as $key => $callback_data ) {
                    $callback = $callback_data['function'];
                    $identifier = $this->get_callback_identifier( $callback );

                    $source = $pluginResolver->resolve( $identifier )
                            ?? $themeResolver->resolve( $identifier )
                            ?? null;

                    if ( $source && in_array( $source, $hidden_sources, true ) ) {
                        remove_action( $hook, $callback, $priority );
                    }
                }
            }
        }
    }

    /**
     * Convierte un callback en identificador legible
     */
    private function get_callback_identifier( $callback ): ?string {
        if ( is_string( $callback ) ) {
            return $callback;
        }

        if ( is_array( $callback ) ) {
            if ( is_object( $callback[0] ) ) {
                return get_class( $callback[0] ) . '->' . $callback[1];
            } elseif ( is_string( $callback[0] ) ) {
                return $callback[0] . '::' . $callback[1];
            }
        }

        if ( $callback instanceof Closure ) {
            return 'Closure';
        }

        return null;
    }
}
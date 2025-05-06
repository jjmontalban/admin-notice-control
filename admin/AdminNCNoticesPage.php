<?php

defined( 'ABSPATH' ) || exit;

class AdminNCNoticesPage {

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Access denied.', 'admin-notice-control' ) );
        }

        $scanner        = new AdminNCHookScanner();
        $pluginResolver = new AdminNCPluginResolver();
        $themeResolver  = new AdminNCThemeResolver();
        $storage        = new AdminNCStorage();
        $raw_callbacks = $scanner->get_registered_notice_callbacks();
        $groups = [];
        $storage_hidden = $storage->get_all();

        // Activos
        foreach ( $raw_callbacks as $cb ) {
            $source = $pluginResolver->resolve( $cb['callback'] )
                    ?? $themeResolver->resolve( $cb['callback'] )
                    ?? __( 'unknown', 'admin-notice-control' );

            if ( ! isset( $groups[ $source ] ) ) {
                $groups[ $source ] = [
                    'source'    => $source,
                    'count'     => 0,
                    'hidden'    => $storage->is_hidden( $source ),
                    'callbacks' => [],
                ];
            }

            $groups[ $source ]['count']++;
            $groups[ $source ]['callbacks'][] = $cb['callback'];
        }

        // Ocultos que no estén en activos
        foreach ( $storage_hidden as $hidden_source ) {
            if ( ! isset( $groups[ $hidden_source ] ) ) {
                $callbacks = $storage->get_callbacks_snapshot( $hidden_source );

                $groups[ $hidden_source ] = [
                    'source'    => $hidden_source,
                    'count'     => count( $callbacks ),
                    'hidden'    => true,
                    'callbacks' => $callbacks,
                ];
            }
        }

        $sources = array_values( $groups );

        include ADMINNC_PLUGIN_DIR . 'templates/settings-page.php';
    }

}

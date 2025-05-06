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

    public static function handle_form() {
        if (
            ! current_user_can( 'manage_options' ) ||
            ! check_admin_referer( 'adminnc_toggle_source' )
        ) {
            wp_die( esc_html__( 'Access denied.', 'admin-notice-control' ) );
        }
    
        $allowed_actions = [ 'hide', 'unhide' ];
    
        /* ── Sanitización masiva de todo el array $_POST ─────────── */
        $post = map_deep( wp_unslash( $_POST ), 'sanitize_text_field' );
    
        $source = $post['adminnc_source'] ?? '';
        $action = $post['adminnc_action'] ?? '';
    
        if ( ! in_array( $action, $allowed_actions, true ) ) {
            wp_die( esc_html__( 'Invalid action.', 'admin-notice-control' ) );
        }

        $scanner        = new AdminNCHookScanner();
        $pluginResolver = new AdminNCPluginResolver();
        $themeResolver  = new AdminNCThemeResolver();
        $storage        = new AdminNCStorage();

        $raw_callbacks = $scanner->get_registered_notice_callbacks();

        if ( $action === 'hide' ) {
            $callbacks_snapshot = [];

            foreach ( $raw_callbacks as $cb ) {
                $resolved_source = $pluginResolver->resolve( $cb['callback'] )
                                 ?? $themeResolver->resolve( $cb['callback'] )
                                 ?? __( 'unknown', 'admin-notice-control' );

                if ( trim($resolved_source) === trim($source) ) {
                    $callbacks_snapshot[] = $cb['callback'];
                }
            }

            $storage->save_callbacks_snapshot( $source, $callbacks_snapshot );
            $storage->hide( $source );
        } elseif ( $action === 'unhide' ) {
            $storage->unhide( $source );
            delete_option( "adminnc_callbacks_snapshot_{$source}" );
        }

        wp_safe_redirect( admin_url( 'options-general.php?page=admin-notice-control' ) );
        exit;
    }
}

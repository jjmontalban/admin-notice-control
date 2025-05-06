<?php

defined( 'ABSPATH' ) || exit;

class NoticesPage {

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Access denied.', 'admin-notice-control' ) );
        }

        $scanner        = new HookScanner();
        $pluginResolver = new PluginResolver();
        $themeResolver  = new ThemeResolver();
        $storage        = new Storage();
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

        include plugin_dir_path( __FILE__ ) . '../templates/settings-page.php';
    }

    public static function handle_form() {
        if (
            ! current_user_can( 'manage_options' ) ||
            ! check_admin_referer( 'adminnc_toggle_source' )
        ) {
            wp_die( esc_html__( 'Access denied.', 'admin-notice-control' ) );
        }
    
        $allowed_actions = [ 'hide', 'unhide' ];
    
        $source = sanitize_text_field( wp_unslash( $_POST['adminnc_source'] ?? '' ) );
        $action = sanitize_text_field( wp_unslash( $_POST['adminnc_action'] ?? '' ) );
    
        if ( ! in_array( $action, $allowed_actions, true ) ) {
            wp_die( esc_html__( 'Invalid action.', 'admin-notice-control' ) );
        }

        $scanner        = new HookScanner();
        $pluginResolver = new PluginResolver();
        $themeResolver  = new ThemeResolver();
        $storage        = new Storage();

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

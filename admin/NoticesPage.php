<?php

defined( 'ABSPATH' ) || exit;

class NoticesPage {

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Access denied.', 'admin-notice-control' ) );
        }

        if ( isset( $_POST['anc_source'], $_POST['anc_action'] ) ) {
            check_admin_referer( 'anc_toggle_source' );

            $storage = new Storage();
            $source  = sanitize_text_field( wp_unslash( $_POST['anc_source'] ) );
            $action  = sanitize_text_field( wp_unslash( $_POST['anc_action'] ) );

            if ( $action === 'hide' ) {
                $storage->hide( $source );
            } elseif ( $action === 'unhide' ) {
                $storage->unhide( $source );
            }

            wp_safe_redirect( admin_url( 'options-general.php?page=admin-notice-control' ) );
            exit;
        }

        $scanner        = new HookScanner();
        $pluginResolver = new PluginResolver();
        $themeResolver  = new ThemeResolver();
        $storage        = new Storage();

        $raw_callbacks = $scanner->get_registered_notice_callbacks();
        $groups = [];

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

        $sources = array_values( $groups );

        include plugin_dir_path( __FILE__ ) . '../templates/settings-page.php';
    }
}
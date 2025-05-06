<?php
/**
 * Plugin Name: Admin Notice Control
 * Plugin URI: https://wordpress.org/plugins/admin-notice-control/
 * Description: Hide, manage, and control admin notices in your WordPress dashboard. Disable annoying plugin and theme messages with a single click.
 * Version: 1.0.0
 * Author: jjmontalban
 * Author URI: https://jjmontalban.github.io
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: admin-notice-control
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'ADMINNC_PLUGIN_FILE', __FILE__ );
define( 'ADMINNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADMINNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register( function( $class ) {
    $class_file = str_replace( '\\', '/', $class ) . '.php';
    $class_file = str_replace( '_', '', $class_file );
    $paths = [ __DIR__ . '/admin/', __DIR__ . '/core/' ];

    foreach ( $paths as $path ) {
        $filepath = $path . $class_file;
        if ( file_exists( $filepath ) ) {
            require_once $filepath;
            return;
        }
    }
} );

add_action( 'plugins_loaded', function () {

    if ( is_admin() ) {
        ( new AdminNCAdminMenu() )->register();
        ( new AdminNCNoticeManager() )->disable_hidden_callbacks();
    }
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="options-general.php?page=admin-notice-control">' . esc_html__('Settings', 'admin-notice-control') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
});

add_action( 'admin_post_adminnc_save_all_sources', 'adminnc_handle_save_all_sources' );

function adminnc_handle_save_all_sources() {
	if (
		! current_user_can( 'manage_options' ) ||
		! check_admin_referer( 'adminnc_save_all_sources', 'adminnc_save_all_nonce' )
	) {
		wp_die( esc_html__( 'Unauthorized action.', 'admin-notice-control' ) );
	}

	$allowed_actions = [ 'hide', 'show' ];

	$scanner        = new AdminNCHookScanner();
	$pluginResolver = new AdminNCPluginResolver();
	$themeResolver  = new AdminNCThemeResolver();
	$storage        = new AdminNCStorage();
	$raw_callbacks  = $scanner->get_registered_notice_callbacks();

	if ( isset( $_POST['source_settings'] ) && is_array( $_POST['source_settings'] ) ) {

		$sanitized_settings = map_deep( wp_unslash( $_POST['source_settings'] ), 'sanitize_text_field' );
	
		foreach ( $sanitized_settings as $source => $action ) {
			if ( ! in_array( $action, $allowed_actions, true ) ) {
				continue;
			}

			if ( 'hide' === $action ) {
				$snapshot = [];

				foreach ( $raw_callbacks as $cb ) {
					$resolved = $pluginResolver->resolve( $cb['callback'] )
						?? $themeResolver->resolve( $cb['callback'] )
						?? __( 'unknown', 'admin-notice-control' );

					if ( trim( $resolved ) === trim( $source ) ) {
						$snapshot[] = $cb['callback'];
					}
				}

				$storage->save_callbacks_snapshot( $source, $snapshot );
				$storage->hide( $source );

			} elseif ( 'show' === $action ) {
				$storage->unhide( $source );
				delete_option( "adminnc_callbacks_snapshot_{$source}" );
			}
		}
	}

	wp_safe_redirect(
		admin_url( 'options-general.php?page=admin-notice-control&updated=1' )
	);
	exit;
}
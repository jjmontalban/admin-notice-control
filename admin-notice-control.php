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

// Carga automática de clases en /admin y /core
spl_autoload_register( function( $class ) {
    $class_file = str_replace( '\\', '/', $class ) . '.php';
    $class_file = str_replace( '_', '', $class_file ); // Convención: AdminMenu → AdminMenu.php
    $paths = [ __DIR__ . '/admin/', __DIR__ . '/core/' ];

    foreach ( $paths as $path ) {
        $filepath = $path . $class_file;
        if ( file_exists( $filepath ) ) {
            require_once $filepath;
            return;
        }
    }
} );

// Cargar traducciones
add_action( 'plugins_loaded', function () {
    load_plugin_textdomain( 'admin-notice-control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

// Inicializar menú de ajustes
add_action( 'plugins_loaded', function () {
    if ( is_admin() ) {
        $menu = new AdminMenu();
        $menu->register();
    }
} );

// Añadir enlace "Settings" en listado de plugins
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function ( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=admin-notice-control' ) ) . '">' . esc_html__( 'Settings', 'admin-notice-control' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
} );

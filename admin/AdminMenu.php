<?php

defined( 'ABSPATH' ) || exit;

class AdminMenu {

    /**
     * Registra la página de ajustes en el menú del admin
     */
    public function register() {
        add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
    }

    /**
     * Añade el submenú bajo "Ajustes"
     */
    public function add_menu_page() {
        add_options_page(
            __( 'Admin Notice Control', 'admin-notice-control' ),
            __( 'Admin Notice Control', 'admin-notice-control' ),
            'manage_options',
            'admin-notice-control',
            [ NoticesPage::class, 'render' ]
        );
    }
}
<?php
/**
 * Cleanup logic when the plugin is fully uninstalled.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'anc_hidden_notices' );
delete_option( 'anc_plugin_version' );

<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'adminnc_hidden_sources' );
delete_option( 'adminnc_plugin_version' );

global $wpdb;
$pattern      = $wpdb->esc_like( 'adminnc_callbacks_snapshot_' ) . '%';
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$pattern
	)
);

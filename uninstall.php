<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'anc_hidden_sources' );
delete_option( 'anc_plugin_version' );

global $wpdb;
$pattern      = $wpdb->esc_like( 'anc_callbacks_snapshot_' ) . '%';
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$pattern
	)
);

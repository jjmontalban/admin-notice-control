<?php

defined( 'ABSPATH' ) || exit;

class AdminNCHookScanner {

    /**
     * Devuelve una lista de callbacks registrados en hooks de avisos
     *
     * @return array
     */
    public function get_registered_notice_callbacks(): array {
        global $wp_filter;

        $hooks_to_check = [
            'admin_notices',
            'all_admin_notices'
        ];

        $results = [];

        foreach ( $hooks_to_check as $hook ) {
            if ( ! isset( $wp_filter[ $hook ] ) || ! is_a( $wp_filter[ $hook ], 'WP_Hook' ) ) {
                continue;
            }

            foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
                foreach ( $callbacks as $callback_data ) {
                    $callback = $callback_data['function'];

                    $identifier = $this->get_callback_identifier( $callback );

                    if ( $identifier ) {
                        $results[] = [
                            'hook'     => $hook,
                            'priority' => $priority,
                            'callback' => $identifier,
                        ];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Genera un identificador legible del callback
     *
     * @param mixed $callback
     * @return string|null
     */
    private function get_callback_identifier( $callback ): ?string {
        if ( is_string( $callback ) ) {
            return $callback;
        }

        if ( is_array( $callback ) ) {
            if ( is_object( $callback[0] ) ) {
                return get_class( $callback[0] ) . '->' . $callback[1];
            } elseif ( is_string( $callback[0] ) ) {
                return $callback[0] . '::' . $callback[1];
            }
        }

        if ( $callback instanceof Closure ) {
            return 'Closure';
        }

        return null;
    }
}
<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Admin Notice Control', 'admin-notice-control' ); ?></h1>

    <p><?php esc_html_e( 'Manage which plugins or themes can display admin notices.', 'admin-notice-control' ); ?></p>

    <?php if ( ! empty( $sources ) ) : ?>
        <table class="widefat fixed striped" id="anc-notice-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Plugin / Theme', 'admin-notice-control' ); ?></th>
                    <th><?php esc_html_e( 'Notices Detected', 'admin-notice-control' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'admin-notice-control' ); ?></th>
                    <th><?php esc_html_e( 'Details', 'admin-notice-control' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $sources as $group ) : ?>
                    <tr>
                        <td><?php echo esc_html( $group['source'] ); ?></td>
                        <td><?php echo esc_html( $group['count'] ); ?></td>
                        <td>
                            <form method="post">
                                <?php wp_nonce_field( 'anc_toggle_source' ); ?>
                                <input type="hidden" name="anc_source" value="<?php echo esc_attr( $group['source'] ); ?>">
                                <input type="hidden" name="anc_action" value="<?php echo $group['hidden'] ? 'unhide' : 'hide'; ?>">
                                <?php
                                submit_button(
                                    $group['hidden'] ? __( 'Show Notices', 'admin-notice-control' ) : __( 'Hide Notices', 'admin-notice-control' ),
                                    'secondary',
                                    '',
                                    false
                                );
                                ?>
                            </form>
                        </td>
                        <td>
                            <details>
                                <summary><?php esc_html_e( 'View Callbacks', 'admin-notice-control' ); ?></summary>
                                <ul style="margin-top: 0.5em;">
                                    <?php foreach ( $group['callbacks'] as $cb ) : ?>
                                        <li><code><?php echo esc_html( $cb ); ?></code></li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e( 'No admin notices detected.', 'admin-notice-control' ); ?></p>
    <?php endif; ?>
</div>
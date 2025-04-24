<?php defined( 'ABSPATH' ) || exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Admin Notice Control', 'admin-notice-control' ); ?></h1>
    <p><?php esc_html_e( 'Manage which plugins or themes can display admin notices.', 'admin-notice-control' ); ?></p>

    <?php if ( ! empty( $sources ) ) : ?>
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="action" value="anc_save_all_sources">
            <?php wp_nonce_field( 'anc_save_all_sources', 'anc_save_all_nonce' ); ?>

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Source', 'admin-notice-control' ); ?></th>
                        <th><?php esc_html_e( 'Notices Detected', 'admin-notice-control' ); ?></th>
                        <th><?php esc_html_e( 'Details', 'admin-notice-control' ); ?></th>
                        <th><?php esc_html_e( 'Action', 'admin-notice-control' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $sources as $group ) : ?>
                        <tr>
                            <td><?php echo esc_html( $group['source'] ); ?></td>
                            <td><?php echo esc_html( $group['count'] ); ?></td>
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
                            <td>
                                <label style="margin-right: 1rem;">
                                    <input type="radio" name="source_settings[<?php echo esc_attr( $group['source'] ); ?>]" value="show"
                                           <?php checked( ! $group['hidden'] ); ?> />
                                    <span><?php esc_html_e( 'Show', 'admin-notice-control' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="source_settings[<?php echo esc_attr( $group['source'] ); ?>]" value="hide"
                                           <?php checked( $group['hidden'] ); ?> />
                                    <span><?php esc_html_e( 'Hide', 'admin-notice-control' ); ?></span>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top: 2rem;">
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'admin-notice-control' ); ?></button>
            </p>
        </form>
    <?php else : ?>
        <p><?php esc_html_e( 'No admin notices detected.', 'admin-notice-control' ); ?></p>
    <?php endif; ?>
</div>

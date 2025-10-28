<?php

namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register plugin settings.
 */
function register_settings() {
    register_setting(
        'js_libs_manager_options',
        'js_libs_manager_enabled_libs',
        [
            'type'              => 'array',
            'sanitize_callback' => __NAMESPACE__ . '\\sanitize_enabled_libs',
            'default'           => [],
        ]
    );

    // Font Awesome kit URL (user-provided kit script or URL)
    register_setting(
        'js_libs_manager_options',
        'js_libs_manager_fontawesome_kit',
        [
            'type'              => 'string',
            'sanitize_callback' => __NAMESPACE__ . '\\sanitize_fontawesome_kit',
            'default'           => '',
        ]
    );
}
add_action( 'admin_init', __NAMESPACE__ . '\\register_settings' );

/**
 * Sanitize enabled libraries input.
 *
 * @param array $input Raw input from checkbox array.
 * @return array Sanitized array of library keys.
 */
function sanitize_enabled_libs( $input ) {
    $libraries   = get_registered_libraries(); // From config.php
    $sanitized   = [];
    $allowed_keys = array_keys( $libraries );

    if ( ! is_array( $input ) ) {
        return $sanitized;
    }

    foreach ( $input as $lib ) {
        $lib = sanitize_key( $lib );
        if ( in_array( $lib, $allowed_keys, true ) ) {
            $sanitized[] = $lib;
        }
    }

    return array_unique( $sanitized );
}


/**
 * Sanitize Font Awesome kit input.
 *
 * Accepts either a full <script> tag or a raw URL. Returns an empty string when
 * the input is invalid or not a Font Awesome kit URL.
 *
 * @param string $input
 * @return string
 */
function sanitize_fontawesome_kit( $input ) {
    if ( empty( $input ) ) {
        return '';
    }

    $input = trim( (string) $input );

    // If the user pasted an entire <script ...> tag, extract the src attribute.
    if ( strpos( $input, '<script' ) !== false ) {
        if ( preg_match( '/src\s*=\s*["\']([^"\']+)["\']/', $input, $m ) ) {
            $url = $m[1];
        } else {
            return '';
        }
    } else {
        $url = $input;
    }

    // Normalize and validate URL
    $url = esc_url_raw( $url );
    if ( empty( $url ) ) {
        return '';
    }

    // Only allow official Font Awesome kit domains to reduce abuse risk.
    $host = parse_url( $url, PHP_URL_HOST );
    $allowed_hosts = array( 'kit.fontawesome.com', 'kit-pro.fontawesome.com' );
    if ( ! in_array( $host, $allowed_hosts, true ) ) {
        return '';
    }

    return $url;
}

/**
 * Add settings page to WordPress admin menu.
 */
function admin_menu() {
    add_options_page(
        __( 'JS Libraries Manager', 'js-libs-manager' ),
        __( 'JS Libraries', 'js-libs-manager' ),
        'manage_options',
        'js-libs-manager',
        __NAMESPACE__ . '\\render_settings_page'
    );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu' );

/**
 * Render the settings page.
 */
function render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'js-libs-manager' ) );
    }

    $libraries     = get_registered_libraries();
    $enabled_libs  = get_option( 'js_libs_manager_enabled_libs', [] );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'js_libs_manager_options' );
            do_settings_sections( 'js_libs_manager_options' );
            ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="js_libs_manager_enabled_libs">
                            <?php esc_html_e( 'Enabled Libraries', 'js-libs-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <?php esc_html_e( 'Select libraries to enqueue on frontend', 'js-libs-manager' ); ?>
                            </legend>

                            <?php foreach ( $libraries as $key => $lib ) : ?>
                                <?php
                                $checked = in_array( $key, $enabled_libs, true ) ? checked( true, true, false ) : '';
                                ?>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="js_libs_manager_enabled_libs[]"
                                        value="<?php echo esc_attr( $key ); ?>"
                                        <?php echo $checked; ?>
                                    >
                                    <?php echo esc_html( $lib['label'] ); ?>
                                </label>
                                <br>
                            <?php endforeach; ?>

                            <h3 style="margin-top:1em"><?php esc_html_e( 'Font Awesome Kit', 'js-libs-manager' ); ?></h3>
                            <p>
                                <label for="js_libs_manager_fontawesome_kit">
                                    <?php esc_html_e( 'Paste your Font Awesome kit script tag or kit URL here', 'js-libs-manager' ); ?>
                                </label>
                            </p>
                            <input
                                type="text"
                                id="js_libs_manager_fontawesome_kit"
                                name="js_libs_manager_fontawesome_kit"
                                value="<?php echo esc_attr( get_option( 'js_libs_manager_fontawesome_kit', '' ) ); ?>"
                                class="regular-text"
                            />
                            <p class="description">
                                <?php esc_html_e( 'Optional: paste your Font Awesome kit script tag (or the .js URL). This will be enqueued in the <head> when Font Awesome is enabled.', 'js-libs-manager' ); ?>
                            </p>

                            <p class="description">
                                <?php esc_html_e( 'Select the JavaScript libraries you want to enqueue on the frontend.', 'js-libs-manager' ); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register settings
function js_libs_manager_register_settings() {
    register_setting('js_libs_manager_options', 'js_libs_manager_enabled_libs', array(
        'type' => 'array',
        'sanitize_callback' => 'js_libs_manager_sanitize_enabled_libs',
        'default' => array()
    ));
}
add_action('admin_init', 'js_libs_manager_register_settings');

// Sanitize enabled libraries
function js_libs_manager_sanitize_enabled_libs($input) {
    global $js_libs_manager_libraries;
    $sanitized = array();
    $allowed_libs = array_keys($js_libs_manager_libraries);
    foreach ($input as $lib) {
        if (in_array($lib, $allowed_libs)) {
            $sanitized[] = $lib;
        }
    }
    return $sanitized;
}

// Add admin menu
function js_libs_manager_admin_menu() {
    add_options_page(
        'JS Libraries Manager',
        'JS Libraries',
        'manage_options',
        'js-libs-manager',
        'js_libs_manager_settings_page'
    );
}
add_action('admin_menu', 'js_libs_manager_admin_menu');

// Settings page callback
function js_libs_manager_settings_page() {
    global $js_libs_manager_libraries;
    ?>
    <div class="wrap">
        <h1>JS Libraries Manager</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('js_libs_manager_options');
            do_settings_sections('js_libs_manager_options');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enabled Libraries</th>
                    <td>
                        <?php
                        $enabled_libs = get_option('js_libs_manager_enabled_libs', array());
                        foreach ($js_libs_manager_libraries as $key => $lib) {
                            $checked = in_array($key, $enabled_libs) ? 'checked="checked"' : '';
                            echo '<label><input type="checkbox" name="js_libs_manager_enabled_libs[]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($lib['label']) . '</label><br>';
                        }
                        ?>
                        <p class="description">Select the JavaScript libraries you want to enqueue on the frontend.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
<?php
/**
 * Plugin Name: Alert Text
 * Plugin URI:  https://github.com/smoothdesigns/alert-text
 * Description: A simple "Hello World" plugin that displays an alert on the front end.
 * Version:     1.0.0
 * Requires at least: 5.3
 * Tested up to:      6.8.2
 * Requires PHP:      7.2
 * Author:      Thomas Mirmo
 * Author URI:  https://github.com/smoothdesigns/
 * License:     GPL-2.0+
 * Text Domain: alert-text
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add a filter to modify the plugins update transient.
add_filter('pre_set_site_transient_update_plugins', 'alert_text_check_for_updates');

// Add a filter to handle the plugin information display.
add_filter('plugins_api', 'alert_text_plugin_info', 10, 3);

// Add a filter to add the "View details" link to the plugin row.
add_filter('plugin_row_meta', 'alert_text_add_plugin_row_meta', 10, 2);

/**
 * Checks for updates by fetching the `readme.txt` file from the GitHub repository.
 *
 * @param object $transient The plugins update transient.
 * @return object The modified transient object.
 */
function alert_text_check_for_updates($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_info = get_plugin_data(__FILE__);
    $current_version = $plugin_info['Version'];

    $info = alert_text_get_remote_info();

    if ($info && version_compare($current_version, $info->version, '<')) {
        $transient->response[plugin_basename(__FILE__)] = $info;
    }

    return $transient;
}

/**
 * Provides detailed plugin information for the update screen.
 *
 * @param false|object|array $result The result object or false.
 * @param string $action The API action.
 * @param object $args The API arguments.
 * @return false|object|array The result object or false.
 */
function alert_text_plugin_info($result, $action, $args) {
    if ($action === 'plugin_information' && isset($args->slug) && $args->slug === 'alert-text') {
        $info = alert_text_get_remote_info();
        if ($info) {
            return $info;
        }
    }
    return $result;
}

/**
 * Fetches plugin information from the GitHub repository's `readme.txt`.
 *
 * @return object|false The plugin info object, or false on failure.
 */
function alert_text_get_remote_info() {
    $readmeUrl = 'https://raw.githubusercontent.com/smoothdesigns/alert-text/main/readme.txt';
    $response = wp_remote_get($readmeUrl);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return false;
    }

    $contents = wp_remote_retrieve_body($response);

    // Parse the readme contents to get plugin info and sections.
    preg_match('/^Stable tag:\s*(\S+)/im', $contents, $matches);
    $version = isset($matches[1]) ? $matches[1] : '';

    $sections = [];
    preg_match_all('/==\s*([^=]+?)\s*==\s*(.*?)(\n\n|$)/s', $contents, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $title = trim($match[1]);
        $content = trim($match[2]);
        $sections[strtolower(str_replace(' ', '_', $title))] = $content;
    }

    $info = (object) [
        'slug' => 'alert-text',
        'plugin_name' => 'Alert Text',
        'name' => 'Alert Text',
        'version' => $version,
        'author' => 'Thomas Mirmo',
        'author_profile' => 'https://github.com/smoothdesigns/',
        'last_updated' => gmdate('Y-m-d H:i:s'),
        'homepage' => 'https://github.com/smoothdesigns/alert-text',
        'requires' => '5.0',
        'tested' => '6.8',
        'sections' => (object) $sections,
        'download_link' => 'https://github.com/smoothdesigns/alert-text/archive/main.zip',
        'trunk' => 'https://github.com/smoothdesigns/alert-text/trunk',
    ];

    return $info;
}

/**
 * Adds a "View details" link to the plugin's row on the plugins page.
 *
 * @param array  $links       The array of plugin row links.
 * @param string $plugin_file The plugin file name.
 * @return array The modified array of links.
 */
function alert_text_add_plugin_row_meta($links, $plugin_file) {
    if (plugin_basename(__FILE__) === $plugin_file) {
        $links[] = '<a href="' . network_admin_url('plugin-install.php?tab=plugin-information&plugin=alert-text&TB_iframe=true&width=600&height=550') . '" class="thickbox open-plugin-details-modal">View details</a>';
    }
    return $links;
}


// This is the function that will display the alert box.
function alert_text_javascript() {
    echo '
        <script>
            alert("Hello World");
        </script>
    ';
}
add_action('wp_footer', 'alert_text_javascript');

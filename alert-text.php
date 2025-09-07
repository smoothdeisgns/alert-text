<?php
/**
 * Plugin Name: Alert Text
 * Plugin URI:  https://github.com/smoothdesigns/alert-text
 * Description: A simple "Hello World" plugin that displays an alert on the front end.
 * Version:     1.0.0
 * Author:      Thomas Mirmo
 * Author URI:  https://labs.athleticsja.org
 * License:     GPL-2.0+
 * Text Domain: alert-text
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue the "Hello World" script.
function alert_text_enqueue_script() {
    wp_enqueue_script(
        'alert-text-script',
        plugins_url('alert-text.js', __FILE__),
        [],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'alert_text_enqueue_script');


// Simple JavaScript file for the alert.
function alert_text_javascript() {
    echo '
        <script>
            alert("Hello World");
        </script>
    ';
}
add_action('wp_footer', 'alert_text_javascript');


// --- GitHub Plugin Update Checker ---
require 'update-checker.php';

// Instantiate the update checker.
// REMEMBER: You must change the repository URI to your own GitHub repository.
$myUpdateChecker = new Puc_v4_Plugin_UpdateChecker(
    'https://github.com/your-username/alert-text-plugin/',
    __FILE__,
    'alert-text'
);

// Optional: Set a specific branch or tag.
// $myUpdateChecker->setBranch('main');
// $myUpdateChecker->setAuthentication('<your-personal-access-token>');

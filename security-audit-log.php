<?php
/*
 * Plugin Name: Security Audit Log
 * Description: Simple plugin to log user activities like login in/out, post/page edit etc.
 * Version: 1.0
 * Author: M. Misiak
 */

// Prevent direct access to the script
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Implements a simple autoloader to load plugin classes.
 *
 * @param string $class Class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/includes/';

    // Convert namespace to file path
    $class = str_replace('SecurityAuditLog\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = $baseDir . "class-" . $class . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize plugin components

/**
 * Initializes the admin menu page.
 */
$admin_menu = new SecurityAuditLog\Admin_Menu();
add_action('admin_menu', [$admin_menu, 'sal_add_admin_menu_page']);

/**
 * Initializes the database handler.
 */
$database = new SecurityAuditLog\Database();

/**
 * Initializes the activities logger.
 */
$activities = new SecurityAuditLog\Activities();

// Register actions with WordPress hooks

/**
 * Logs user login actions.
 */
add_action('wp_login', [$activities, 'sal_log_user_login'], 10, 2);

/**
 * Logs user logout actions.
 */
add_action('wp_logout', [$activities, 'sal_log_user_logout'], 10);

/**
 * Logs post or page edits.
 */
add_action('post_updated', [$activities, 'sal_log_post_edit'], 10, 3);
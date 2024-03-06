<?php

namespace SecurityAuditLog;

/**
 * Manages the admin menu for the Security Audit Log plugin.
 *
 * This class is responsible for adding a new menu item to the WordPress admin dashboard,
 * and rendering the content for the plugin's admin page.
 */
class Admin_Menu
{
    /**
     * Adds a plugin menu page to the WordPress admin dashboard.
     *
     * Registers a new menu page. This page will display the security audit log
     * where administrators can view logged activities.
     *
     * @return void
     */
    public function sal_add_admin_menu_page()
    {
        add_menu_page(
            'Security Audit Log', // Page title
            'SAL', // Menu title
            'manage_options', // Capability
            'security-audit-log', // Menu slug
            [$this, 'sal_display_log_page'], // Function to display the page
            'dashicons-welcome-view-site', // Icon URL
            6 // Position
        );
    }

    /**
     * Renders the content for the plugin's admin menu page.
     *
     * Displays the log table where administrators can view logged activities such as user logins,
     * logouts, and post edits. Utilizes the `Logs_Table` class to prepare and display the table.
     *
     * @return void
     */
    public function sal_display_log_page()
    {
        $log_table = new Logs_Table();
        $log_table->prepare_items();
        ?>
        <div class="wrap">
            <h2>Activity logs</h2>
            <form method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>"/>
                <?php
                $log_table->display();
                ?>
            </form>
        </div>
        <?php
    }
}
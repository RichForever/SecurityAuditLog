<?php

namespace SecurityAuditLog;

/**
 * Manages database interactions for the Security Audit Log plugin.
 *
 * This class handles the creation of a custom table within the WordPress database
 * for storing security audit logs. It also includes methods for retrieving log entries,
 * supporting pagination, sorting, and searching functionalities.
 */
class Database
{
    /**
     * The name of the custom table for storing security audit logs.
     *
     * @var string
     */
    private static string $table_name;

    /**
     * Initializes the database table for audit logs.
     *
     * Checks if the plugin's admin page is accessed and initiates the creation
     * of the custom table if it does not exist.
     */
    public function __construct()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . "security_audit_log";

        /**
         * Create table in database when user open plugin page
         */
        if (isset($_GET['page']) && $_GET['page'] == 'security-audit-log') {
            $this->sal_create_custom_table();
        }
    }

    /**
     * Creates a new custom table in the database for storing audit logs.
     *
     * This method checks if the table already exists before attempting to create it.
     * It defines the structure for the table, including columns for ID, time,
     * user ID, and the activity text.
     *
     * @return void
     */
    public function sal_create_custom_table()
    {
        global $wpdb;

        if ($wpdb->get_var("SHOW TABLES LIKE '" . self::$table_name . "'") != self::$table_name) {
            $sql = "CREATE TABLE " . self::$table_name . " (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                user_id mediumint(9) NOT NULL,
                activity text NOT NULL,
                PRIMARY KEY  (id)
        );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Retrieves audit logs from the database with pagination, sorting, and search functionality.
     *
     * @param string $order_by The column to sort the results by.
     * @param string $order The direction of the sort (ASC or DESC).
     * @param string $search_query A search query to filter results.
     * @param int $current_page The current page number for pagination.
     * @param int $per_page The number of items to display per page.
     * @return array An array of stdClass objects containing the log entries.
     */
    public static function sal_get_logs($order_by, $order, $search_query, $current_page, $per_page)
    {
        global $wpdb;

        $query = "SELECT * FROM " . self::$table_name;
        $whereConditions = [];
        $queryParams = [];

        // search
        if (!empty($search_query)) {
            $like_query = '%' . $wpdb->esc_like($search_query) . '%';
            $whereConditions[] = "(id LIKE %s OR time LIKE %s OR user_id LIKE %s OR activity LIKE %s)";
            array_push($queryParams, $like_query, $like_query, $like_query, $like_query);
        }

        if (!empty($whereConditions)) {
            $query .= ' WHERE ' . join(' AND ', $whereConditions);
        }

        $query .= " ORDER BY $order_by $order";

        // Add pagination
        $offset = ($current_page - 1) * $per_page;
        $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);

        if (!empty($queryParams)) {
            $query = $wpdb->prepare($query, $queryParams);
        }

        return $wpdb->get_results($query);
    }

    /**
     * Retrieves the total number of audit log entries that match a search query.
     *
     * This is used for pagination to calculate the total number of pages.
     *
     * @param string $search_query A search query to filter results.
     * @return int The total number of log entries that match the search criteria.
     */
    public static function sal_get_total_logs($search_query)
    {
        global $wpdb;


        $query = "SELECT COUNT(*) FROM " . self::$table_name;
        $whereConditions = [];
        $queryParams = [];

        // search
        if (!empty($search_query)) {
            $like_query = '%' . $wpdb->esc_like($search_query) . '%';
            $whereConditions[] = "(id LIKE %s OR time LIKE %s OR user_id LIKE %s OR activity LIKE %s)";
            array_push($queryParams, $like_query, $like_query, $like_query, $like_query);
        }

        if (!empty($whereConditions)) {
            $query .= ' WHERE ' . join(' AND ', $whereConditions);
        }

        if (!empty($queryParams)) {
            $query = $wpdb->prepare($query, $queryParams);
        }

        return $wpdb->get_var($query);
    }
}
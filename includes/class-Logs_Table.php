<?php

namespace SecurityAuditLog;

use WP_List_Table;

/**
 * Custom table class for displaying the security audit logs in the WordPress admin.
 *
 * Extends the WP_List_Table class to leverage WordPress' table display capabilities.
 * This class defines the columns to be displayed, sortable columns, and how to render each row.
 */

class Logs_Table extends WP_List_Table
{
    /**
     * Initializes the table with custom settings.
     */
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Log', 'SecurityAuditLog'),
            'plural' => __('Logs', 'SecurityAuditLog'),
            'ajax' => false
        ]);
    }

    /**
     * Defines the columns that are going to be used in the table.
     *
     * @return array An associative array containing column identifiers and display names.
     */
    public function get_columns()
    {
        $columns = [
            'id' => __('ID', 'SecurityAuditLog'),
            'time' => __('Time', 'SecurityAuditLog'),
            'user_id' => __('User ID', 'SecurityAuditLog'),
            'activity' => __('Activity', 'SecurityAuditLog')
        ];
        return $columns;
    }

    /**
     * Specifies the columns that can be used to sort the table data.
     *
     * @return array An associative array of sortable columns.
     */
    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id', false),
            'user_id' => array('user_id', false),
            'time' => array('time', false)

        );
        return $sortable_columns;
    }

    /**
     * Prepares the items for displaying.
     *
     * Fetches data, sets up columns, and pagination.
     */
    public function prepare_items()
    {
        $database = new Database;
        $columns = $this->get_columns();
        $hidden = []; // Columns to hide, if any
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
        $order_by = isset($_REQUEST['orderby']) && array_key_exists($_REQUEST['orderby'], $sortable) ? $_REQUEST['orderby'] : 'id';
        $order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], ['asc', 'desc']) ? $_REQUEST['order'] : 'asc';
        $search_query = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';

        $per_page = isset($_REQUEST['per_page']) && intval($_REQUEST['per_page']) > 0 ? intval($_REQUEST['per_page']) : 10;
        $current_page = $this->get_pagenum();
        $total_records = Database::sal_get_total_logs($search_query);

        $this->items = Database::sal_get_logs($order_by, $order, $search_query, $current_page, $per_page);

        $this->set_pagination_args([
            'total_items' => $total_records,
            'per_page' => $per_page,
            'total_pages' => ceil($total_records / $per_page)
        ]);
    }

    /**
     * Default column rendering method, used for any column not explicitly defined.
     *
     * @param object $item A row's data.
     * @param string $column_name The name of the column to render.
     * @return mixed The rendered value for the given column.
     */
    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'time':
            case 'user_id':
            case 'activity':
                return $item->$column_name;
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Renders extra table navigation HTML content at the top or bottom of the table.
     *
     * @param string $which Specifies the location ('top' or 'bottom') for the extra content.
     */
    public function extra_tablenav($which)
    {
        if ($which == "top") {
            ?>
            <div class="alignleft actions">
                <input type="search" id="search-input" name="s"
                       value="<?php _e(esc_attr($this->get_search_query())); ?>"/>
                <?php submit_button(__('Search'), 'button', false, false, ['id' => 'search-submit']); ?>
            </div>
            <?php
        }

        if ($which == "bottom") {
            ?>
            <div class="alignleft actions" style="display: flex; align-items: center; gap: 8px;">
                <select name="per_page" id="per_page" class="postform" style="margin-right: 0;">
                    <?php
                    $per_page_options = [10, 20, 50, 100]; // Define your options here
                    foreach ($per_page_options as $option) {
                        echo '<option value="' . $option . '"' . (isset($_REQUEST['per_page']) && $_REQUEST['per_page'] == $option ? ' selected="selected"' : '') . '>' . $option . '</option>';
                    }
                    ?>
                </select>
                <label for="per_page"><?php _e('Records per page', 'SecurityAuditLog'); ?></label>
                <?php submit_button(__('Apply'), 'action', 'set_per_page', false); ?>
            </div>
            <?php
        }
    }
}
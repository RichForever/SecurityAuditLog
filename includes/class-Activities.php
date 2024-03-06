<?php

namespace SecurityAuditLog;

/**
 * Handles logging of various user activities, such as login, logout, and post edits.
 */
class Activities
{

    /**
     * Logs when a user logs in.
     *
     * @param string $user_login The login name of the user.
     * @param WP_User $user WP_User object of the logged-in user.
     * @return void
     */
    public function sal_log_user_login($user_login, $user)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'security_audit_log';
        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'user_id' => $user->ID,
                'activity' => 'User Logged In'
            )
        );

    }

    /**
     * Logs when a user logs out.
     *
     * @return void
     */
    public function sal_log_user_logout()
    {
        global $wpdb;
        $current_user = wp_get_current_user();
        $table_name = $wpdb->prefix . 'security_audit_log';
        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'user_id' => $current_user->ID,
                'activity' => 'User Logged Out'
            )
        );
    }

    /**
     * Logs when a post is edited by a user.
     *
     * @param int $post_ID The ID of the post being edited.
     * @param WP_Post $post_after Post object after the edit.
     * @param WP_Post $post_before Post object before the edit.
     * @return void
     */
    public function sal_log_post_edit($post_ID, $post_after, $post_before)
    {
        global $wpdb;

        if ($post_before->post_status === 'publish' && $post_after->post_status === 'publish') {
            $table_name = $wpdb->prefix . 'security_audit_log';
            $current_user = wp_get_current_user();
            $wpdb->insert(
                $table_name,
                array(
                    'time' => current_time('mysql'),
                    'user_id' => $current_user->ID,
                    'activity' => 'Edited Post - ' . $post_ID
                )
            );
        }

    }
}
<?php
/**
 * Reservations List Table Class.
 *
 * This class extends from the WP_List_Table WP core class to display reservations from the DB in a custom dashboard.
 *
 * @since 0.0.1
 * 
 * NOTE: The code in this class is still pending optimization. This is the first version to mainly test functionality.
 * Source: https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
 */

if (! class_exists('WP_List_Table') ) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('Reservations_List_Table') ) {
    class Reservations_List_Table extends WP_List_Table
    {
    
        /**
         * Class constructor 
         */
        public function __construct()
        {
    
            parent::__construct(
                [
                'singular' => __('Reservation', 'eventspractice'), //singular name of the listed records
                'plural'   => __('Reservations', 'eventspractice'), //plural name of the listed records
                'ajax'     => false //does this table support ajax?
                ] 
            );
    
        }
    
    
        /**
         * Retrieve reservations data from the database
         *
         * @param integer $per_page Number of items per page.
         * @param integer $page_number Page number.
         *
         * @return mixed
         */
        public static function get_reservations( $per_page = 5, $page_number = 1 )
        {
    
            global $wpdb;

            $sql = "SELECT {$wpdb->prefix}eventspractice_reservations.id as 'id', user_nicename as 'name', user_email, post_title as 'event', event_id 
            FROM {$wpdb->prefix}eventspractice_reservations, {$wpdb->prefix}posts, {$wpdb->prefix}users
            WHERE {$wpdb->prefix}eventspractice_reservations.event_id = {$wpdb->prefix}posts.ID
            AND {$wpdb->prefix}eventspractice_reservations.user_id = {$wpdb->prefix}users.ID";
    
            if (! empty($_REQUEST['orderby']) ) {
                $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
                $sql .= ! empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
            }
    
            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
    
    
            $result = $wpdb->get_results($sql, 'ARRAY_A');
    
            return $result;
        }
    
    
        /**
         * Delete a reservation record from the DB.
         *
         * @param int $id The reservation ID.
         */
        public static function delete_reservation( $id )
        {
            global $wpdb;
    
            $wpdb->delete(
                "{$wpdb->prefix}eventspractice_reservations",
                [ 'id' => $id ],
                [ '%d' ]
            );
        }
    
    
        /**
         * Return the count of records in the database.
         *
         * @return null|string
         */
        public static function record_count()
        {
            global $wpdb;
    
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}eventspractice_reservations";
    
            return $wpdb->get_var($sql);
        }
    
    
        /**
         * Text displayed when no reservation data is available 
         */
        public function no_items()
        {
            _e('No reservations avaliable.', 'eventspractice');
        }
    
    
        /**
         * Render a column when no column specific method exist.
         *
         * @param array  $item  An array of DB data.
         * @param string $column_name The column name.
         *
         * @return mixed
         */
        public function column_default( $item, $column_name )
        {
            switch ( $column_name ) {
            case 'event':
                return '<a title="View Event" href="'. get_the_permalink($item[ 'event_id' ]).'"><span class="dashicons dashicons-welcome-view-site"></span> '.$item[ $column_name ].'</a>';
            case 'user_email': 
                return '<a title="Send Email" href="mailto:'.$item[ $column_name ].'"><span class="dashicons dashicons-email-alt"></span> '.$item[ $column_name ].'</a>';
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }
    
        /**
         * Render the bulk edit checkbox
         *
         * @param array $item An array of DB data.
         *
         * @return string The checkbox HTML.
         */
        function column_cb( $item )
        {
            return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
            );
        }
    
    
        /**
         * Method for name column
         *
         * @param array $item An array of DB data.
         *
         * @return string The first column with actions listed.
         */
        function column_name( $item )
        {
    
            $delete_nonce = wp_create_nonce('sp_delete_reservation');
    
            $title = '<strong>' . $item['name'] . '</strong>';
    
            $actions = [
            'delete' => sprintf('<a title="Delete reservation" href="?post_type=%s&page=%s&action=%s&reservation_id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['post_type']), esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce)
    
            ];
    
            return $title . $this->row_actions($actions);
        }
    
    
        /**
         *  Associative array of columns
         *
         * @return array
         */
        function get_columns()
        {
            $columns = [
            'cb'            => '<input type="checkbox" />',
            'name'            => __('Name', 'eventspractice'),
            'user_email'    => __('Email', 'eventspractice'),
            'event'         => __('Event', 'eventspractice')
            ];
    
            return $columns;
        }
    
    
        /**
         * Columns to make sortable.
         *
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
            'name' => array( 'name', true ),
            'user_email' => array( 'user_email', true ),
            'event' => array( 'event', true )
            );
    
            return $sortable_columns;
        }
    
        /**
         * Returns an associative array containing the bulk action
         *
         * @return array
         */
        public function get_bulk_actions()
        {
            $actions = [
            'bulk-delete' => 'Delete'
            ];
    
            return $actions;
        }
    
    
        /**
         * Handles data query and filter, sorting, and pagination.
         */
        public function prepare_items()
        {
    
            $this->_column_headers = $this->get_column_info();
    
    
            // Process bulk action 
            $this->process_bulk_action();
    
            $per_page     = $this->get_items_per_page('reservations_per_page', 5);
            $current_page = $this->get_pagenum();
            $total_items  = self::record_count();
    
            $this->set_pagination_args(
                [
                'total_items' => $total_items, //Calculate the total number of items
                'per_page'    => $per_page //Determine how many items to show on a page
                ] 
            );
    
            $this->items = self::get_reservations($per_page, $current_page);
        }

        /**
         * Handles bulk actions. 
         */
        public function process_bulk_action()
        {
    
            //Detect when a bulk action is being triggered
            if ('delete' === $this->current_action() ) {
    
                // In our file that handles the request, verify the nonce.
                $nonce = esc_attr($_REQUEST['_wpnonce']);
    
                if (! wp_verify_nonce($nonce, 'sp_delete_reservation') ) {
                    die('Bye bye');
                }
                else {
                    self::delete_reservation(absint($_GET['reservation_id']));
                    wp_redirect('edit.php?post_type=event&page=reservations'); // Set to the Reservations page
                    exit;
                }
    
            }
    
            // If the delete bulk action is triggered
            if (( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' )
                || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' )
            ) {
    
                $delete_ids = esc_sql($_POST['bulk-delete']);
    
                // loop over the array of record IDs and delete them
                foreach ( $delete_ids as $id ) {
                    self::delete_reservation($id);
    
                }
    
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                    // add_query_arg() return the current url
                    wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }
        }
    
    }
    
}


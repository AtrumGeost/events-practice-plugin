<?php
/**
 * Reservations Class.
 *
 * This class creates a form for reservations and saves the submitted informtion into the database.
 *
 * @since 0.0.1
 * 
 * NOTE: The code in this class is still pending optimization. This is the first version to mainly test functionality.
 */
if (!class_exists('Events_Practice_Reservation') ) {
    class Events_Practice_Reservation
    {
        /**
         * Construct function that calls action and filter hooks when the class is initialized.
         */
        public function __construct()
        {
            add_shortcode( 
                'eventspractice_custom_reservation',
                array ( $this, 'custom_reservation_shortcode' ) 
            );
        }

        /**
         * Creates a table in the WP database to save the reservation information.
         */
        static function create_reservations_table()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'eventspractice_reservations'; 
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                name tinytext NOT NULL,
                last_name tinytext NOT NULL,
                event_id bigint(20) NOT NULL,
                user_id bigint(20) NOT NULL,
                PRIMARY KEY  (id)
              ) $charset_collate;";
              
              include_once ABSPATH . 'wp-admin/includes/upgrade.php';
              dbDelta($sql);
        }

        /**
         * Outputs HTML code to display the reservation form.
         */
        public function reservation_form()
        {
            ?>
            <h2>RSVP</h2>
            <form method="post">
            <div>
            <label for="name"><?php esc_html_e('First Name', 'eventspractice'); ?></label>
            <input type="text" name="name" id="name" value="" required="" aria-required="true">
            </div>
            <div>
            <label for="name"><?php esc_html_e('Last Name', 'eventspractice'); ?></label>
            <input type="text" name="last_name" id="last_name" value="" required="" aria-required="true">
            </div>
            <div>
            <label for="email"><?php esc_html_e('Email', 'eventspractice'); ?></label>
            <input type="email" name="email" id="email" value="" required="" aria-required="true">
            </div>
            <?php wp_nonce_field(basename(__FILE__), 'eventspractice_reservation_nonce'); ?>
            <button class="wp-block-button__link" data-id-attr="placeholder" type="submit"><?php esc_html_e('Send reservation', 'eventspractice'); ?></button>
            </form>
            <?php
        }
        
        /**
         * Internal function to save the reservation data into the eventspractice_reservations table.
         * 
         * @param string $name The reservor's first name.
         * @param string $last_name The reservor's last name.
         * @param integer $event_id The post ID for the Event CPT.
         * @param integer $user_id The reservor's user ID.
         * @return boolean Returns true after saving the data into the eventspractice_reservations table.
         */
        protected function complete_reservation( $name, $last_name, $event_id, $user_id )
        {
                
                global $wpdb;

                $table = $wpdb->prefix.'eventspractice_reservations';
                
                $wpdb->insert(
                    $table,
                    array (
                        'name'      => $name,
                        'last_name' => $last_name,
                        'event_id'  => $event_id,
                        'user_id'   => $user_id,
                    ),
                    array (
                        '%s', 
                        '%s', 
                        '%d', 
                        '%d'
                    )
                );

                echo '<p><strong>' . esc_html__('Reservation completed.', 'eventspractice') . '</strong></p>';
                return true;   
        }

        /**
         * Sanitizes and saves the form information, and displays the reservation form if the form has not been submited.
         */
        public function custom_reservation_function()
        {
        
            $status = null;
        
            if (isset($_POST['name']) ) {
                // Check that the nonce was set and valid
                if(!wp_verify_nonce($_POST['eventspractice_reservation_nonce'], basename(__FILE__)) ) {
                    echo '<p>' . esc_html__('Did not save because your form seemed to be invalid. Sorry!', 'eventspractice') . '</p>';
                    return;
                }
                // sanitize user form input
                $name               = sanitize_text_field($_POST['name']);
                $lastname           = sanitize_text_field($_POST['last_name']);
                $username           = sanitize_user($name. "_" .$lastname);
                $email              = sanitize_email($_POST['email']);
                $random_password    = wp_generate_password($length = 12, $include_standard_special_chars = false);
                $author             = wp_create_user($username, $random_password, $email);
                $post_id            = get_the_ID();
        
        
                $status = $this->complete_reservation($name, $lastname, $post_id, $author);
            }
            if (!$status ) {
                $this->reservation_form();
            }   
        }

        /**
         * Displays the reservation form in the eventspractice_custom_reservation shortcode.
         */
        public function custom_reservation_shortcode()
        {
            ob_start();
            $this->custom_reservation_function();
            return ob_get_clean();
        }
    }
}

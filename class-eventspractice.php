<?php
/**
 * General Events Practice Class.
 *
 * This class creates the Events Custom Post Type (CPT) and its respective metaboxes.
 *
 * @since 0.0.1
 */
if (!class_exists('Events_Practice') ) {
    class Events_Practice
    {
        /**
         * Construct function that calls action and filter hooks when the class is initialized.
         */
        public function __construct()
        {
            add_action( 
                'init', 
                array( $this, 'events_cpt' ) 
            );
            add_action( 
                'load-post.php', 
                array( $this, 'metaboxes_setup' )
            );
            add_action( 
                'load-post-new.php', 
                array( $this, 'metaboxes_setup' )
            );
            add_filter( 
                'the_content',
                array( $this, 'add_meta_boxes_to_content' ) 
            );
        }

        /**
         * Create the Event CPT.
         */
        public function events_cpt()
        {
            $labels = [
            'name'                          => __('Events', 'eventspractice'),
            'singular_name'                 => __('Event', 'eventspractice'),
            'menu_name'                     => __('My Events', 'eventspractice'),
            'all_items'                     => __('All Events', 'eventspractice'),
            'add_new'                       => __('Add new', 'eventspractice'),
            'add_new_item'                  => __('Add new Event', 'eventspractice'),
            'edit_item'                     => __('Edit Event', 'eventspractice'),
            'new_item'                      => __('New Event', 'eventspractice'),
            'view_item'                     => __('View Event', 'eventspractice'),
            'view_items'                    => __('View Events', 'eventspractice'),
            'search_items'                  => __('Search Events', 'eventspractice'),
            'not_found'                     => __('No Events found', 'eventspractice'),
            'not_found_in_trash'            => __('No Events found in trash', 'eventspractice'),
            'parent'                        => __('Parent Event:', 'eventspractice'),
            'featured_image'                => __('Featured image for this Event', 'eventspractice'),
            'set_featured_image'            => __('Set featured image for this Event', 'eventspractice'),
            'remove_featured_image'         => __('Remove featured image for this Event', 'eventspractice'),
            'use_featured_image'            => __('Use as featured image for this Event', 'eventspractice'),
            'archives'                      => __('Event archives', 'eventspractice'),
            'insert_into_item'              => __('Insert into Event', 'eventspractice'),
            'uploaded_to_this_item'         => __('Upload to this Event', 'eventspractice'),
            'filter_items_list'             => __('Filter Events list', 'eventspractice'),
            'items_list_navigation'         => __('Events list navigation', 'eventspractice'),
            'items_list'                    => __('Events list', 'eventspractice'),
            'attributes'                    => __('Events attributes', 'eventspractice'),
            'name_admin_bar'                => __('Event', 'eventspractice'),
            'item_published'                => __('Event published', 'eventspractice'),
            'item_published_privately'      => __('Event published privately.', 'eventspractice'),
            'item_reverted_to_draft'        => __('Event reverted to draft.', 'eventspractice'),
            'item_scheduled'                => __('Event scheduled', 'eventspractice'),
            'item_updated'                  => __('Event updated.', 'eventspractice'),
            'parent_item_colon'             => __('Parent Event:', 'eventspractice'),
            ];
        
            $args = [
            'label'                     => __('Events', 'eventspractice'),
            'labels'                    => $labels,
            'description'               => 'An events CPT to practice plugin development.',
            'public'                    => true,
            'publicly_queryable'        => true,
            'show_ui'                   => true,
            'show_in_rest'              => true,
            'rest_base'                 => '',
            'rest_controller_class'     => 'WP_REST_Posts_Controller',
            'has_archive'               => 'eventspractice',
            'show_in_menu'              => true,
            'show_in_nav_menus'         => true,
            'delete_with_user'          => false,
            'exclude_from_search'       => false,
            'capability_type'           => 'post',
            'map_meta_cap'              => true,
            'hierarchical'              => false,
            'rewrite'                   => [ 'slug' => 'event', 'with_front' => true ],
            'query_var'                 => true,
            'supports'                  => [ 'title', 'editor', 'thumbnail', 'author' ],
            'show_in_graphql'           => false,
            'menu_icon'                 => 'dashicons-calendar-alt',
            ];
        
            register_post_type('event', $args);
        }

        /**
         * Handle action hooks to add metaboxes to the Event CPT.
         */
        public function metaboxes_setup()
        {
            add_action( 
                'add_meta_boxes', 
                array( $this, 'add_post_meta_boxes' ) 
            );
            add_action( 
                'save_post', 
                array( $this, 'eventspractice_save_post_class_meta' ), 
                10, 
                2 
            );
        }

        /**
         * Internal function to handle the creation of metaboxes.
         * 
         * @param string $id The ID of the metabox.
         * @param string $title The label/title for the metabox.
         * @param callback $cb The callback function that displays the metabox.
         * @param string $cpt The CPT where the metabox will be added.
         * @return function The function that will add the metabox.
         */
        protected function generate_metaboxes( $id, $title, $cb, $cpt )
        {
            return add_meta_box(
                $id,
                esc_html__($title, 'eventspractice'),
                array( $this, $cb ),
                $cpt,
                'side',
                'default'
            );
        }

        /**
         * Create multiple metaboxes by calling the generate_metaboxes function.
         */
        public function add_post_meta_boxes()
        {
            $this->generate_metaboxes( 
                'eventspractice-number-attendees', 
                'Number of attendees', 
                'number_attendees_meta_box', 
                'event'
            );
            $this->generate_metaboxes(
                'eventspractice-location',
                'Location',
                'eventspractice_location_meta_box',
                'event'
            );
            $this->generate_metaboxes(
                'eventspractice-date',
                'Date',
                'eventspractice_date_meta_box',
                'event'
            );
        }

        /**
         * Generate generic HTML code to display Text Field metaboxes.
         * 
         * @param object $post The post object.
         * @param string $name The name of the text field.
         * @param string $id The text field ID.
         * @return string The HTML code with the metabox text fields.
         */
        protected function generate_metabox_html_text_fields( $post, $name, $id )
        {
            $label     = __($name, 'eventspractice');
            $nonce     = $id . '-nonce';
            $value     = esc_attr(get_post_meta($post->ID, $id, true));

            $html_field  = '';
            $html_field .= wp_nonce_field(basename(__FILE__), $nonce);
            $html_field .= '<p>';
            $html_field .= '<label for="';
            $html_field .= $id;
            $html_field .= '">'; 
            $html_field .= $label;
            $html_field .= '</label>';
            $html_field .= '<br />';
            $html_field .= '<input class="widefat" type="text"';
            $html_field .= 'name="';
            $html_field .= $id;
            $html_field .= '" id="';
            $html_field .= $id;
            $html_field .= '" value="';
            $html_field .= $value;
            $html_field .= '" size="30" />';
            $html_field .= '</p>';

            return $html_field;
        }


        /**
         * Display the metaboxes by calling the generate_metabox_html_text_fields function.
         * Note: This and the two subsequent functions could be merged into a single function by using a loop. This could be a good optimization for the next version.
         */
        public function number_attendees_meta_box( $post )
        {
            echo $this->generate_metabox_html_text_fields( 
                $post,
                'Maximum number of attendees for this event.',
                'eventspractice-number-attendees'
            );
        }
        public function eventspractice_location_meta_box( $post )
        {
            echo $this->generate_metabox_html_text_fields( 
                $post,
                'Address for this event.',
                'eventspractice-location'
            );
        }
        public function eventspractice_date_meta_box( $post )
        {
            echo $this->generate_metabox_html_text_fields( 
                $post,
                'Date for this event.',
                'eventspractice-date'
            );
        }

        /**
         * Internal function to verify the nonce value.
         * 
         * @param string $id The nonce ID.
         * @return boolean If the nonce verification is successful it returns false.
         */
        protected function verify_text_field_nonce( $id )
        {
            $nonce = $id . '-nonce';
            return !isset($_POST[ $nonce ]) || !wp_verify_nonce($_POST[ $nonce ], basename(__FILE__));
        }

        /**
         * Internal function to get the data when the form is submitted.
         * 
         * @param string $id The metabox ID.
         * @return string Returns the new value for the metabox after sanitizing it.
         */
        protected function get_posted_data( $id )
        {
            return ( isset($_POST[ $id ]) ? sanitize_text_field($_POST[ $id ]) : null );
        }

        /**
         * Save the metabox information/fields.
         * The code was referenced from this guide: https://www.smashingmagazine.com/2011/10/create-custom-post-meta-boxes-wordpress/
         * 
         * @param integer $post_id The current post ID.
         * @param object $post The post object.
         * @return integer Only returns the $post_id if something went wrong.
         */
        public function eventspractice_save_post_class_meta( $post_id, $post )
        {
            $meta_id_number_attendees = 'eventspractice-number-attendees';
            $meta_id_location = 'eventspractice-location';
            $meta_id_date = 'eventspractice-date';
            // Verify the nonce before proceeding.
            if (
                $this->verify_text_field_nonce($meta_id_number_attendees) 
                && 
                $this->verify_text_field_nonce($meta_id_location) 
                && 
                $this->verify_text_field_nonce($meta_id_date)
            ) {
                return $post_id;
            }

            // Get the post type object
            $post_type = get_post_type_object($post->post_type);

            // Check if the current user has permission to edit the post.
            if (!current_user_can($post_type->cap->edit_post, $post_id) ) {
                return $post_id;
            }
            
            // Get the posted data and sanitize it for use as an HTML class.
            $new_meta_value_number_attendees     = $this->get_posted_data($meta_id_number_attendees);
            $new_meta_value_location             = $this->get_posted_data($meta_id_location);
            $new_meta_value_date                 = $this->get_posted_data($meta_id_date);

            // Save the changes
            if (isset($new_meta_value_number_attendees) ) {
                update_post_meta($post_id, $meta_id_number_attendees, $new_meta_value_number_attendees);
            } 
            if (isset($new_meta_value_location) ) {
                update_post_meta($post_id, $meta_id_location, $new_meta_value_location);
            } 
            if (isset($new_meta_value_date) ) {
                update_post_meta($post_id, $meta_id_date, $new_meta_value_date);
            } 
        }

        /**
         * Display the metaboxes after the content in the Event CPT
         * 
         * @param string $content The current post content.
         * @return string Returns the conent of the post with the content of the metaboxes at the end.
         */
        public function add_meta_boxes_to_content( $content )
        {   
            $meta_id_number_attendees = 'eventspractice-number-attendees';
            $meta_id_location = 'eventspractice-location';
            $meta_id_date = 'eventspractice-date';

            $number_attendees = get_post_meta(get_the_ID(), $meta_id_number_attendees, true);
            $location = get_post_meta(get_the_ID(), $meta_id_location, true);
            $date = get_post_meta(get_the_ID(), $meta_id_date, true);
            
            if(is_single() && 'event' == get_post_type() ) {
                $content .= '<hr class="wp-block-separator"/>';
                $content .= '<div class="eventspractice-metadata"><ul>';
                $content .= "<li><strong>" . esc_html__('Max. Number of Attendees:', 'eventspractice') . "</strong> " . esc_attr($number_attendees) . "</li>";
                $content .= "<li><strong>" . esc_html__('Location:', 'eventspractice') . "</strong> " .  esc_attr($location) . "</li>";
                $content .= "<li><strong>" . esc_html__('Date:', 'eventspractice') . "</strong> ".  esc_attr($date) . "</li>";
                $content .= '</ul></div>';
                $content .= '<hr class="wp-block-separator"/>';

            }
            return $content;
        }

    }
}

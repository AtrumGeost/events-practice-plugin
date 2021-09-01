<?php

if ( !class_exists( 'Events_Practice' ) ) {
	class Events_Practice {
		public function __construct() {
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
		public function events_cpt () {
			$labels = [
				'name' 						=> __( 'Events', 'eventspractice' ),
				'singular_name' 			=> __( 'Event', 'eventspractice' ),
				'menu_name' 				=> __( 'My Events', 'eventspractice' ),
				'all_items' 				=> __( 'All Events', 'eventspractice' ),
				'add_new' 					=> __( 'Add new', 'eventspractice' ),
				'add_new_item' 				=> __( 'Add new Event', 'eventspractice' ),
				'edit_item' 				=> __( 'Edit Event', 'eventspractice' ),
				'new_item' 					=> __( 'New Event', 'eventspractice' ),
				'view_item' 				=> __( 'View Event', 'eventspractice' ),
				'view_items' 				=> __( 'View Events', 'eventspractice' ),
				'search_items' 				=> __( 'Search Events', 'eventspractice' ),
				'not_found' 				=> __( 'No Events found', 'eventspractice' ),
				'not_found_in_trash' 		=> __( 'No Events found in trash', 'eventspractice' ),
				'parent' 					=> __( 'Parent Event:', 'eventspractice' ),
				'featured_image' 			=> __( 'Featured image for this Event', 'eventspractice' ),
				'set_featured_image' 		=> __( 'Set featured image for this Event', 'eventspractice' ),
				'remove_featured_image' 	=> __( 'Remove featured image for this Event', 'eventspractice' ),
				'use_featured_image' 		=> __( 'Use as featured image for this Event', 'eventspractice' ),
				'archives' 					=> __( 'Event archives', 'eventspractice' ),
				'insert_into_item' 			=> __( 'Insert into Event', 'eventspractice' ),
				'uploaded_to_this_item' 	=> __( 'Upload to this Event', 'eventspractice' ),
				'filter_items_list' 		=> __( 'Filter Events list', 'eventspractice' ),
				'items_list_navigation' 	=> __( 'Events list navigation', 'eventspractice' ),
				'items_list' 				=> __( 'Events list', 'eventspractice' ),
				'attributes' 				=> __( 'Events attributes', 'eventspractice' ),
				'name_admin_bar' 			=> __( 'Event', 'eventspractice' ),
				'item_published' 			=> __( 'Event published', 'eventspractice' ),
				'item_published_privately' 	=> __( 'Event published privately.', 'eventspractice' ),
				'item_reverted_to_draft' 	=> __( 'Event reverted to draft.', 'eventspractice' ),
				'item_scheduled' 			=> __( 'Event scheduled', 'eventspractice' ),
				'item_updated' 				=> __( 'Event updated.', 'eventspractice' ),
				'parent_item_colon' 		=> __( 'Parent Event:', 'eventspractice' ),
			];
		
			$args = [
				'label' 					=> __( 'Events', 'eventspractice' ),
				'labels' 					=> $labels,
				'description' 				=> 'An events CPT to practice plugin development.',
				'public' 					=> true,
				'publicly_queryable' 		=> true,
				'show_ui' 					=> true,
				'show_in_rest' 				=> true,
				'rest_base' 				=> '',
				'rest_controller_class' 	=> 'WP_REST_Posts_Controller',
				'has_archive' 				=> 'eventspractice',
				'show_in_menu' 				=> true,
				'show_in_nav_menus' 		=> true,
				'delete_with_user' 			=> false,
				'exclude_from_search' 		=> false,
				'capability_type' 			=> 'post',
				'map_meta_cap' 				=> true,
				'hierarchical' 				=> false,
				'rewrite' 					=> [ 'slug' => 'event', 'with_front' => true ],
				'query_var' 				=> true,
				'supports' 					=> [ 'title', 'editor', 'thumbnail', 'author' ],
				'show_in_graphql' 			=> false,
				'menu_icon' 				=> 'dashicons-calendar-alt',
			];
		
			register_post_type( 'event', $args );
		}

		public function metaboxes_setup(){
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

		protected function generate_metaboxes( $id, $title, $cb, $cpt ){
			return add_meta_box(
				$id,
				esc_html__( $title, 'eventspractice' ),
				array( $this, $cb ),
				$cpt,
				'side',
				'default'
			);
		}

		public function add_post_meta_boxes() {
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


		  protected function generate_metabox_html_text_fields ( $post, $name, $id ){
			$label 	= __( $name , 'eventspractice' );
			$nonce 	= $id . '-nonce';
			$value 	= esc_attr( get_post_meta( $post->ID, $id, true ) );

			$html_field  = '';
			$html_field .= wp_nonce_field( basename( __FILE__ ), $nonce );
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


		/* Display the post meta box. */
		public function number_attendees_meta_box( $post ) {
			echo $this->generate_metabox_html_text_fields ( 
				$post,
				'Maximum number of attendees for this event.',
				'eventspractice-number-attendees'
			);
		}

		public function eventspractice_location_meta_box( $post ){
			echo $this->generate_metabox_html_text_fields ( 
				$post,
				'Address for this event.',
				'eventspractice-location'
			);
		}

		public function eventspractice_date_meta_box( $post ) {
			echo $this->generate_metabox_html_text_fields ( 
				$post,
				'Date for this event.',
				'eventspractice-date'
			);
		}

		protected function verify_text_field_nonce( $id ){
			$nonce = $id . '-nonce';
			return !isset($_POST[ $nonce ] ) || !wp_verify_nonce( $_POST[ $nonce ], basename( __FILE__ ) );
		}

		protected function get_posted_data( $id ){
			return ( isset( $_POST[ $id ] ) ? sanitize_text_field( $_POST[ $id ] ) : null );
		}

		

		/* Save the meta box’s post metadata. */
		function eventspractice_save_post_class_meta( $post_id, $post ) {
			$meta_id_number_attendees = 'eventspractice-number-attendees';
			$meta_id_location = 'eventspractice-location';
			$meta_id_date = 'eventspractice-date';
			/* Verify the nonce before proceeding. */
			if ( 
					$this->verify_text_field_nonce( $meta_id_number_attendees ) &&
					$this->verify_text_field_nonce( $meta_id_location ) &&
					$this->verify_text_field_nonce( $meta_id_date )
				) {
				return $post_id;
			}

			/* Get the post type object. */
			$post_type = get_post_type_object( $post->post_type );

			/* Check if the current user has permission to edit the post. */
			if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ){
				return $post_id;
			}
			
			/* Get the posted data and sanitize it for use as an HTML class. */
			$new_meta_value_number_attendees 	= $this->get_posted_data( $meta_id_number_attendees );
			$new_meta_value_location 			= $this->get_posted_data( $meta_id_location );
			$new_meta_value_date 				= $this->get_posted_data( $meta_id_date );

			if ( isset( $new_meta_value_number_attendees ) ) {
				update_post_meta( $post_id, $meta_id_number_attendees, $new_meta_value_number_attendees );
			} 
			if ( isset( $new_meta_value_location ) ) {
				update_post_meta( $post_id, $meta_id_location, $new_meta_value_location );
			} 
			if ( isset( $new_meta_value_date ) ) {
				update_post_meta( $post_id, $meta_id_date, $new_meta_value_date );
			} 
		}

		/* Display custom posts at the end of content on CPT */

		function add_meta_boxes_to_content( $content ) {   
			$meta_id_number_attendees = 'eventspractice-number-attendees';
			$meta_id_location = 'eventspractice-location';
			$meta_id_date = 'eventspractice-date';

			$number_attendees = get_post_meta( get_the_ID(), $meta_id_number_attendees, true );
			$location = get_post_meta( get_the_ID(), $meta_id_location, true );
			$date = get_post_meta( get_the_ID(), $meta_id_date, true );
			
			if( is_single( ) && 'event' == get_post_type() ) {
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

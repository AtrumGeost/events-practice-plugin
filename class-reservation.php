<?php

function eventspractice_register_reservation_cpt() {

	/**
	 * Post Type: Reservations.
	 */

	$labels = [
		"name" => __( "Reservations", "eventspractice" ),
		"singular_name" => __( "Reservation", "eventspractice" ),
		"menu_name" => __( "Reservations", "eventspractice" ),
		"all_items" => __( "All Reservations", "eventspractice" ),
		"add_new" => __( "Add new", "eventspractice" ),
		"add_new_item" => __( "Add new Reservation", "eventspractice" ),
		"edit_item" => __( "Edit Reservation", "eventspractice" ),
		"new_item" => __( "New Reservation", "eventspractice" ),
		"view_item" => __( "View Reservation", "eventspractice" ),
		"view_items" => __( "View Reservations", "eventspractice" ),
		"search_items" => __( "Search Reservations", "eventspractice" ),
		"not_found" => __( "No Reservations found", "eventspractice" ),
		"not_found_in_trash" => __( "No Reservations found in trash", "eventspractice" ),
		"parent" => __( "Parent Reservation:", "eventspractice" ),
		"featured_image" => __( "Featured image for this Reservation", "eventspractice" ),
		"set_featured_image" => __( "Set featured image for this Reservation", "eventspractice" ),
		"remove_featured_image" => __( "Remove featured image for this Reservation", "eventspractice" ),
		"use_featured_image" => __( "Use as featured image for this Reservation", "eventspractice" ),
		"archives" => __( "Reservation archives", "eventspractice" ),
		"insert_into_item" => __( "Insert into Reservation", "eventspractice" ),
		"uploaded_to_this_item" => __( "Upload to this Reservation", "eventspractice" ),
		"filter_items_list" => __( "Filter Reservations list", "eventspractice" ),
		"items_list_navigation" => __( "Reservations list navigation", "eventspractice" ),
		"items_list" => __( "Reservations list", "eventspractice" ),
		"attributes" => __( "Reservations attributes", "eventspractice" ),
		"name_admin_bar" => __( "Reservation", "eventspractice" ),
		"item_published" => __( "Reservation published", "eventspractice" ),
		"item_published_privately" => __( "Reservation published privately.", "eventspractice" ),
		"item_reverted_to_draft" => __( "Reservation reverted to draft.", "eventspractice" ),
		"item_scheduled" => __( "Reservation scheduled", "eventspractice" ),
		"item_updated" => __( "Reservation updated.", "eventspractice" ),
		"parent_item_colon" => __( "Parent Reservation:", "eventspractice" ),
	];

	$args = [
		"label" => __( "Reservations", "eventspractice" ),
		"labels" => $labels,
		"description" => "A CPT that\'s created when a user reserves their spot on an event.",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => "reservation",
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "reservation", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-email",
		"supports" => [ "title", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "reservation", $args );
}

add_action( 'init', 'eventspractice_register_reservation_cpt' );

function reservation_form () {
    ?>
    <h2>RSVP</h2>
    <form method="post">
    <div>
    <label for="name"><?php esc_html_e('First Name','eventspractice'); ?></label>
    <input type="text" name="name" id="name" value="" required="" aria-required="true">
    </div>
    <div>
    <label for="name"><?php esc_html_e('Last Name','eventspractice'); ?></label>
    <input type="text" name="last_name" id="last_name" value="" required="" aria-required="true">
    </div>
    <div>
    <label for="email"><?php esc_html_e('Email','eventspractice'); ?></label>
    <input type="email" name="email" id="email" value="" required="" aria-required="true">
    </div>
    <?php wp_nonce_field( basename( __FILE__ ), 'eventspractice_reservation_nonce' ); ?>
    <button class="wp-block-button__link" data-id-attr="placeholder" type="submit"><?php esc_html_e('Send reservation','eventspractice'); ?></button>
    </form>
    <?php
}


function complete_reservation($username, $author) {
        $args = [
            'post_title'    => get_the_title() . " — Reserved by " . $username,
            'post_status'   => 'publish',
            'post_type'     => 'reservation',
            'post_author'   => $author,
        ];
        wp_insert_post($args);
        echo '<p><strong>' . esc_html__('Reservation completed.','eventspractice') . '</strong></p>';
        return true;   
}

function custom_reservation_function() {

	$status = null;

    if ( isset($_POST['name'] ) ) {
        // Check that the nonce was set and valid
        if( !wp_verify_nonce($_POST['eventspractice_reservation_nonce'], basename( __FILE__ )) ) {
            echo '<p>' . esc_html__('Did not save because your form seemed to be invalid. Sorry!','eventspractice') . '</p>';
            return;
        }
        // sanitize user form input
        $name               = sanitize_text_field( $_POST['name'] );
        $lastname           = sanitize_text_field( $_POST['last_name'] );
        $username           = sanitize_user($name. "_" .$lastname);
        $email              = sanitize_email( $_POST['email'] );
        $random_password    = wp_generate_password( $length = 12, $include_standard_special_chars = false );
        $author             = wp_create_user( $username, $random_password, $email );


        $status = complete_reservation($username, $author);
    }
    if ( !$status )
    {
        reservation_form ();
    }
    
 
    
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'eventspractice_custom_reservation', 'custom_reservation_shortcode' );
 
// The callback function that will replace [book]
function custom_reservation_shortcode() {
    ob_start();
    custom_reservation_function();
    return ob_get_clean();
}
<?php
/**
 * Plugin Name:       Events Practice
 * Plugin URI:        https://github.com/AtrumGeost/events-practice-plugin
 * Description:       A plugin to practice for the Dev. App.
 * Version:           0.0.1
 * Author:            Jorge Calle
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eventspractice
 * Domain Path:       /languages
 */

 // If this file is called directly, abort
 if ( !defined( 'WPINC' ) ) {
     die;
 }

// Define plugin paths and URLs
define( 'EVENTSPRACTICE_URL', plugin_dir_url( __FILE__ ) );
define( 'EVENTSPRACTICE_DIR', plugin_dir_path( __FILE__ ) );


// Include the main plugin class
include( EVENTSPRACTICE_DIR. '/class-eventspractice.php' );

// Include the rsvp class
include( EVENTSPRACTICE_DIR. '/class-reservation-1.php' );

include( EVENTSPRACTICE_DIR. 'includes/eventspractice-menus.php' );


$events_practice = new Events_Practice();

// Add link to the settings page below the plugin description
function eventspractice_add_settings_link( $links )
{
    $settings_link = '<a href="admin.php?page=wpplugin-settings">' . esc_html__( 'Settings', 'eventspractice' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$filter_name = "plugin_action_links_" . plugin_basename( __FILE__ );
add_filter( $filter_name, 'eventspractice_add_settings_link' );

// Create Plugin Options
include( EVENTSPRACTICE_DIR . 'includes/eventspractice-options.php');

// Testing table creation
$reservation = new Events_Practice_Reservation();
register_activation_hook( __FILE__, array( 'Events_Practice_Reservation', 'create_reservations_table' ) );
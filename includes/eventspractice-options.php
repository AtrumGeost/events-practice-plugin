<?php
/**
* Plugin Options.
*
* Experimental to test the functions to display options inside specific menu items.
*/

// Function for learning how to add options
// SQL Query: SELECT * FROM wp_options WHERE option_name = "eventspractice_option";
function eventspractice_options() {

  $options = array (
    'Option 1' => 'A',
    'Option 2' => 'B',
    'Option 3' => 'C'
  );

  if ( !get_option( 'eventspractice_options' ) ){
    add_option( 'eventspractice_option', $options );
  }
  update_option( 'eventspractice_option', $options );


  // add_option( 'eventspractice_option', 'My NEW Plugin Options' );
  // update_option( 'eventspractice_option', 'This is a test with the Options API' );
  // delete_option( 'eventspractice_option' );

}
add_action( 'admin_init', 'eventspractice_options' );

// Settings practice

function eventspractice_settings() {
  // If plugin settings don't exist, then create them
  if( !get_option( 'eventspractice_settings' ) ) {
    add_option( 'eventspractice_settings' );
}
  // Define (at least) one section for our fields
  add_settings_section(
    // Unique identifier for the section
    'eventspractice_settings_section',
    // Section Title
    __( 'My first section', 'eventspractice' ),
    // Callback for an optional description
    'eventspractice_settings_section_callback',
    // Admin page to add section to
    'eventspractice-settings'
  );

  add_settings_field(
    // Unique identifier for field
    'eventspractice_settings_custom_text',
    // Field Title
    __( 'Custom Texty Text', 'wpplugin'),
    // Callback for field markup
    'eventspractice_settings_custom_text_callback',
    // Page to go on
    'eventspractice-settings',
    // Section to go in
    'eventspractice_settings_section'
  );

  register_setting(
    'eventspractice_settings',
    'eventspractice_settings'
  );

}
add_action( 'admin_init', 'eventspractice_settings' );

function eventspractice_settings_section_callback() {

  esc_html_e( 'Events Practice settings section description :D', 'eventspractice' );

}

function eventspractice_settings_custom_text_callback() {

  $options = get_option( 'eventspractice_settings' );

	$custom_text = '';
	if( isset( $options[ 'custom_text' ] ) ) {
		$custom_text = esc_html( $options['custom_text'] );
	}

  echo '<input type="text" id="eventspractice_customtext" name="eventspractice_settings[custom_text]" value="' . $custom_text . '" />';

}
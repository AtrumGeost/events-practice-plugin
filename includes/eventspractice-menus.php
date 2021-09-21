<?php
/**
* Plugin Menus.
*
* Experimental to test the functions to add menus and submenus.
*/

// Add pages to WP Admin
 function eventspractice_settings_page()
 {
     add_menu_page(
         __( 'Events Practice', 'eventspractice' ),
         __( 'Events Practice', 'eventspractice' ),
         'manage_options',
         'eventspractice',
         'eventspractice_settings_page_markup',
         'dashicons-calendar-alt',
         25
     );

     add_submenu_page(
        'eventspractice',
        __( 'Events', 'eventspractice' ),
        __( 'Events', 'eventspractice' ),
        'manage_options',
        'eventspractice-events',
        'eventspractice_settings_subpage_markup'
     );

     add_submenu_page(
        'eventspractice',
        __( 'RSVPs', 'eventspractice' ),
        __( 'RSVPs', 'eventspractice' ),
        'manage_options',
        'eventspractice-rsvps',
        'eventspractice_settings_subpage_markup'
     );

     add_submenu_page(
        'eventspractice',
        __( 'Settings', 'eventspractice' ),
        __( 'Settings', 'eventspractice' ),
        'manage_options',
        'eventspractice-settings',
        'eventspractice_settings_subpage_markup'
     );
 }
add_action( 'admin_menu', 'eventspractice_settings_page' );

// Display content for the admin page
function eventspractice_settings_page_markup()
{
    // Double check user capabilities
    if ( !current_user_can('manage_options') ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( get_admin_page_title(), 'eventspractice' ); ?></h1>
        <p><?php esc_html_e( 'Some content.', 'eventspractice' ); ?></p>
    </div>
    <?php
}

// Display content for the admin subpages
function eventspractice_settings_subpage_markup()
{
    // Double check user capabilities
    if ( !current_user_can('manage_options') ) {
        return;
    }
    include( EVENTSPRACTICE_DIR . 'templates/admin/settings-page.php' );
}
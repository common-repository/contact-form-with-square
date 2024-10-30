<?php
/**
 * Define admin menu and sub menu 
 */
add_action( 'admin_menu', 'cfws_admin_settings_page' );

/**
 * Add admin menu in wordpress admin
 */
if (!function_exists('cfws_admin_settings_page')) 
{
	function cfws_admin_settings_page() {
	    add_menu_page(__("LM CONTACT","lm-contact-square"), __("LM CONTACT","lm-contact-square"), 'manage_options', 'lm-form-listing','cfws_settings_page',plugins_url('contact-form-with-square/assets/images/icon.png'));
	    add_submenu_page('lm-form-listing', __("Settings","lm-contact-square"), __("Settings","lm-contact-square"), 'manage_options','lm-square-settings','cfws_square_setting_page' );
	}
}

/**
 * Class File for listing Data from the table
 */
include_once __DIR__ . '/class-contact-listing.php';

/**
 * Display Contact Form Listing
 */
include_once __DIR__ . '/lm-contact-form-listing.php';


/**
 * Admin Square Payment gateway Settings
 */
include_once __DIR__ . '/square-general_settings.php';

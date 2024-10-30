<?php
/*
Plugin Name: Contact Form With Square
Plugin URI: http://wordpress.org/plugins/contact-form-with-square/
Description: This plugin is used to accept the payment online via square payment gateway.
Requires at least: 4.6
Tested up to: 5.3.0
Version: 1.0.0
Author: LUCKI MEDIA
Text Domain: lm-contact-square
Author URI: https://luckimedia.in/
*/

/**
 * Define Global & Constant Variable 
 * @global type $cfws_db_version
 */
global $cfws_db_version;
$cfws_db_version = '1.0';
define('CFWS_URL', plugin_dir_url( __FILE__ ));
define('CFWS_URI', plugin_dir_path( __DIR__ ));

/**
 * Activation & Deactivation.
 * Activation Hook to Create Table & Store Form data.
 * Deactivation Hook to flush data.
 */
register_activation_hook( __FILE__, 'cfws_install' );
register_deactivation_hook( __FILE__, 'cfws_uninstall' );

/**
 * Create table
 * @global type $cfws_db_version
 * @global type $wpdb
 */
if (!function_exists('cfws_install ')) 
{
	function cfws_install() {
		global $wpdb;
		global $cfws_db_version;
		$table_name = $wpdb->prefix . 'cfws_contact';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name tinytext NOT NULL,
			last_name tinytext NOT NULL,
			email varchar(255) DEFAULT '' NOT NULL,
			phone varchar(255) DEFAULT '' NOT NULL,
			amount varchar(255) DEFAULT '' NOT NULL,
			date_time DATETIME NOT NULL,
			transaction_id varchar(255) DEFAULT '' NOT NULL,
			transaction_data Text DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'cfws_db_version', $cfws_db_version );
	}
}

/**
 * Deactivation hook for plugin.
 */
if (!function_exists('cfws_uninstall ')) 
{
	function cfws_uninstall() {
		// Deactivation rules here
	}
}
/**
 * Include All Css and Js for Front End
 */
include_once __DIR__ . '/includes/enqueue.php';

/**
 * Load Admin Files
 */
include_once __DIR__ . '/admin/admin-settings.php';

/**
 * Custom Contact From
 */
include_once __DIR__ . '/includes/lm-contact-form.php';

/**
 * Square Payment Gateway Lib
 */
require_once __DIR__ . '/lib/square-sdk/autoload.php';

/**
 * Suare Payment gateway form fields define for front end
 */
include_once __DIR__ . '/includes/square-form.php';

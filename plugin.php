<?php
/*
Plugin Name: Webmaster User Role
Plugin URI: http://tylerdigital.com
Description: Adds a Webmaster user role between Administrator and Editor.  By default this user is the same as Administrator, without the capability to manage plugins or change themes
Version: 1.0
Author: Tyler Digital
Author URI: http://tylerdigital.com
Author Email: support@tylerdigital.com
License:

  Copyright 2012 Tyler Digital

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class TD_WebmasterUserRole {
	
	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/

	const name = 'Webmaster User Role';
	
	const slug = 'td-webmaster-user-role';
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
	
	    // Define constants used throughout the plugin
	    $this->init_plugin_constants();
  
		load_plugin_textdomain( PLUGIN_LOCALE, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		
    	// Load JavaScript and stylesheets
    	// $this->register_scripts_and_styles();

	} // end constructor
	
	function activate() {
		$admin_role = get_role('administrator');
		$capabilities = $admin_role->capabilities;
		unset($capabilities['level_10']);
		unset($capabilities['install_plugins']);
		unset($capabilities['activate_plugins']);
		unset($capabilities['update_plugins']);
		unset($capabilities['edit_plugins']);
		unset($capabilities['delete_plugins']);
		unset($capabilities['install_themes']);
		unset($capabilities['update_themes']);
		unset($capabilities['switch_themes']);
		unset($capabilities['edit_themes']);
		unset($capabilities['delete_themes']);
		
		/* Add Gravity Forms Capabilities */
		$capabilities['gravityforms_view_entries'] = 1;
		$capabilities['gravityforms_edit_forms'] = 1;
		add_role('webmaster', 'Webmaster', $capabilities);
	}
	function deactivate() {
		remove_role('webmaster');
	}
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
  
	/*--------------------------------------------*
	 * Private Functions
	 *---------------------------------------------*/
   
	/**
	 * Initializes constants used for convenience throughout 
	 * the plugin.
	 */
	private function init_plugin_constants() {
		
		if ( !defined( 'PLUGIN_NAME' ) ) {
		  define( 'PLUGIN_NAME', self::name );
		} // end if
		
		if ( !defined( 'PLUGIN_SLUG' ) ) {
		  define( 'PLUGIN_SLUG', self::slug );
		} // end if
	
	} // end init_plugin_constants
	
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else { 
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {
		
		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') );
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if
    
	} // end load_file
  
} // end class
$td_webmaster_user_role = new TD_WebmasterUserRole();
register_activation_hook(__FILE__, array($td_webmaster_user_role, 'activate') );
register_deactivation_hook( __FILE__, array($td_webmaster_user_role, 'deactivate') );

?>
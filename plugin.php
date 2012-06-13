<?php
/*
Plugin Name: Webmaster User Role
Plugin URI: http://tylerdigital.com
Description: Adds a Webmaster user role between Administrator and Editor.  By default this user is the same as Administrator, without the capability to manage plugins or change themes
Version: 1.0.2
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

if(!class_exists('TD_WebmasterUserRole')) {
class TD_WebmasterUserRole {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/

	const name = 'Webmaster User Role';

	const slug = 'td-webmaster-user-role';

	private $default_options = array(
		'role_display_name' => 'Admin',
		'cap_gravityforms_view_entries' => 1,
		'cap_gravityforms_edit_forms' => 0,
	);
 
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
		add_action('wpmu_new_blog', array($this, 'add_role_to_blog'));
		add_action('updated_'.self::slug.'_option', array($this, 'updated_option'), 10, 3);
		add_action('deleted_'.self::slug.'_option', array($this, 'deleted_option'));
	} // end constructor

	function activate($network_wide) {		
		if($network_wide) {
			$blogs = $this->_blogs();
			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				$capabilities = $this->capabilities();
				add_role('webmaster', $this->get_option('role_display_name'), $capabilities);
				restore_current_blog();
			}
		
		} else {
			$capabilities = $this->capabilities();
			add_role('webmaster', $this->get_option('role_display_name'), $capabilities);
		}
	}
	function deactivate($network_wide) {
		if($network_wide) {
			$blogs = $this->_blogs();
			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				remove_role('webmaster');
				restore_current_blog();
			}
		
		} else {
			remove_role('webmaster');
		}
	}
	
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
	function capabilities() {
		$admin_role = get_role('administrator');
		$capabilities = $admin_role->capabilities;
		unset($capabilities['level_10']);
		unset($capabilities['update_core']);
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
		unset($capabilities['add_users']);
		unset($capabilities['edit_users']);
		unset($capabilities['promote_users']);
	
		/* Add Gravity Forms Capabilities */
		$capabilities['gravityforms_view_entries'] = $this->get_option('cap_gravityforms_view_entries');
		$capabilities['gravityforms_edit_forms'] = $this->get_option('cap_gravityforms_edit_forms');
		
		return $capabilities;
	}
	
	function add_role_to_blog($blog_id) {
		switch_to_blog( $blog_id );
		$capabilities = $this->capabilities();
		add_role('webmaster', 'Admin', $capabilities);
		restore_current_blog();
	}

	function updated_option($option, $oldvalue, $newValue) {
		if($option=='role_display_name' || strpos('cap_', $option)!==false) {
			$this->deactivate(false);
			$this->activate(false);
		}
	}

	function deleted_option($option) {
		if($option=='role_display_name' || strpos('cap_', $option)!==false) {
			$this->deactivate(false);
			$this->activate(false);
		}
	}

	function get_option($option) {
		// Allow plugins to short-circuit options.
		$pre = apply_filters( 'pre_'.self::slug.'_option_' . $option, false );
		if ( false !== $pre )
			return $pre;

		$option = trim($option);
		if ( empty($option) )
			return false;

		$saved_options = get_option(self::slug.'_options');

		if ( isset( $saved_options[$option] ) ) {
			$value = $saved_options[$option];
		} else {
			$saved_options = (empty($saved_options)) ? array() : $saved_options;
			$saved_options = array_merge($this->default_options, $saved_options);
			$value = $saved_options[$option];
		}

		return apply_filters( self::slug.'option_' . $option, $value );
	}

	function update_option($option, $newValue) {
		$option = trim($option);
		if ( empty($option) )
			return false;

		if ( is_object($newvalue) )
			$newvalue = clone $newvalue;

		$oldvalue = $this->get_option( $option );
		$newvalue = apply_filters( 'pre_update_'.self::slug.'_option_' . $option, $newvalue, $oldvalue );

		// If the new and old values are the same, no need to update.
		if ( $newvalue === $oldvalue )
			return false;

		$_newvalue = $newvalue;
		$newvalue = maybe_serialize( $newvalue );

		do_action( 'update_'.self::slug.'_option', $option, $oldvalue, $_newvalue );

		$options = get_option(self::slug.'_options');
		if(empty($options)) $options = array($option => $newValue);
		else $options[$option] = $newValue;
		update_option(self::slug.'_options', $options);

		do_action( "update_".self::slug."_option_{$option}", $oldvalue, $_newvalue );
		do_action( 'updated_'.self::slug.'_option', $option, $oldvalue, $_newvalue );

		return true;
	}

	function delete_option($option) {
		do_action( 'delete_'.self::slug.'_option', $option );
		$options = get_option(self::slug.'_options');
		if(!isset($options[$option])) return false;
		unset($options[$option]);
		
		$result = update_option(self::slug.'_options', $options);

		if($result) {
			do_action( "delete_".self::slug."_option_$option", $option );
			do_action( 'deleted_'.self::slug.'_option', $option );
			return true;
		}
		return false;
	}

	
	
	/*--------------------------------------------*
	 * Private Functions
	 *---------------------------------------------*/
	
  		function _blogs() {
		global $wpdb;
		$blogs = $wpdb->get_col( $wpdb->prepare( "
			SELECT blog_id
			FROM {$wpdb->blogs}
			WHERE site_id = %d
			AND blog_id <> %d
			AND spam = '0'
			AND deleted = '0'
			AND archived = '0'
			ORDER BY registered DESC
			LIMIT %d, 5
		", $wpdb->siteid, $wpdb->blogid, $offset ) );
		
		return $blogs;
	}

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
} // end class_exists()
if(!isset($td_webmaster_user_role)) $td_webmaster_user_role = new TD_WebmasterUserRole();
register_activation_hook(__FILE__, array($td_webmaster_user_role, 'activate') );
register_deactivation_hook( __FILE__, array($td_webmaster_user_role, 'deactivate') );

?>
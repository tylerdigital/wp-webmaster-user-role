<?php
/*
Plugin Name: Webmaster User Role
Plugin URI: http://tylerdigital.com/products/webmaster-user-role/
Description: Adds a Webmaster user role between Administrator and Editor.  By default this user is the same as Administrator, without the capability to manage plugins or change themes
Version: 1.3.2
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
if ( !class_exists( 'TD_WebmasterUserRole' ) ) {
	class TD_WebmasterUserRole {

		/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/

		const name = 'Webmaster User Role';

		const slug = 'td-webmaster-user-role';

		const version = '1.3.1';

		const file = __FILE__;

		private $default_options = array(
			'role_display_name' => 'Admin',
			'cap_gravityforms_view_entries' => 1,
			'cap_gravityforms_edit_forms' => 0,
		);

		private $pro;

		/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

		/**
		 * Initializes the plugin by setting localization, filters, and administration functions.
		 */
		function __construct() {

			load_plugin_textdomain( 'td-webmaster-user-role', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

			// Load JavaScript and stylesheets
	    	add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ), 11 );
			add_action( 'wpmu_new_blog', array( $this, 'add_role_to_blog' ) );
			add_action( 'updated_'.self::slug.'_option', array( $this, 'updated_option' ), 10, 3 );
			add_action( 'deleted_'.self::slug.'_option', array( $this, 'deleted_option' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ), 999 );
			add_action( 'admin_init', array( &$this, 'create_role_if_missing' ), 10 );
			add_action( 'admin_init', array( &$this, 'prevent_network_admin_access' ), 10 );
			add_action( 'admin_init', array( &$this, 'cleanup_dashboard_widgets' ), 20 );
			$site_version = get_site_option( 'td-webmaster-user-role-version' );
			if( $site_version!=self::version ) {
				$this->deactivate( is_multisite() );
				$this->activate( is_multisite() );
				update_site_option( 'td-webmaster-user-role-version', self::version );
			}

			require_once( dirname( __FILE__ ). '/includes/updater.php' );
			new TD_WebmasterUserRoleUpdater( $this );

			/* Load Core Modules */
			include_once( dirname( __FILE__ ). '/includes/module-plugins.php' );
			new TDWUR_Plugins( $this );
			include_once( dirname( __FILE__ ). '/includes/module-themes.php' );
			new TDWUR_Themes( $this );
			include_once( dirname( __FILE__ ). '/includes/module-users.php' );
			new TDWUR_Users( $this );
			include_once( dirname( __FILE__ ). '/includes/module-tools.php' );
			new TDWUR_Tools( $this );

			/* Load 3rd Party Modules */
			include_once( dirname( __FILE__ ). '/includes/module-acf.php' );
			new TDWUR_ACF( $this );
			include_once( dirname( __FILE__ ). '/includes/module-cf7.php' );
			new TDWUR_Cf7( $this );
			include_once( dirname( __FILE__ ). '/includes/module-event-espresso.php' );
			new TDWUR_Event_Espresso( $this );
			include_once( dirname( __FILE__ ). '/includes/module-events-calendar.php' );
			new TDWUR_Events_Calendar( $this );
			include_once( dirname( __FILE__ ). '/includes/module-gravity-forms.php' );
			new TDWUR_Gravity_Forms( $this );
			include_once( dirname( __FILE__ ). '/includes/module-itsec.php' );
			new TDWUR_Itsec( $this );
			include_once( dirname( __FILE__ ). '/includes/module-sgcachepress.php' );
			new TDWUR_SGCachePress( $this );
			include_once( dirname( __FILE__ ). '/includes/module-wpai.php' );
			new TDWUR_WPAI( $this );
			include_once( dirname( __FILE__ ). '/includes/module-yoast.php' );
			new TDWUR_Yoast( $this );
		} // end constructor

		function activate( $network_wide ) {
			if ( $network_wide ) {
				$blogs = $this->_blogs();
				foreach ( $blogs as $blog_id ) {
					switch_to_blog( $blog_id );
					$capabilities = $this->capabilities();
					add_role( 'webmaster', $this->get_option( 'role_display_name' ), $capabilities );
					restore_current_blog();
				}

			} else {
				$capabilities = $this->capabilities();
				add_role( 'webmaster', $this->get_option( 'role_display_name' ), $capabilities );
			}
		}
		function deactivate( $network_wide ) {
			if ( $network_wide ) {
				$blogs = $this->_blogs();
				foreach ( $blogs as $blog_id ) {
					switch_to_blog( $blog_id );
					remove_role( 'webmaster' );
					restore_current_blog();
				}

			} else {
				remove_role( 'webmaster' );
			}
		}


		/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

		public static function current_user_is_webmaster() {
			if ( is_super_admin() ) return false;
			return current_user_can( 'webmaster' );
		}

		function capabilities() {
			$admin_role = get_role( 'administrator' );
			$capabilities = $admin_role->capabilities;
			unset( $capabilities['level_10'] );
			unset( $capabilities['update_core'] );
			unset( $capabilities['install_plugins'] );
			unset( $capabilities['activate_plugins'] );
			unset( $capabilities['update_plugins'] );
			unset( $capabilities['edit_plugins'] );
			unset( $capabilities['delete_plugins'] );
			unset( $capabilities['install_themes'] );
			unset( $capabilities['update_themes'] );
			unset( $capabilities['switch_themes'] );
			unset( $capabilities['edit_themes'] );
			unset( $capabilities['delete_themes'] );
			unset( $capabilities['list_users'] );
			unset( $capabilities['create_users'] );
			unset( $capabilities['add_users'] );
			unset( $capabilities['edit_users'] );
			unset( $capabilities['delete_users'] );
			unset( $capabilities['remove_users'] );
			unset( $capabilities['promote_users'] );

			/* Add Gravity Forms Capabilities */
			$capabilities['gravityforms_view_entries'] = $this->get_option( 'cap_gravityforms_view_entries' );
			$capabilities['gravityforms_edit_forms'] = $this->get_option( 'cap_gravityforms_edit_forms' );

			/* Add TablePress Capabilities */
			$capabilities['tablepress_list_tables'] = 1;
			$capabilities['tablepress_add_tables'] = 1;
			$capabilities['tablepress_edit_tables'] = 1;
			$capabilities['tablepress_import_tables'] = 1;
			$capabilities['tablepress_export_tables'] = 1;
			$capabilities['tablepress_access_about_screen'] = 1;
			$capabilities['tablepress_access_options_screen'] = 0;

			/* Add WooCommerce Capabilities */
			$woo_caps = $this->get_woocommerce_capabilities();
			foreach ( $woo_caps as $woo_cap_key => $woo_cap_array ) {
				foreach ($woo_cap_array as $key => $woo_cap) {
					$capabilities[$woo_cap] = 1;
				}
			}
			// $capabilities['manage_woocommerce'] = 0;

			$capabilities['editor'] = 1; // Needed for 3rd party plugins that check explicitly for the "editor" role (looking at you NextGen Gallery)

			if ( is_multisite() ) {
				$capabilities['administrator'] = 1;
				$capabilities['level_10'] = 1;
			}

			global $webmaster_user_role_config;
			if ( !empty ( $webmaster_user_role_config ) ) {
				foreach ($webmaster_user_role_config as $config_key => $config_value) {
					if ( strpos( $config_key, 'webmaster_cap') !== false && is_array( $config_value ) ) {
						$capabilities = wp_parse_args( $config_value, $capabilities );
					}
				}				
			}

			$capabilities = apply_filters( 'td_webmaster_capabilities', $capabilities );
			return $capabilities;
		}

		public function get_woocommerce_capabilities() {
			$capabilities = array();

			$capabilities['core'] = array(
				'manage_woocommerce',
				'view_woocommerce_reports'
			);

			$capability_types = array( 'product', 'shop_order', 'shop_coupon' );

			foreach ( $capability_types as $capability_type ) {

				$capabilities[ $capability_type ] = array(
					// Post type
					"edit_{$capability_type}",
					"read_{$capability_type}",
					"delete_{$capability_type}",
					"edit_{$capability_type}s",
					"edit_others_{$capability_type}s",
					"publish_{$capability_type}s",
					"read_private_{$capability_type}s",
					"delete_{$capability_type}s",
					"delete_private_{$capability_type}s",
					"delete_published_{$capability_type}s",
					"delete_others_{$capability_type}s",
					"edit_private_{$capability_type}s",
					"edit_published_{$capability_type}s",

					// Terms
					"manage_{$capability_type}_terms",
					"edit_{$capability_type}_terms",
					"delete_{$capability_type}_terms",
					"assign_{$capability_type}_terms"
				);
			}

			return $capabilities;
		}

		function create_role_if_missing() {
			$wp_roles = new WP_Roles();
			if ( $wp_roles->is_role( 'webmaster' ) ) return;

			$this->deactivate( is_multisite() );
			$this->activate( is_multisite() );
		}

		function prevent_network_admin_access() {
			if ( is_network_admin() && !is_super_admin( get_current_user_id() ) ) {
				wp_redirect( admin_url( ) );
				exit();
			}
		}

		function cleanup_dashboard_widgets() {
			if ( $this->current_user_is_webmaster() ) {
				// remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
				remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
				remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
			}
		}

		function admin_menu() {
			if ( $this->current_user_is_webmaster() ) {
				global $webmaster_user_role_config;
				remove_menu_page( 'branding' );
				if ( is_object( $webmaster_user_role_config ) && empty( $webmaster_user_role_config->sections ) ) return;

				if ( empty ( $webmaster_user_role_config['webmaster_admin_menu_tools_settings']['options-general.php'] ) ) remove_menu_page( 'options-general.php' );
				if ( empty ( $webmaster_user_role_config['webmaster_admin_menu_sucuri']['sucuriscan'] ) ) remove_menu_page( 'sucuriscan' );
				if ( empty ( $webmaster_user_role_config['webmaster_admin_menu_tools_settings']['tools.php'] ) ) remove_menu_page( 'tools.php' );
			}
		}

		function add_role_to_blog( $blog_id ) {
			switch_to_blog( $blog_id );
			$capabilities = $this->capabilities();
			add_role( 'webmaster', 'Admin', $capabilities );
			restore_current_blog();
		}

		function updated_option( $option, $oldvalue, $newValue ) {
			if ( $option=='role_display_name' || strpos( 'cap_', $option )!==false ) {
				$this->deactivate( is_multisite() );
				$this->activate( is_multisite() );
			}
		}

		function deleted_option( $option ) {
			if ( $option=='role_display_name' || strpos( 'cap_', $option )!==false ) {
				$this->deactivate( is_multisite() );
				$this->activate( is_multisite() );
			}
		}

		function get_option( $option ) {
			// Allow plugins to short-circuit options.
			$pre = apply_filters( 'pre_'.self::slug.'_option_' . $option, false );
			if ( false !== $pre )
				return $pre;

			$option = trim( $option );
			if ( empty( $option ) )
				return false;

			$saved_options = get_option( self::slug.'_options' );

			if ( isset( $saved_options[$option] ) ) {
				$value = $saved_options[$option];
			} else {
				$saved_options = ( empty( $saved_options ) ) ? array() : $saved_options;
				$saved_options = array_merge( $this->default_options, $saved_options );
				$value = $saved_options[$option];
			}

			return apply_filters( self::slug.'option_' . $option, $value );
		}

		function update_option( $option, $newValue ) {
			$option = trim( $option );
			if ( empty( $option ) )
				return false;

			if ( is_object( $newvalue ) )
				$newvalue = clone $newvalue;

			$oldvalue = $this->get_option( $option );
			$newvalue = apply_filters( 'pre_update_'.self::slug.'_option_' . $option, $newvalue, $oldvalue );

			// If the new and old values are the same, no need to update.
			if ( $newvalue === $oldvalue )
				return false;

			$_newvalue = $newvalue;
			$newvalue = maybe_serialize( $newvalue );

			do_action( 'update_'.self::slug.'_option', $option, $oldvalue, $_newvalue );

			$options = get_option( self::slug.'_options' );
			if ( empty( $options ) ) $options = array( $option => $newValue );
			else $options[$option] = $newValue;
			update_option( self::slug.'_options', $options );

			do_action( "update_".self::slug."_option_{$option}", $oldvalue, $_newvalue );
			do_action( 'updated_'.self::slug.'_option', $option, $oldvalue, $_newvalue );

			return true;
		}

		function delete_option( $option ) {
			do_action( 'delete_'.self::slug.'_option', $option );
			$options = get_option( self::slug.'_options' );
			if ( !isset( $options[$option] ) ) return false;
			unset( $options[$option] );

			$result = update_option( self::slug.'_options', $options );

			if ( $result ) {
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
			AND spam = '0'
			AND deleted = '0'
			AND archived = '0'
			ORDER BY registered DESC
		", $wpdb->siteid ) );

			return $blogs;
		}

		/**
		 * Registers and enqueues stylesheets for the administration panel and the
		 * public facing site.
		 */
		public function register_scripts_and_styles() {
			if ( is_admin() ) {
				// $this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
				$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
			} else {
				// $this->load_file( self::slug . '-script', '/js/widget.js', true );
				// $this->load_file( self::slug . '-style', '/css/widget.css' );
			} // end if/else
		} // end register_scripts_and_styles

		/**
		 * Helper function for registering and enqueueing scripts and styles.
		 *
		 * @name The  ID to register with WordPress
		 * @file_path  The path to the actual file
		 * @is_script  Optional argument for if the incoming file_path is a JavaScript source file.
		 */
		private function load_file( $name, $file_path, $is_script = false ) {

			$url = plugins_url( $file_path, __FILE__ );
			$file = plugin_dir_path( __FILE__ ) . $file_path;

			if ( file_exists( $file ) ) {
				if ( $is_script ) {
					wp_register_script( $name, $url, array( 'jquery' ) );
					wp_enqueue_script( $name );
				} else {
					wp_register_style( $name, $url );
					wp_enqueue_style( $name );
				} // end if
			} // end if

		} // end load_file

	} // end class
} // end class_exists()
if ( !isset( $td_webmaster_user_role ) ) $td_webmaster_user_role = new TD_WebmasterUserRole();
register_activation_hook( __FILE__, array( $td_webmaster_user_role, 'activate' ) );
register_deactivation_hook( __FILE__, array( $td_webmaster_user_role, 'deactivate' ) );

?>

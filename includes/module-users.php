<?php
class TDWUR_Users {
	public $parent;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );
		add_filter( 'editable_roles' , array( $this, 'remove_adminstrator_from_editable_roles' ) );
	}

	function is_active() {
		return true; // WP Core functionality, users is always present
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image users',
			'title'     => __('Users', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_caps_users',
					'type'      => 'checkbox',
					'title'     => __('User Capabilities', 'redux-framework-demo'),
					'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

					'options'   => array(
						'list_users' => 'List Users',
						'create_users' => 'Create Users',
						'edit_users' => 'Edit Users',
						'delete_users' => 'Delete Users',
					),
					
					'default'   => array(
						'list_users' => '0',
						'create_users' => '0',
						'delete_users' => '0',
						'promote_users' => '0',
						'edit_users' => '0',
						'remove_users' => '0',
					)
				),

			)
		);

		if ( is_multisite() ) {
			$this->section['fields']['0']['options'] = array(
				'list_users' => 'View list of users on their site',
				'promote_users' => 'Add existing users (must be existing users on the network)',
				'remove_users' => 'Remove users from their site',
			);

			$this->section['fields']['0']['desc'] = '
				<p><strong>Notes for Multisite:</strong></p>
				<p>WordPress core code only allows designated "Super Admins" to Create new Users, Edit Users, and Delete Users from the Network.</p>
				<p>Blog/Site administrators are only able to add or remove existing users for their site.</p>
				<p>Due to these core restrictions, the Webmaster role won\'t be able to create brand new users for the network. This is actually not possible for a full Administrator-level user either, unless you add them as a Super Admin with the ability to administer the entire Network.</p>
				<p><a href="http://codex.wordpress.org/Create_A_Network" target="_blank">Learn More about WordPress Multisite</a></p>';
		}

		$sections[] = $this->section;

		return $sections;
	}

	function remove_adminstrator_from_editable_roles( $roles ){
		if ( !TD_WebmasterUserRole::current_user_is_webmaster() ) return $roles;

		if ( isset( $roles['administrator'] ) ){
			unset( $roles['administrator'] );
		}
		return $roles;
	}

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;

		if ( is_multisite() ) {
			$capabilities['add_users'] = (int)$webmaster_user_role_config['webmaster_caps_users']['promote_users'];
		}

		return $capabilities;
	}



}
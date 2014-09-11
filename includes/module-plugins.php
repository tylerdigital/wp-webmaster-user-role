<?php
class TDWUR_Plugins {
	public $parent;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );
	}

	function is_active() {
		return true; // WP Core functionality, plugins is always present
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image plugins',
			'title'     => __('Plugins', 'webmaster-plugin-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_caps_plugins',
					'type'      => 'checkbox',
					'title'     => __('Plugin Capabilities', 'redux-framework-demo'),
					'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

					'options'   => array(
						'install_plugins' => 'Install Plugins',
						'activate_plugins' => 'Activate/Deactivate Plugins',
						'update_plugins' => 'Update Plugins',
						'edit_plugins' => 'Edit Plugins (Plugins > Editor menu item)',
						'delete_plugins' => 'Delete Plugins',
					),

					'default'   => array(
						'install_plugins' => '0',
						'activate_plugins' => '0',
						'update_plugins' => '0',
						'edit_plugins' => '0',
						'delete_plugins' => '0',
					)
				),
			)
		);

		if ( is_multisite() ) {
			$this->section['fields']['0']['options'] = array(
				'activate_plugins' => 'Activate/Deactivate Plugins',
			);

			$this->section['fields']['0']['desc'] = '
			<p><strong>Notes for Multisite:</strong></p>
			<p>WordPress core code only allows designated "Super Admins" to manage plugins for the entire network</p>
			<p>Blog/Site admins can only activate/deactivate plugins installed by the network administrator</p>';
		}

		$sections[] = $this->section;

		return $sections;
	}

	

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;

		if ( is_multisite() ) {
			$capabilities['manage_network_plugins'] = (int)$webmaster_user_role_config['webmaster_caps_plugins']['activate_plugins'];
		}

		return $capabilities;
	}



}
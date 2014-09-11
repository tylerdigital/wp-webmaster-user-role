<?php
class TDWUR_Themes {
	public $parent;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		// add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );
	}

	function is_active() {
		return true; // WP Core functionality, plugins is always present
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image themes',
			'title'     => __('Themes', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_caps_themes',
					'type'      => 'checkbox',
					'title'     => __('Theme Capabilities', 'redux-framework-demo'),
					'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

					'options'   => array(
						'install_themes' => 'Install Themes',
						'update_themes' => 'Update Themes',
						'switch_themes' => 'Switch Active Theme',
						'edit_themes' => 'Edit Themes',
						'delete_themes' => 'Delete Themes',
					),

					'default'   => array(
						'install_themes' => '0',
						'update_themes' => '0',
						'switch_themes' => '0',
						'edit_themes' => '0',
						'delete_themes' => '0',
					)
				),
			)
		);

		if ( is_multisite() ) {
			$this->section['fields']['0']['options'] = array(
				'switch_themes' => 'Switch Active Theme',
			);

			$this->section['fields']['0']['desc'] = '
				<p><strong>Notes for Multisite:</strong></p>
				<p>WordPress core code only allows designated "Super Admins" to manage themes for the entire network</p>
				<p>Blog/Site admins can only activate/deactivate themes installed by the network administrator</p>';
		}

		$sections[] = $this->section;

		return $sections;
	}

	

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;

		// if ( is_multisite() ) {
		// 	$capabilities['manage_network_themes'] = 0;// (int)$webmaster_user_role_config['webmaster_caps_plugins']['activate_plugins'];
		// }

		return $capabilities;
	}



}
<?php
class TDWUR_Tools {
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
			'icon'      => 'wp-menu-image tools',
			'title'     => __('Tools & Settings', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_admin_menu_tools_settings',
					'type'      => 'checkbox',
					'title'     => __('Visible in Menu', 'redux-framework-demo'),
					'subtitle'  => __('Webmaster users can view', 'redux-framework-demo'),
					
					'options'   => array(
						'tools.php' => 'Tools Menu',
						'options-general.php' => 'Settings Menu',
					),
					
					'default'   => array(
						'tools.php' => '0',
						'options-general.php' => '0',
					)
				),

			)
		);

		if ( is_multisite() ) {

		}

		$sections[] = $this->section;

		return $sections;
	}

	

	function capabilities( $capabilities ) {
		global $webmaster_plugin_role_config;

		return $capabilities;
	}



}
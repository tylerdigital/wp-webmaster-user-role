<?php
class TDWUR_WPAI {
	public $parent;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
	}

	function is_active() {
		return ( class_exists( 'PMXI_Plugin' ) );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$sections[] = array(
			'icon'      => 'wp-menu-image tools',
			'title'     => __('WP All Import', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_wpai_metabox_settings',
					'type'      => 'checkbox',
					'title'     => __('WP All Import Capabilities', 'webmaster-user-role'),
					'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

					'options'   => array(
						'wpai_settings' => __('Use WP All Import Settings Menu', 'webmaster-user-role'),
					),
					
					'default'   => array(
						'wpai_settings' => '0',
					)
				),
			)
		);

		return $sections;
	}

	function admin_menu() {
		if ( !TD_WebmasterUserRole::current_user_is_webmaster() ) return;

		global $webmaster_user_role_config;
		if ( is_array( $webmaster_user_role_config ) && empty ( $webmaster_user_role_config['webmaster_wpai_metabox_settings']['wpai_settings'] ) ) {
			remove_menu_page( 'pmxi-admin-home' );
		}
	}
}
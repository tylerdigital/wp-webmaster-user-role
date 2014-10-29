<?php
class TDWUR_SGCachePress {
	public $parent;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
	}

	function is_active() {
		return ( class_exists( 'SG_CachePress' ) );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$sections[] = array(
			'icon'      => 'wp-menu-image tools',
			'title'     => __('SiteGround Caching', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_sgcachepress_metabox_settings',
					'type'      => 'checkbox',
					'title'     => __('SiteGround Caching Capabilities', 'webmaster-user-role'),
					'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

					'options'   => array(
						'sgcachepress_settings' => __('Use SiteGround SuperCacher Settings Menu', 'webmaster-user-role'),
					),
					
					'default'   => array(
						'sgcachepress_settings' => '0',
					)
				),
			)
		);

		return $sections;
	}

	function admin_menu() {
		if ( !TD_WebmasterUserRole::current_user_is_webmaster() ) return;

		global $webmaster_user_role_config;
		if ( is_array( $webmaster_user_role_config ) && empty ( $webmaster_user_role_config['webmaster_sgcachepress_metabox_settings']['sgcachepress_settings'] ) ) {
			remove_menu_page( 'sg-cachepress' );
		}
	}
}
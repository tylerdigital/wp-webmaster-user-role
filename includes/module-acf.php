<?php
class TDWUR_ACF {
	public $parent;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
	}

	function is_active() {
		return ( function_exists( 'get_field' ) );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$sections[] = array(
			'icon'      => 'wp-menu-image tools',
			'title'     => __('Advanced Custom Fields', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_admin_menu_acf',
					'type'      => 'checkbox',
					'title'     => __('Advanced Custom Fields', 'webmaster-user-role'),
					'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

					'options'   => array(
						'acf_menu' => __('Manage Custom Fields', 'webmaster-user-role'),
					),
					
					'default'   => array(
						'acf_menu' => '0',
					)
				),
			)
		);

		return $sections;
	}

	function admin_menu() {
		if ( !$this->is_active() ) return;
		if ( !TD_WebmasterUserRole::current_user_is_webmaster() ) return;

		global $webmaster_user_role_config;
		if ( ! ( is_array( $webmaster_user_role_config ) && isset( $webmaster_user_role_config['webmaster_admin_menu_acf']['acf_menu'] ) && !empty( $webmaster_user_role_config['webmaster_admin_menu_acf']['acf_menu'] ) ) ) {
			remove_menu_page( 'edit.php?post_type=acf' );
			remove_menu_page( 'edit.php?post_type=acf-field-group' );
		}
	}
}
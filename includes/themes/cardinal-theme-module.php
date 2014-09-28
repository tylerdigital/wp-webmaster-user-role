<?php
class TDWUR_Cardinal_Theme {
	private $active;

	function __construct() {
		$this->parent = $parent;

		add_filter( 'webmaster_supported_theme', array( $this, 'is_supported_theme' ) );
		add_filter( 'webmaster_supported_theme_setting_fields', array( $this, 'setting_fields' ) );

	}

	function is_supported_theme( $supported ) {
		if ( $supported ) return $supported;

		return $this->is_active();
	}

	function is_active() {
		if ( $this->active ) return true;

		$current_theme = wp_get_theme();
		if ( $current_theme->Name=='cardinal' || $current_theme->Template=='cardinal' ) {
			$this->active = true;
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 100 );

			return true;
		}

		return false;
	}

	function setting_fields( $fields = array() ) {
		if ( ! $this->is_active() ) return $fields;

		$fields = array();
		$fields[] = array(
			'id'        => 'cardinal_theme_settings',
			'type'      => 'checkbox',
			'title'     => __('Cardinal Theme Compatibility', 'webmaster-user-role'),
			'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

			'options'   => array(
				'access_theme_options_panel' => 'Access Theme Options panel',
			),

			'default'   => array(
				'access_theme_options_panel' => '0',
			)
		);

		return $fields;
	}

	function admin_menu() {
		global $webmaster_user_role_config;
		if ( !is_array( $webmaster_user_role_config ) ) return;

		if ( empty( $webmaster_user_role_config['cardinal_theme_settings']['access_theme_options_panel'] ) ) remove_menu_page( '_sf_options' );
	}

}
new TDWUR_Cardinal_Theme();
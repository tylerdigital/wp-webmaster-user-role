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
					'title'     => __('Theme Capabilities', 'webmaster-user-role'),
					'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

					'options'   => array(
						'edit_theme_options' => 'Manage Theme, Widgets, Menus, and Customizer',
						'install_themes' => 'Install Themes',
						'update_themes' => 'Update Themes',
						'switch_themes' => 'Switch Active Theme',
						'edit_themes' => 'Edit Themes',
						'delete_themes' => 'Delete Themes',
					),

					'default'   => array(
						'edit_theme_options' => '1',
						'install_themes' => '0',
						'update_themes' => '0',
						'switch_themes' => '0',
						'edit_themes' => '0',
						'delete_themes' => '0',
					)
				),
			)
		);

		$supported_theme = apply_filters( 'webmaster_supported_theme', false );
		if ( $supported_theme ) {
			$supported_theme_setting_fields = apply_filters( 'webmaster_supported_theme_setting_fields', array() );
			$this->section['fields'] = array_merge( $this->section['fields'], $supported_theme_setting_fields );
		} else {
			$this->add_unsupported_theme_settings_fields();
		}

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

	function add_unsupported_theme_settings_fields() {
		$this->section['fields'][] = array(
			'id'        => 'unsupported_theme_settings',
			'type'      => 'checkbox',
			'title'     => __('3rd Party Theme Compatibility', 'webmaster-user-role'),
			'subtitle'  => __('Webmaster users can', 'webmaster-user-role'),

			'options'   => array(
				'access_theme_options_panel' => 'Access Theme Options panel (see notes)',
			),
			'desc' 		=> __(
				'<p><strong>Note: Your theme is not specifically supported, but it can be!</strong></p>
				 <p>Many themes create custom panels for Theme Options. Unfortunately each theme is different and requires a custom integration.</p>
				 <p>If you\'d like to see your theme supported, please <strong>email us at <a href="mailto:support@tylerdigital.com">support@tylerdigital.com</a></strong>
				 and we\'ll be happy to add support for it!</p>
				 <p><strong>On unsupported themes, this checkbox will try to control access to the Theme Options panel,
				 but in most cases you will need to submit a support request to add support for your theme.</strong></p>',
				 'webmaster-user-role'
			),

			'default'   => array(
				'access_theme_options_panel' => '0',
			)
		);
	}

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;

		// if ( is_multisite() ) {
		// 	$capabilities['manage_network_themes'] = 0;// (int)$webmaster_user_role_config['webmaster_caps_plugins']['activate_plugins'];
		// }

		return $capabilities;
	}



}
<?php
class TDWUR_Cf7 {
	public $parent;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		// add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );
		add_filter( 'wpcf7_map_meta_cap', array( $this, 'wpcf7_map_meta_cap' ) );
	}

	function is_active() {
		return ( defined( 'WPCF7_VERSION' ) || function_exists( 'wpcf7' ) );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$sections[] = $this->section = array(
			'icon'      => 'wp-menu-image dashicons dashicons-list-view',
			'title'     => __('Contact Form 7', 'webmaster-user-role'),
			'fields'    => array(
				array(
					'id'        => 'webmaster_caps_cf7',
					'type'      => 'checkbox',
					'title'     => __('Contact Form 7', 'redux-framework-demo'),
					'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

					'options'   => array(
						'wpcf7_read_contact_forms' => 'Read contact forms',
						'wpcf7_edit_contact_forms' => 'Create new contact forms',
						'wpcf7_edit_contact_form' => 'Edit contact forms',
						'wpcf7_delete_contact_form' => 'Delete contact forms',
					),
					
					'default'   => array(
						'wpcf7_read_contact_forms' => '1',
						'wpcf7_edit_contact_forms' => '0',
						'wpcf7_edit_contact_form' => '0',
						'wpcf7_delete_contact_form' => '0',
					)
				),
			)
		);

		return $sections;
	}

	function wpcf7_map_meta_cap( $meta_caps ) {
		/* Prevent infinite loop triggered by current_user_can('webmaster') */
		remove_filter( 'wpcf7_map_meta_cap', array( $this, 'wpcf7_map_meta_cap' ) );

		if ( empty( $this->section['fields']['0']['default'] ) ) return $meta_caps;
		if ( !TD_WebmasterUserRole::current_user_is_webmaster() ) return $meta_caps;
		global $webmaster_user_role_config;
		if ( !isset( $webmaster_user_role_config['webmaster_caps_cf7'] ) ) return $meta_caps;
		if ( !$this->is_active() ) return $meta_caps;
		
		foreach ($this->section['fields']['0']['default'] as $cap => $default_value) {
			$value = ( isset( $webmaster_user_role_config['webmaster_caps_cf7'][$cap] ) )
				? $webmaster_user_role_config['webmaster_caps_cf7'][$cap]
				: $default_value;
			if ( !empty( $value ) ) {
				$meta_caps[$cap] = 'webmaster';
			} else {
				$meta_caps[$cap] = 'administrator';
			}
		}

		return $meta_caps;
	}

	// function capabilities( $capabilities ) {
	// 	global $webmaster_user_role_config;
	// 	foreach ($this->section['fields'] as $field) {
	// 		if ( strpos( $field['id'], 'webmaster_cap') !== false && is_array( $field['options'] ) ) {
	// 			foreach ($field['options'] as $cap => $label) {
	// 				$capabilities[$cap] = (int)( !empty( $webmaster_user_role_config[$field['id']] ) );
	// 			}
	// 		}
	// 	}

	// 	return $capabilities;
	// }



}
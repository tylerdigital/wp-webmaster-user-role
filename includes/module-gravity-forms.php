<?php
class TDWUR_Gravity_Forms {
	public $parent;
	public $caps;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );

		// add_filter( 'td_webmaster_capabilities', array( $this, 'default_capabilities' ), 2 );
		add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );

		add_action( 'init', array( $this, 'expose_gf_addon_caps' ), 999 );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
	}

	function is_active() {
		return class_exists( 'GFForms' );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image dashicons dashicons-list-view',
			'title'     => __( 'Gravity Forms', 'webmaster-user-role' ),
			'fields'    => array(
				array(
					'id'        => 'webmaster_caps_gravityforms_forms',
					'type'      => 'checkbox',
					'title'     => __( 'Managing Forms', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

					'options'   => array(
						'gravityforms_edit_forms' => 'List & Edit Forms',
						'gravityforms_create_form' => 'Create & Duplicate Forms',
						'gravityforms_delete_forms' => 'Delete Forms',
						'gravityforms_preview_forms' => 'Preview Forms',
					),

					'default'   => array(
						'gravityforms_create_form' => '0',
						'gravityforms_edit_forms' => '1',
						'gravityforms_delete_forms' => '0',
						'gravityforms_preview_forms' => '0',
					)
				),

				array(
					'id'        => 'webmaster_caps_gravityforms_entries',
					'type'      => 'checkbox',
					'title'     => __( 'Managing Entries (Form Submissions/Data)', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

					'options'   => array(
						'gravityforms_view_entries' => 'View Form Entries',
						'gravityforms_view_entry_note' => 'View Internal Notes on Form Entries',
						'gravityforms_edit_entries' => 'Edit Form Entries',
						'gravityforms_edit_entry_note' => 'Edit Internal Notes on Form Entries',
						'gravityforms_delete_entries' => 'Delete Form Entries',
						'gravityforms_export_entries' => 'Export Form Entries',
					),

					'default'   => array(
						'gravityforms_view_entries' => '1',
						'gravityforms_view_entry_note' => '0',
						'gravityforms_edit_entries' => '0',
						'gravityforms_edit_entry_note' => '0',
						'gravityforms_delete_entries' => '0',
						'gravityforms_export_entries' => '0',
					)
				),


				array(
					'id'        => 'webmaster_caps_gravityforms_advanced',
					'type'      => 'checkbox',
					'title'     => __( 'Advanced Features', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

					'options'   => array(
						'gravityforms_view_settings' => 'View Settings',
						'gravityforms_edit_settings' => 'Edit Settings',
						'gravityforms_uninstall' => 'Uninstall Gravity Forms',
						'gravityforms_view_updates' => 'Show when updates are available',
						'gravityforms_view_addons' => 'Show Add-Ons Available/Installed (User also needs capability to install plugins)',
					),

					'default'   => array(
						'gravityforms_view_settings' => '0',
						'gravityforms_edit_settings' => '0',
						'gravityforms_uninstall' => '0',
						'gravityforms_view_updates' => '0',
						'gravityforms_view_addons' => '0',
					)
				),
			)
		);

		/* MailChimp Add-On */
		if ( class_exists( 'GFMailChimp' ) ) {
			$this->section['fields'][] = array(
				'id'        => 'webmaster_caps_gravityforms_mailchimp',
				'type'      => 'checkbox',
				'title'     => __( 'MailChimp Add-On', 'redux-framework-demo' ),
				'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

				'options'   => array(
					'gravityforms_mailchimp' => 'Manage MailChimp Settings',
				),

				'default'   => array(
					'gravityforms_mailchimp' => '0',
				)
			);
		}
		
		/* Zapier Add-On */
		if ( class_exists( 'GFZapier' ) ) {
			$this->section['fields'][] = array(
				'id'        => 'webmaster_caps_gravityforms_zapier',
				'type'      => 'checkbox',
				'title'     => __( 'Zapier Add-On', 'redux-framework-demo' ),
				'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

				'options'   => array(
					'gravityforms_zapier' => 'Manage Zapier Settings',
				),

				'default'   => array(
					'gravityforms_zapier' => '0',
				)
			);
		}

		/* User Registration Add-On */
		if ( class_exists( 'GFUser' ) ) {
			$this->section['fields'][] = array(
				'id'        => 'webmaster_caps_gravityforms_userregistration',
				'type'      => 'checkbox',
				'title'     => __( 'User Registration Add-On', 'redux-framework-demo' ),
				'subtitle'  => __( 'Webmaster users can', 'redux-framework-demo' ),

				'options'   => array(
					'gravityforms_user_registration' => 'Manage User Registration Settings',
				),

				'default'   => array(
					'gravityforms_user_registration' => '0',
				)
			);
		}

		$sections[] = $this->section;

		return $sections;
	}

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;
		if ( !is_array( $webmaster_user_role_config ) ) return;

		$capabilities['gravityforms_mailchimp_uninstall'] = (int)$webmaster_user_role_config['webmaster_caps_gravityforms_mailchimp']['gravityforms_mailchimp'];

		return $capabilities;
	}

	function expose_gf_addon_caps() {
		/* For some reason Gravity Forms only exposes capabilities for add-ons if the Members plugin is installed & available */
		/* Loading an empty members_get_capabilities() function so Gravity Forms thinks it's activated */
		if ( !function_exists( 'members_get_capabilities' ) ) {
			function members_get_capabilities() {}
		}
	}

	function admin_footer() {
		$screen = get_current_screen();
		if ( $screen->id != 'toplevel_page_gf_edit_forms' ) return;

		global $webmaster_user_role_config;
		if ( !is_array( $webmaster_user_role_config ) ) return;

		if ( !empty( $webmaster_user_role_config['webmaster_caps_gravityforms_forms']['gravityforms_create_form'] ) ) return;
		// Hide "Add New" button since GF didn't tie this into the gravityforms_create_form capability
		?>
		<style type="text/css">
			.toplevel_page_gf_edit_forms .add-new-h2 {
				display: none;
			}
		</style>
		<?php
	}

}
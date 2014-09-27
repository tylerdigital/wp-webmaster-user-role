<?php
class TDWUR_Event_Espresso {
	public $parent;
	public $caps;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		$this->caps = array(
			'management' => 'Manage Settings',
			'espresso_events' => 'Events',
			'espresso_registrations' => 'Registrations',
			'espresso_transactions' => 'Transactions',
			'espresso_messages' => 'Messages',
			'pricing' => 'Pricing',
			'espresso_registration_form' => 'Registration Form',
			'espresso_venues' => 'Venues',
			'espresso_general_settings' => 'General Settings',
			'espresso_support' => 'Help & Support',
			'espresso_payment_settings' => 'Payment Methods',
			'espresso_maintenance_settings' => 'Maintenance',
			'espresso_about' => 'About',
		);
		foreach ($this->caps as $cap => $label) {
			add_filter( 'FHEE_'.$cap.'_capability', array( $this, 'set_webmaster_cap' ) );
		}
	}

	function is_active() {
		return defined( 'EVENT_ESPRESSO_VERSION' );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image dashicons dashicons-list-view',
			'title'     => __( 'Event Espresso', 'webmaster-user-role' ),
			'fields'    => array(
				array(
					'id'        => 'webmaster_event_espresso_settings',
					'type'      => 'checkbox',
					'title'     => __( 'Event Espresso Capabilities', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users can access Event Espresso menu:', 'redux-framework-demo' ),

					'options'   => $this->caps,

					'default'   => array_combine( array_keys( $this->caps ), array_fill( 0, count( $this->caps ), 1 ) )
				),
			)
		);

		$sections[] = $this->section;

		return $sections;
	}

	function set_webmaster_cap( $capability ) {
		if ( TD_WebmasterUserRole::current_user_is_webmaster() ) {
			global $webmaster_user_role_config;
			if ( is_array( $webmaster_user_role_config ) ) {

				$current_filter = current_filter();
				$current_cap = str_replace( array( 'FHEE_', '_capability' ), '', $current_filter );
				if ( $current_cap == 'management' ) {
					$current_cap = 'espresso_events'; // The top-level "management cap" is the same as the Events submenu
				}

				if ( !isset( $webmaster_user_role_config['webmaster_event_espresso_settings'][$current_cap] ) ) {
					$webmaster_user_role_config['webmaster_event_espresso_settings'][$current_cap] = $this->section['fields']['0']['default'][$current_cap];
				}
				if ( (int)$webmaster_user_role_config['webmaster_event_espresso_settings'][$current_cap] ) {
					return 'webmaster';
				}
			}
		}
		return $capability;
	}

}

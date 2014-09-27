<?php
class TDWUR_Events_Calendar {
	public $parent;
	public $caps;
	private $section;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_filter( 'redux/options/webmaster_user_role_config/sections', array( $this, 'settings_section' ) );
		$this->caps = array(
			'events' => array(
				'edit_tribe_event' => 'Manage Events',
				'delete_tribe_event' => 'Delete Events',
			),
			'venues' => array(
				'edit_tribe_venue' => 'Manage Venues',
				'delete_tribe_venue' => 'Delete Venues',
			),
			'organizers' => array(
				'edit_tribe_organizer' => 'Manage Organizers',
				'delete_tribe_organizer' => 'Delete Organizers',
			),
		);

		// add_filter( 'td_webmaster_capabilities', array( $this, 'default_capabilities' ), 2 );
		add_filter( 'td_webmaster_capabilities', array( $this, 'capabilities' ) );
	}

	function is_active() {
		return class_exists( 'TribeEvents' );
	}

	function settings_section( $sections ) {
		if ( !$this->is_active() ) return $sections;

		$this->section = array(
			'icon'      => 'wp-menu-image dashicons dashicons-list-view',
			'title'     => __( 'Events Calendar', 'webmaster-user-role' ),
			'fields'    => array(
				'events' => array(
					'id'        => 'webmaster_events_calendar_events_settings',
					'type'      => 'checkbox',
					'title'     => __( 'Event Capabilities', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users have the following access for Events:', 'redux-framework-demo' ),

					'options'   => $this->caps['events'],

					'default'   => array_combine( array_keys( $this->caps['events'] ), array_fill( 1, count( $this->caps['events'] ), 1 ) )
				),
				'venues' => array(
					'id'        => 'webmaster_events_calendar_venues_settings',
					'type'      => 'checkbox',
					'title'     => __( 'Venue Capabilities', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users have the following access for Venues:', 'redux-framework-demo' ),

					'options'   => $this->caps['venues'],

					'default'   => array_combine( array_keys( $this->caps['venues'] ), array_fill( 1, count( $this->caps['venues'] ), 1 ) )
				),
				'organizers' => array(
					'id'        => 'webmaster_events_calendar_organizers_settings',
					'type'      => 'checkbox',
					'title'     => __( 'Organizers Capabilities', 'redux-framework-demo' ),
					'subtitle'  => __( 'Webmaster users have the following access for Organizers:', 'redux-framework-demo' ),

					'options'   => $this->caps['organizers'],

					'default'   => array_combine( array_keys( $this->caps['organizers'] ), array_fill( 1, count( $this->caps['organizers'] ), 1 ) )
				),
			)
		);

		$sections[] = $this->section;

		return $sections;
	}

	function default_capabilities( $capabilities ) {
		foreach ($this->caps as $cap_cpt => $cap_array) {
			foreach ($cap_array as $cap => $cap_label) {
				if ( !isset( $webmaster_user_role_config['webmaster_events_calendar_'.$cap_cpt.'_settings'][$cap] ) ) {
					$webmaster_user_role_config['webmaster_events_calendar_'.$cap_cpt.'_settings'][$cap] = $this->section['fields'][$cap_cpt]['default'][$cap];
				}
			}
		}

		return $capabilities;
	}

	function capabilities( $capabilities ) {
		global $webmaster_user_role_config;
		if ( !is_array( $webmaster_user_role_config ) ) return;

		/* Fill in events caps */
		$capabilities['read_tribe_event'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];
		$capabilities['read_private_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];
		
		$capabilities['publish_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];

		$capabilities['edit_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];
		$capabilities['edit_published_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];
		$capabilities['edit_others_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];
		$capabilities['edit_private_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['edit_tribe_event'];

		$capabilities['delete_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['delete_tribe_event'];
		$capabilities['delete_published_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['delete_tribe_event'];
		$capabilities['delete_others_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['delete_tribe_event'];
		$capabilities['delete_private_tribe_events'] = (int)$webmaster_user_role_config['webmaster_events_calendar_events_settings']['delete_tribe_event'];


		/* Fill in venues caps */
		$capabilities['read_private_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];
		$capabilities['read_tribe_venue'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];

		$capabilities['publish_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];

		$capabilities['edit_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];
		$capabilities['edit_published_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];
		$capabilities['edit_others_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];
		$capabilities['edit_private_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['edit_tribe_venue'];

		$capabilities['delete_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['delete_tribe_venue'];
		$capabilities['delete_published_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['delete_tribe_venue'];
		$capabilities['delete_others_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['delete_tribe_venue'];
		$capabilities['delete_private_tribe_venues'] = (int)$webmaster_user_role_config['webmaster_events_calendar_venues_settings']['delete_tribe_venue'];


		/* Fill in organizers caps */
		$capabilities['read_private_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];
		$capabilities['read_tribe_organizer'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];

		$capabilities['publish_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];

		$capabilities['edit_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];
		$capabilities['edit_published_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];
		$capabilities['edit_others_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];
		$capabilities['edit_private_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['edit_tribe_organizer'];

		$capabilities['delete_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['delete_tribe_organizer'];
		$capabilities['delete_published_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['delete_tribe_organizer'];
		$capabilities['delete_others_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['delete_tribe_organizer'];
		$capabilities['delete_private_tribe_organizers'] = (int)$webmaster_user_role_config['webmaster_events_calendar_organizers_settings']['delete_tribe_organizer'];

		return $capabilities;
	}

}

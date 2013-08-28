=== Webmaster User Role ===
Contributors: tylerdigital, croixhaug
Tags: admin, users, webmaster, capabilities, administrator, editor, permissions, roles, user roles
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 1.1

Adds a Webmaster user role between Administrator and Editor. Perfect for clients and those who know just enough to be dangerous.

== Description ==
This plugin creates a new role named "Admin" that is the same as "Administrator" with the following changes:

In WP-Admin:
- Hide / Remove Settings menu
- Hide / Remove Plugins menu
- Hide / Remove Tools menu
- Disable theme installation
- Disable theme switching
- Hide / Remove Appearance > Editor
- Disable WP core updates
- Disable capability to add/delete/edit users

3rd party plugin compatibility:

- Gravity Forms (RocketGenius) - user can view form entries but not edit them or create new ones
- Ultimate Branding (WPMU Dev) - hide branding menu
- Sucuri Scanner (Sucuri) - hide security scan information
- Advanced Custom Fields (Elliot Condon) - hide ACF menu, only admins/developers should be modifying ACF definitions/rules/fields

== Installation ==
Install and activate, there are no settings in the UI

== Changelog ==
= v1.1
* Add support for Sucuri Scanner [http://wordpress.org/plugins/sucuri-scanner/]
* Add support for Advanced Custom Fields [http://wordpress.org/plugins/advanced-custom-fields/]
* Remove tools menu – so webmaster users can't import/export/migrate/find&replace

= v1.0.9
* Add support for Ultimate Branding [http://premium.wpmudev.org/project/ultimate-branding/]

= v1.0.8
* Add Gravity Forms edit_forms capability as an option (only allows entry viewing by default) via filter:
add_filter( 'td-webmaster-user-roleoption_cap_gravityforms_edit_forms', __return_true );

= v1.0.7
* Remove settings menu from wp-admin

= v1.0.5
* Remove capability to delete users

= v1.0.4
* Add "editor" cap for role so plugins checking for "editor" explicitly work

= v1.0.3
* Remove capabiilty to add, edit, promote users
* Remove capability to update core

= v1.0.2
* Initial Release
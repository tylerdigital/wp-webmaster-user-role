=== Webmaster User Role ===
Contributors: tylerdigital, croixhaug
Tags: admin, users, webmaster, capabilities, administrator, editor, permissions, roles, user roles
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 1.0.8

Adds a Webmaster user role between Administrator and Editor.  By default this user is the same as Administrator, without the capability to manage plugins or change themes

== Description ==
Adds a Webmaster user role between Administrator and Editor.  By default this user is the same as Administrator, without the capability to manage plugins or change themes.

3rd party plugin compatibility:

- Gravity Forms (user can view form entries but not edit them or create new ones)
- Ultimate Branding (by WPMU Dev) - hide branding menu

== Installation ==
Install and activate, there are no settings in the UI

== Changelog ==
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
=== Webmaster User Role ===
Contributors: tylerdigital, croixhaug
Tags: client, clients, restrict access, access, admin, users, webmaster, capabilities, administrator, editor, permissions, roles, user roles
Requires at least: 3.5
Tested up to: 4.1.1
Stable tag: 1.3.2

Adds a new "Admin" user role between Administrator and Editor. Perfect for clients and those who know just enough to be dangerous.

== Description ==
This plugin creates a new role named "Admin" that is the same as "Administrator" with the following changes:

= In WP-Admin =
* Hide / Remove Settings menu
* Hide / Remove Plugins menu
* Hide / Remove Tools menu
* Hide / Remove Users menu
* Disable theme installation
* Disable theme switching
* Hide / Remove Appearance > Editor
* Disable WP core updates
* Hide non-essential dashboard items

= 3rd party plugin compatibility =

* Advanced Custom Fields (Elliot Condon) - hide ACF menu, only admins/developers should be modifying ACF definitions/rules/fields
* Contact Form 7 - user can only read contact form submissions
* Gravity Forms (RocketGenius) - user can view form entries but not edit forms or create new ones
* iThemes Security - hide iThemes security menus
* Sucuri Scanner (Sucuri) - hide security scan information
* TablePress - Show/Edit/Import/Export TablePress content (all tabs except plugin options)
* Ultimate Branding (WPMU Dev) - hide branding menu
* Yoast SEO - hide Yoast SEO settings menu (also hide the SEO metabox when editing pages/posts by upgrading to Webmaster User Role Pro)

[Upgrade to the Pro version](http://tylerdigital.com/products/webmaster-user-role/) of this plugin to get a settings panel to customize the admin dashboard specifically for your client:

[embed http://tylerdigital.wistia.com/medias/ur07v1vnu5?embedType=iframe&videoFoam=true&videoWidth=700]

[Learn more about Webmaster User Role Pro](http://tylerdigital.com/products/webmaster-user-role/)

== Screenshots ==

1. Adds a role to fit nicely between Administrator and Editor
2. Easily remove administrative menu items, while leaving content-related items like Menus and Wigets
3. Gravity Forms Settings (Pro version only)
4. Yoast SEO Settings (Pro version only)
5. Appearance/Themes Settings (Pro version only)

== Installation ==
Install and activate, there are no settings in the free version of this plugin. Webmaster User Role is built with a carefully chosen set of permissions intended for the majority of clients. There are many free options available for further tweaking user permissions or editing the admin menu. We also have a [Pro version](http://tylerdigital.com/products/webmaster-user-role/) that makes it easy to customize for your clients.

== Changelog ==
= v 1.3.2 =
* Improve support for new Gravity Forms version
* Fix register_scripts_and_styles PHP warning
* Fix potential issue with webmaster user being given no access permissions

= v 1.3.1 =
* Show Yoast metabox when editing pages (still hide settings) by default

= v 1.3 =
* Hide Users menu (often requested, and really the webmaster user couldn't do much in this screen anyway)
* Add support for ACF5 (hide Custom Fields Menu compatible with new version)
* Add support for Contact Form 7
* Add support for iThemes Security

= v 1.21 =
* Add support for TablePress [http://wordpress.org/plugins/tablepress/]

= v 1.2 =
* Multisite bugfix: Prevent webmaster from removing users from individual sites
* Multisite bugfix: Stop removing Settings & Tools from network administrator

= v1.1.1 =
* Now hides non-essential dashboard items
* Fixes a conflict with Mizzo theme (thanks djesch)

= v1.1 =
* Add support for Sucuri Scanner [http://wordpress.org/plugins/sucuri-scanner/]
* Add support for Advanced Custom Fields [http://wordpress.org/plugins/advanced-custom-fields/]
* Remove tools menu – so webmaster users can't import/export/migrate/find&replace

= v1.0.9 =
* Add support for Ultimate Branding [http://premium.wpmudev.org/project/ultimate-branding/]

= v1.0.8 =
* Add Gravity Forms edit_forms capability as an option (only allows entry viewing by default) via filter:
add_filter( 'td-webmaster-user-roleoption_cap_gravityforms_edit_forms', __return_true );

= v1.0.7 =
* Remove settings menu from wp-admin

= v1.0.5 =
* Remove capability to delete users

= v1.0.4 =
* Add "editor" cap for role so plugins checking for "editor" explicitly work

= v1.0.3 =
* Remove capabiilty to add, edit, promote users
* Remove capability to update core

= v1.0.2 =
* Initial Release
<?php
if ( !class_exists( 'TD_WebmasterUserRolePro' ) ) {
	class TD_WebmasterUserRolePro {
		public $config;
		public $parent;

		function __construct( $parent ) {
			$this->parent = $parent;
			add_action( 'admin_init', array( $this, 'update_caps' ) );
		}

		function update_caps() { 
			global $webmaster_user_role_config;
			$last_caps_update = get_site_option( 'td-webmaster-last-caps-update' );
			if( $last_caps_update != $webmaster_user_role_config['REDUX_last_saved'] ) {
				$this->parent->deactivate( false );
				$this->parent->activate( false );
				update_site_option( 'td-webmaster-last-caps-update', $webmaster_user_role_config['REDUX_last_saved'] );
			}
		}
	}
}

if (!class_exists('Redux_Webmaster_User_Role_Config')) {

	class Redux_Webmaster_User_Role_Config {

		public $args        = array();
		public $sections    = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( dirname( __FILE__ ) ) . '/lib/redux/ReduxCore/framework.php' ) ) {
				require_once( dirname( dirname( __FILE__ ) ) . '/lib/redux/ReduxCore/framework.php' );
			}

			// This is needed. Bah WordPress bugs.  ;)
			if (  true == Redux_Helpers::isTheme(__FILE__) ) {
				$this->initSettings();
			} else {
				add_action('plugins_loaded', array($this, 'initSettings'), 10);
			}

		}

		public function initSettings() {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if (!isset($this->args['opt_name'])) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			//add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
			
			// Function to test the compiler hook and demo CSS output.
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
			
			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			
			// Change the default value of a field after it's been set, but before it's been useds
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
			
			// Dynamically add a section. Can be also used to modify sections/fields
			//add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
		}

		/**

		  This is a test function that will let you see when the compiler hook occurs.
		  It only runs if a field	set with compiler=>true is changed.

		 * */
		function compiler_action($options, $css) {
			//echo '<h1>The compiler hook has run!';
			//print_r($options); //Option values
			//print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

			/*
			  // Demo of how to use the dynamic CSS and write your own static CSS file
			  $filename = dirname(__FILE__) . '/style' . '.css';
			  global $wp_filesystem;
			  if( empty( $wp_filesystem ) ) {
				require_once( ABSPATH .'/wp-admin/includes/file.php' );
			  WP_Filesystem();
			  }

			  if( $wp_filesystem ) {
				$wp_filesystem->put_contents(
					$filename,
					$css,
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			  }
			 */
		}

		/**

		  Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		  Simply include this function in the child themes functions.php file.

		  NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		  so you must use get_template_directory_uri() if you want to use any of the built in icons

		 * */
		function dynamic_section($sections) {
			//$sections = array();
			$sections[] = array(
				'title' => __('Section via hook', 'webmaster-user-role'),
				'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'webmaster-user-role'),
				'icon' => 'el-icon-paper-clip',
				// Leave this as a blank section, no options just some intro text set above.
				'fields' => array()
			);

			return $sections;
		}

		/**

		  Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

		 * */
		function change_arguments($args) {
			//$args['dev_mode'] = true;

			return $args;
		}

		/**

		  Filter hook for filtering the default value of any given field. Very useful in development mode.

		 * */
		function change_defaults($defaults) {
			$defaults['str_replace'] = 'Testing filter hook!';

			return $defaults;
		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if (class_exists('ReduxFrameworkPlugin')) {
				remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
			}
		}

		public function setSections() {

			/**
			  Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 * */
			// Background Patterns Reader
			$sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
			$sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
			$sample_patterns        = array();

			if (is_dir($sample_patterns_path)) :

				if ($sample_patterns_dir = opendir($sample_patterns_path)) :
					$sample_patterns = array();

					while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

						if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
							$name = explode('.', $sample_patterns_file);
							$name = str_replace('.' . end($name), '', $sample_patterns_file);
							$sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
						}
					}
				endif;
			endif;

			ob_start();

			$ct             = wp_get_theme();
			$this->theme    = $ct;
			$item_name      = TD_WebmasterUserRole::name;
			$tags           = 'users,capabilities';
			$screenshot     = false; // $this->theme->get_screenshot();
			$class          = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'webmaster-user-role'), TD_WebmasterUserRole::name);
			
			?>
			<div id="current-theme" class="<?php echo esc_attr($class); ?>">
			<?php if ($screenshot) : ?>
				<?php if (current_user_can('edit_theme_options')) : ?>
						<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
							<img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
						</a>
				<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
				<?php endif; ?>

				<h4>Webmaster User Role</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf(__('By %s', 'webmaster-user-role'), $this->theme->display('Author')); ?></li>
						<li><?php printf(__('Version %s', 'webmaster-user-role'), $this->theme->display('Version')); ?></li>
						<li><?php echo '<strong>' . __('Tags', 'webmaster-user-role') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
					</ul>
					<p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
			<?php
			if ($this->theme->parent()) {
				printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'webmaster-user-role'), $this->theme->parent()->display('Name'));
			}
			?>

				</div>
			</div>

			<?php
			$item_info = ob_get_contents();

			ob_end_clean();

			$sampleHTML = '';
			if (file_exists(dirname(__FILE__) . '/info-html.html')) {
				/** @global WP_Filesystem_Direct $wp_filesystem  */
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH . '/wp-admin/includes/file.php');
					WP_Filesystem();
				}
				$sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
			}

			// ACTUAL DECLARATION OF SECTIONS

			$this->sections[] = array(
				'icon'      => 'el-icon-cogs',
				'title'     => __('Plugin Capabilities', 'webmaster-user-role'),
				'fields'    => array(
					array(
						'id'        => 'webmaster_caps_plugins',
						'type'      => 'checkbox',
						'title'     => __('Plugins', 'redux-framework-demo'),
						'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),
						
						'options'   => array(
							'install_plugins' => 'Install Plugins',
							'activate_plugins' => 'Activate Plugins',
							'update_plugins' => 'Update Plugins',
							'edit_plugins' => 'Edit Plugins',
							'delete_plugins' => 'Delete Plugins',
						),
						
						'default'   => array(
							'install_plugins' => '0',
							'activate_plugins' => '0',
							'update_plugins' => '0',
							'edit_plugins' => '0',
							'delete_plugins' => '0',
						)
					),

				)
			);

			$this->sections[] = array(
				'icon'      => 'el-icon-cogs',
				'title'     => __('Theme Capabilities', 'webmaster-user-role'),
				'fields'    => array(
					array(
						'id'        => 'webmaster_caps_themes',
						'type'      => 'checkbox',
						'title'     => __('Themes', 'redux-framework-demo'),
						'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

						'options'   => array(
							'install_themes' => 'Install Themes',
							'update_themes' => 'Activate Themes',
							'switch_themes' => 'Update Themes',
							'edit_themes' => 'Edit Themes',
							'delete_themes' => 'Delete Themes',
						),
						
						'default'   => array(
							'install_themes' => '0',
							'update_themes' => '0',
							'switch_themes' => '0',
							'edit_themes' => '0',
							'delete_themes' => '0',
						)
					),

				)
			);

			$this->sections[] = array(
				'icon'      => 'el-icon-cogs',
				'title'     => __('User Capabilities', 'webmaster-user-role'),
				'fields'    => array(
					array(
						'id'        => 'webmaster_caps_users',
						'type'      => 'checkbox',
						'title'     => __('Users', 'redux-framework-demo'),
						'subtitle'  => __('Webmaster users can', 'redux-framework-demo'),

						'options'   => array(
							'add_users' => 'Install Users',
							'edit_users' => 'Edit Users',
							'delete_users' => 'Delete Users',
						),
						
						'default'   => array(
							'add_users' => '0',
							'edit_users' => '0',
							'delete_users' => '0',
						)
					),

				)
			);


		}

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			// $this->args['help_tabs'][] = array(
			// 	'id'        => 'redux-help-tab-1',
			// 	'title'     => __('Theme Information 1', 'webmaster-user-role'),
			// 	'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'webmaster-user-role')
			// );

			// $this->args['help_tabs'][] = array(
			// 	'id'        => 'redux-help-tab-2',
			// 	'title'     => __('Theme Information 2', 'webmaster-user-role'),
			// 	'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'webmaster-user-role')
			// );

			// // Set the help sidebar
			// $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'webmaster-user-role');
		}

		/**

		  All the possible arguments for Redux.
		  For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

		 * */
		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name'          => 'webmaster_user_role_config',            // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'      => TD_WebmasterUserRole::name,     // Name that appears at the top of your panel
				'display_version'   => TD_WebmasterUserRole::version,  // Version that appears at the top of your panel
				'menu_type'         => 'submenu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
				'menu_title'        => __(TD_WebmasterUserRole::name, 'webmaster-user-role'),
				'page_title'        => __('Webmaster User Role Settings', 'webmaster-user-role'),
				
				// You will need to generate a Google API key to use this feature.
				// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
				'google_api_key' => '', // Must be defined to add google fonts to the typography module
				
				'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
				'admin_bar'         => false,                    // Show the panel pages on the admin bar
				'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
				// 'dev_mode'          => true,                    // Show the time the page took to load, etc
				'customizer'        => false,                    // Enable basic customizer support
				
				// OPTIONAL -> Give you extra features
				'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent'       => 'options-general.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
				'menu_icon'         => '',                      // Specify a custom URL to an icon
				'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
				'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
				'page_slug'         => 'webmaster_user_role',              // Page slug used to denote the panel
				'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
				'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
				'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
				'show_import_export' => false,                   // Shows the Import/Export panel when not used as a field.
				
				// CAREFUL -> These options are for advanced use only
				'transient_time'    => 60 * MINUTE_IN_SECONDS,
				'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
				
				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'system_info'           => false, // REMOVE

				// HINTS
				'hints' => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'         => 'light',
						'shadow'        => true,
						'rounded'       => false,
						'style'         => '',
					),
					'tip_position'  => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'    => array(
						'show'          => array(
							'effect'        => 'slide',
							'duration'      => '500',
							'event'         => 'mouseover',
						),
						'hide'      => array(
							'effect'    => 'slide',
							'duration'  => '500',
							'event'     => 'click mouseleave',
						),
					),
				)
			);


			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			$this->args['share_icons'][] = array(
				'url'   => 'https://github.com/tylerdigital',
				'title' => 'Visit us on GitHub',
				'icon'  => 'el-icon-github'
				//'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/tylerdigital',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el-icon-twitter'
			);

			// Panel Intro text -> before the form
			$this->args['intro_text'] = '<p>'.__('Customize the capabilities for your Webmaster User Role', 'webmaster-user-role').'</p>';

			// Add content after the form.
			$this->args['footer_text'] = '<p>'.__('<a href="http://tylerdigital.com/downloads/webmaster-user-role/" target="_blank">Plugin Documentation</a>', 'webmaster-user-role').'</p>';
		}

	}
	
	global $webmaster_user_role_config;
	$webmaster_user_role_config = new Redux_Webmaster_User_Role_Config();
}
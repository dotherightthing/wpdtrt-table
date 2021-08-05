<?php
/**
 * DTRT Table
 *
 * @package     WPDTRT_Table
 * @author      Dan Smith
 * @copyright   2021 Do The Right Thing
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  DTRT Table
 * Plugin URI:   https://github.com/dotherightthing/wpdtrt-table
 * Description:  Shortcode to author a vertical table with overflow hint and scrollbar.
 * Version:      1.0.1
 * Author:       Dan Smith
 * Author URI:   https://profiles.wordpress.org/&#39;dotherightthingnz
 * License:      GPLv2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wpdtrt-table
 * Domain Path:  /languages
 */

/**
 * Group: Constants
 *
 * Note:
 * - WordPress makes use of the following constants when determining the path to the content and plugin directories.
 *   These should not be used directly by plugins or themes, but are listed here for completeness.
 * - WP_CONTENT_DIR  // no trailing slash, full paths only
 * - WP_CONTENT_URL  // full url
 * - WP_PLUGIN_DIR  // full path, no trailing slash
 * - WP_PLUGIN_URL  // full url, no trailing slash
 * - WordPress provides several functions for easily determining where a given file or directory lives.
 *   Always use these functions in your plugins instead of hard-coding references to the wp-content directory
 *   or using the WordPress internal constants.
 * - plugins_url()
 * - plugin_dir_url()
 * - plugin_dir_path()
 * - plugin_basename()
 *
 * See:
 * - <https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Constants>
 * - <https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Plugins>
 * _____________________________________
 */

if ( ! defined( 'WPDTRT_TABLE_VERSION' ) ) {
	/**
	 * Constant: WPDTRT_TABLE_VERSION
	 *
	 * Plugin version.
	 *
	 * Note:
	 * - WP provides get_plugin_data(), but it only works within WP Admin,
	 *   so we define a constant instead.
	 *
	 * See:
	 * - <https://wordpress.stackexchange.com/questions/18268/i-want-to-get-a-plugin-version-number-dynamically>
	 *
	 * Example:
	 * ---php
	 * $plugin_data = get_plugin_data( __FILE__ ); $plugin_version = $plugin_data['Version'];
	 * ---
	 */
	define( 'WPDTRT_TABLE_VERSION', '1.0.1' );
}

if ( ! defined( 'WPDTRT_TABLE_PATH' ) ) {
	/**
	 * Constant: WPDTRT_TABLE_PATH
	 *
	 * Plugin directory filesystem path (with trailing slash).
	 *
	 * See:
	 * - <https://developer.wordpress.org/reference/functions/plugin_dir_path/>
	 * - <https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything>
	 */
	define( 'WPDTRT_TABLE_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPDTRT_TABLE_URL' ) ) {
	/**
	 * Constant: WPDTRT_TABLE_URL
	 *
	 * Plugin directory URL path (with trailing slash).
	 *
	 * See:
	 * - <https://codex.wordpress.org/Function_Reference/plugin_dir_url>
	 * - <https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything>
	 */
	define( 'WPDTRT_TABLE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Constant: WPDTRT_PLUGIN_CHILD
 *
 * Boolean, used to determine the correct path to the PSR-4 autoloader.
 *
 * See:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51>
 */
if ( ! defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
	define( 'WPDTRT_PLUGIN_CHILD', true );
}

/**
 * Determine the correct path to the PSR-4 autoloader.
 *
 * See:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/104>
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-WordPress-plugin-dependencies>
 */
if ( defined( 'WPDTRT_TABLE_TEST_DEPENDENCY' ) ) {
	$project_root_path = realpath( __DIR__ . '/../../..' ) . '/';
} else {
	$project_root_path = '';
}

require_once $project_root_path . 'vendor/autoload.php';

/**
 * Replace the TGMPA autoloader
 *
 * See:
 * - <https://github.com/dotherightthing/generator-wpdtrt-plugin-boilerplate#77>
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate#136>
 */
if ( is_admin() ) {
	require_once $project_root_path . 'vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php';
}

// sub classes, not loaded via PSR-4.
// remove the includes you don't need, edit the files you do need.
require_once WPDTRT_TABLE_PATH . 'src/class-wpdtrt-table-plugin.php';
require_once WPDTRT_TABLE_PATH . 'src/class-wpdtrt-table-shortcode.php';

// log & trace helpers.
global $debug;
$debug = new DoTheRightThing\WPDebug\Debug();

/**
 * Group: WordPress Integration
 *
 * Comment out the actions you don't need.
 *
 * Notes:
 *  Default priority is 10. A higher priority runs later.
 *  register_activation_hook() is run before any of the provided hooks
 *
 * See:
 * - <https://developer.wordpress.org/plugins/hooks/actions/#priority>
 * - <https://codex.wordpress.org/Function_Reference/register_activation_hook>
 * _____________________________________
 */

register_activation_hook( dirname( __FILE__ ), 'wpdtrt_table_activate' );

add_action( 'init', 'wpdtrt_table_plugin_init', 0 );
add_action( 'init', 'wpdtrt_table_shortcode_init', 100 );

register_deactivation_hook( dirname( __FILE__ ), 'wpdtrt_table_deactivate' );

/**
 * Group: Plugin config
 * _____________________________________
 */

/**
 * Function: wpdtrt_table_activate
 *
 * Register functions to be run when the plugin is activated.
 *
 * Note:
 * - See also Plugin::helper_flush_rewrite_rules()
 *
 * See:
 * - <https://codex.wordpress.org/Function_Reference/register_activation_hook>
 *
 * TODO:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/128>
 */
function wpdtrt_table_activate() {
	flush_rewrite_rules();
}

/**
 * Function: wpdtrt_table_deactivate
 *
 * Register functions to be run when the plugin is deactivated (WordPress 2.0+).
 *
 * Note:
 * - See also Plugin::helper_flush_rewrite_rules()
 *
 * See:
 * - <https://codex.wordpress.org/Function_Reference/register_deactivation_hook>
 *
 * TODO:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/128>
 */
function wpdtrt_table_deactivate() {
	flush_rewrite_rules();
}

/**
 * Function: wpdtrt_table_plugin_init
 *
 * Plugin initialisaton.
 *
 * Note:
 * - We call init before widget_init so that the plugin object properties are available to it.
 * - If widget_init is not working when called via init with priority 1, try changing the priority of init to 0.
 * - init: Typically used by plugins to initialize. The current user is already authenticated by this time.
 * - widgets_init: Used to register sidebars. Fired at 'init' priority 1 (and so before 'init' actions with priority ≥ 1!)
 *
 * See:
 * - <https://wp-mix.com/wordpress-widget_init-not-working/>
 * - <https://codex.wordpress.org/Plugin_API/Action_Reference>
 *
 * TODO:
 * - Add a constructor function to WPDTRT_Table_Plugin, to explain the options array
 */
function wpdtrt_table_plugin_init() {
	// pass object reference between classes via global
	// because the object does not exist until the WordPress init action has fired.
	global $wpdtrt_table_plugin;

	/**
	 * Array: plugin_options
	 *
	 * Global options.
	 *
	 * See:
	 * - <Options - Adding global options: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-global-options>
	 */
	$plugin_options = array();

	/**
	 * Array: instance_options
	 *
	 * Shortcode or Widget options.
	 *
	 * See:
	 * - <Options - Adding shortcode or widget options: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-shortcode-or-widget-options>
	 */
	$instance_options = array(
		'caption' => array(
			'type'    => 'text',
			'label'   => __( 'Caption', 'wpdtrt-table' ),
			'size'    => 100,
			'tip'     => __( 'Appears as a heading above the table', 'wpdtrt-table' ),
			'default' => '',
		),
		'headers' => array(
			'type'    => 'text',
			'label'   => __( 'Headers', 'wpdtrt-table' ),
			'size'    => 100,
			'tip'     => __( 'Column headers (th), separated by upright bars', 'wpdtrt-table' ),
			'default' => 'none',
		),
		'cols'    => array(
			'type'    => 'text',
			'label'   => __( 'Cols', 'wpdtrt-table' ),
			'size'    => 300,
			'tip'     => __( 'Column content (td), separated by upright bars; rows are created based on the number of headers', 'wpdtrt-table' ),
			'default' => 'none',
		),
		'widths'  => array(
			'type'    => 'text',
			'label'   => __( 'Widths', 'wpdtrt-table' ),
			'size'    => 50,
			'tip'     => __( 'Column widths (auto or N%), separated by upright bars', 'wpdtrt-table' ),
			'default' => 'auto|auto|auto',
		),
	);

	$plugin_dependencies = array();

	/**
	 * Array: ui_messages
	 *
	 * UI Messages.
	 */
	$ui_messages = array(
		'demo_data_description'       => __( 'This demo was generated from the following data', 'wpdtrt-table' ),
		'demo_data_displayed_length'  => __( '# results displayed', 'wpdtrt-table' ),
		'demo_data_length'            => __( '# results', 'wpdtrt-table' ),
		'demo_data_title'             => __( 'Demo data', 'wpdtrt-table' ),
		'demo_date_last_updated'      => __( 'Data last updated', 'wpdtrt-table' ),
		'demo_sample_title'           => __( 'Demo sample', 'wpdtrt-table' ),
		'demo_shortcode_title'        => __( 'Demo shortcode', 'wpdtrt-table' ),
		'insufficient_permissions'    => __( 'Sorry, you do not have sufficient permissions to access this page.', 'wpdtrt-table' ),
		'no_options_form_description' => __( 'There aren\'t currently any options.', 'wpdtrt-table' ),
		'noscript_warning'            => __( 'Please enable JavaScript', 'wpdtrt-table' ),
		'options_form_description'    => __( 'Please enter your preferences.', 'wpdtrt-table' ),
		'options_form_submit'         => __( 'Save Changes', 'wpdtrt-table' ),
		'options_form_title'          => __( 'General Settings', 'wpdtrt-table' ),
		'loading'                     => __( 'Loading latest data...', 'wpdtrt-table' ),
		'success'                     => __( 'settings successfully updated', 'wpdtrt-table' ),
	);

	/**
	 * Array: demo_shortcode_params
	 *
	 * Demo shortcode.
	 *
	 * See:
	 * - <Settings page - Adding a demo shortcode: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Settings-page:-Adding-a-demo-shortcode>
	 */
	$demo_shortcode_params = array(
		'caption' => 'Demo table',
		'widths'  => '20%|30%|auto',
		'headers' => 'Column 1|Column 2|Column 3',
		'cols'    => 'A|B|C|1|2|3|Foo|Bar|Baz',
	);

	/**
	 * Plugin configuration
	 */
	$wpdtrt_table_plugin = new WPDTRT_Table_Plugin(
		array(
			'path'                  => WPDTRT_TABLE_PATH,
			'url'                   => WPDTRT_TABLE_URL,
			'version'               => WPDTRT_TABLE_VERSION,
			'prefix'                => 'wpdtrt_table',
			'slug'                  => 'wpdtrt-table',
			'menu_title'            => __( 'Table', 'wpdtrt-table' ),
			'settings_title'        => __( 'Settings', 'wpdtrt-table' ),
			'developer_prefix'      => 'DTRT',
			'messages'              => $ui_messages,
			'plugin_options'        => $plugin_options,
			'instance_options'      => $instance_options,
			'plugin_dependencies'   => $plugin_dependencies,
			'demo_shortcode_params' => $demo_shortcode_params,
		)
	);
}

/**
 * Group: Shortcode config
 */

/**
 * Function: wpdtrt_table_shortcode_init
 *
 * Register Shortcode.
 */
function wpdtrt_table_shortcode_init() {

	global $wpdtrt_table_plugin;

	$wpdtrt_table_shortcode = new WPDTRT_Table_Shortcode(
		array(
			'name'                      => 'wpdtrt_table_shortcode',
			'plugin'                    => $wpdtrt_table_plugin,
			'template'                  => 'table',
			'selected_instance_options' => array(
				'caption',
				'headers',
				'cols',
				'widths',
			),
		)
	);
}

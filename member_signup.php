<?php
/**
 * Member Signup.
 *
 * Manage site members subscription and profiles.
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 *
 * @wordpress-plugin
 * Plugin Name:       Member Signup
 * Plugin URI:        https://github.com/lucatume/member-signup-for-wordpress.git
 * Description:       Manage site members subscription and profiles.
 * Version:           1.0.0
 * Author:            theAverageDev (Luca Tumedei)
 * Author URI:
 * Text Domain:       membersignup
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/lucatume/member-signup-for-wordpress.git
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
/**
 * Useful constants
 */
define('MEMBERSIGNUP_PLUGIN_FILE', basename(dirname(__FILE__) . '\\' . basename(__FILE__)));
define('MEMBERSIGNUP_PLUGIN_URL', plugin_dir_url(MEMBERSIGNUP_PLUGIN_FILE));
define('MEMBERSIGNUP_PLUGIN_DIRPATH', plugin_dir_path(__FILE__));
define('MEMBERSIGNUP_PLUGIN_DIRNAME', dirname(__FILE__));
/**
 * Require all the classes of the plugin
 */

foreach (glob(MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-*.php') as $filename) {
	require_once $filename;
}
/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
*/
register_activation_hook(__FILE__, array(
	'membersignup',
	'activate'
));
register_deactivation_hook(__FILE__, array(
	'membersignup',
	'deactivate'
));
/*
 * At init load our main plugin classes
*/
add_action('init', array(
	'membersignup',
	'get_instance'
)); // Since this is the class containing many versiona and slug inits it should always be the first one to be initialized
add_action('init', array(
	'membersignup_Admin_Scripts_Styles',
	'get_instance'
));
add_action('init', array(
	'membersignup_Scripts_Styles',
	'get_instance'
));
add_action('init', array(
	'membersignup_Settings_Controller',
	'get_instance'
));
add_action('init', array(
	'membersignup_Redirect_Controller',
	'get_instance'
));
add_action('init', array(
	'membersignup_Shortcode_controller',
	'get_instance'
));
add_action('init', array(
	'membersignup_Registration_controller',
	'get_instance'
));
add_action('init', array(
	'membersignup_User_Profile_controller',
	'get_instance'
));

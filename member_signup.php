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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Useful constants
 */
define( 'MEMBERSIGNUP_PLUGIN_DIRPATH', plugin_dir_path( __FILE__ ) );
define( 'MEMBERSIGNUP_PLUGIN_DIRNAME', dirname(__FILE__) );

/*
 * Require main front-end and back-end class files
 */
require_once( MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-member_signup.php' );
require_once( MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-member_signup-admin.php' );

/**
 * Require options framework adapter class
 */
require_once( MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-options_framework.php');

/**
 * Require more adapter classes
 */
require_once MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-user_role_checker.php';
require_once MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/class-options_getter.php';


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'membersignup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'membersignup', 'deactivate' ) );

/*
 * At init load our main plugin
 */
add_action( 'init', array( 'membersignup', 'get_instance' ) );
add_action( 'init', array( 'membersignup_Admin', 'get_instance' ) );
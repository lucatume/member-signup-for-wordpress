<?php
/**
 * Redirects
 *
 * Classes related to the redirection of visitors
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

class membersignup_Redirect_Controller {
	
	/**
	 * The instance of this class
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct(){
		/**
		 * Require the adapter classes used in the plugin.
		 * When in normal WordPress environment adapter classes should be loaded already
		 * but if the file is used in unit-testing then adapter classes are searched in the 
		 * adapter-classes plugin folder
		 * see 	https://github.com/lucatume/adapter-classes-for-wordpress for more info
		 * on Adapter Classes plugin
		 */
		if ( ! function_exists( 'adclasses_include_adapter_classes' )) {
			require_once __DIR__ . '/../../adapter-classes-for-wordpress/adapter_classes.php';
		}

		// Get hold of neeed adapter classes instances
		$this->filters = adclasses_Filters::get_instance();

		/**
		 * Add an action to redirect users to the member login page
		 */
		$this->filters->add_action( 'wp_loaded', array( $this, 'redirect_to_member_login' ) );
	}
	
	/**
	 * Redirects not logged-in visitors to a custom login page
	 * @return none
	 */
	public function redirect_to_member_login(){
		// logged-in users go their usual way
		if  ( is_user_logged_in() )
			return;
		// only attempt redirection if visiting the login page
		if ( $GLOBALS[ 'pagenow'] != 'wp-login.php') 
			return;
		// get the plugin set options or an empty array as a default
		$membersignup_options = get_option( 'membersignup_options', array() );
		// if the custom login page has been set use it else default it to the default login page with redirection to the admin url
		$custom_login_page_url = 'default';
		if ( isset( $membersignup_options['custom_member_login_page_url'] )) {
			$custom_login_page_url = $membersignup_options['custom_member_login_page_url']; 
		} 
		// do not attempt redirection if the redirect points to the default login page
		if ( $custom_login_page_url == 'default' ) {
			return;
		}
		// Check for POST or GET requests to avoid blocking custom login functions
		// original code by user Anatoly of StackOverflow
		// http://stackoverflow.com/questions/1976781/redirecting-wordpresss-login-register-page-to-a-custom-login-registration-page
		if ( isset( $_POST['wp-submit'] ) ||   // in case of LOGIN
      		( isset($_GET['action']) && $_GET['action']=='logout') ||   // in case of LOGOUT
      		( isset($_GET['checkemail']) && $_GET['checkemail']=='confirm') ||   // in case of LOST PASSWORD
      		( isset($_GET['checkemail']) && $_GET['checkemail']=='registered') ) {
			return;
		}
		wp_redirect( $custom_login_page_url );
		exit();
	}
}
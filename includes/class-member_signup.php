<?php
/**
 * Plugin Name.
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-membersignup-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package membersignup
 * @author  theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 */

class membersignup {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'membersignup';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/**
		 * Add a shortcode to output the member login form in the page
		 */
		add_shortcode( 'membersignup', array( $this, 'display_login_form') );

		/**
		 * Add an action to redirect users to the member login page
		 */
		add_action( 'wp_loaded', array( $this, 'redirect_to_member_login' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

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

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0'
		AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		//  add the "member" role
		add_role( 'member', esc_html__( 'Member', $domain = $plugin_slug ), $capabilities = array() );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		//do not load minified style during debug
		$debug_postfix = '';
		if ( ! defined(WP_DEBUG) || false == WP_DEBUG ) {
			$debug_postfix = '.min';	
		}
		else{
			$debug_postfix = '';
		}

		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( "assets/css/public{$debug_postfix}.css", __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		//do not load minified script during debug
		$debug_postfix = '';
		if ( ! defined(WP_DEBUG) || false == WP_DEBUG ) {
			$debug_postfix = '.min';	
		}
		else{
			$debug_postfix = '';
		}

		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( "assets/js/public{$debug_postfix}.js", __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Returns the custom member login form markup
	 * @param  array $atts    The shortcode attributes
	 * @param  string $content The content enclosed by the shortcode
	 * @return string          The markup to display in the page
	 */
	public function display_login_form( $atts, $content='' ){
		return '<form id="member_login_form">Member login form</form>';
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
		// Check for POST or GET requests to avoid blocking custom login functions
		// original code by user Anatoly of StackOverflow
		// http://stackoverflow.com/questions/1976781/redirecting-wordpresss-login-register-page-to-a-custom-login-registration-page
		if ( isset( $_POST['wp-submit'] ) ||   // in case of LOGIN
      		( isset($_GET['action']) && $_GET['action']=='logout') ||   // in case of LOGOUT
      		( isset($_GET['checkemail']) && $_GET['checkemail']=='confirm') ||   // in case of LOST PASSWORD
      		( isset($_GET['checkemail']) && $_GET['checkemail']=='registered') ) {
			return;
		}
		// get the plugin set options
		$membersignup_options = get_option( 'membersignup_options', array() );
		// is the custom login page has been set use it else default it to the default login page
		$custom_login_page_url = get_site_url( null, '/wp-login.php' );
		if ( isset( $membersignup_options['custom_member_login_page_url'] )) {
			$custom_login_page_url = $membersignup_options['custom_member_login_page_url']; 
		} 
		wp_redirect( $custom_login_page_url );
		exit();
	}
}
?>
<?php
/**
 * Plugin Name.
 *
 * @package   membersignup_Admin
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-membersignup-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package membersignup_Admin
 * @author  theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 */
class membersignup_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = membersignup::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		/*
		 * Add an action link pointing to the options page.
		 *
		 * TODO:
		 *
		 * - Rename "membersignup.php" to the name your plugin
		 */
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'membersignup.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/**
		 * Add the plugin settings to the previously registered page
		 */
		add_action( 'admin_init', array( $this, 'register_settings' ) );

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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {


		//do not load minified styles during debug
			$debug_postfix = '';
			if ( ! defined(WP_DEBUG) || false == WP_DEBUG ) {
				$debug_postfix = '.min';	
			}
			else{
				$debug_postfix = '';
			}

			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( "assets/css/admin{$debug_postfix}.css", __FILE__ ), array(), membersignup::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {

		//do not load minified scripts during debug
			$debug_postfix = '';
			if ( ! defined(WP_DEBUG) || false == WP_DEBUG ) {
				$debug_postfix = '.min';	
			}
			else{
				$debug_postfix = '';
			}

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( "assets/js/admin{$debug_postfix}.js", __FILE__ ), array( 'jquery' ), membersignup::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Member Signup settings', 'membersignup' ),
			__( 'Member Signup', 'membersignup' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
			);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
				),
			$links
			);

	}

	/**
	 * Registers the settings for the plugin
	 * @return none
	 */
	public function register_settings(){
		register_setting( 'membersignup_options', 'membersignup_options', array($this, 'validate_options'));
		add_settings_section( 'main_settings', __( 'Login page settings', 'membersignup' ), 'the_main_section_description', 'membersignup' );
		add_settings_field( 'custom_member_login_page_url', __( 'Custom login page', 'membersignup' ), array($this, 'the_custom_login_page_select'), 'membersignup', 'main_settings' );
	}

	/**
	 * Echoes the first section description
	 * @return none echoes in the plugin settings page
	 */
	public function the_main_section_description(){
		echo '';
	}

	/**
	 * Echoes the custom login page label and select control in the plugin settings page
	 * @return [type] [description]
	 */
	public function the_custom_login_page_select(){
		$options = get_option('membersignup_options');
		// get a list of pages
		$pages = get_pages( array() );
		echo '<label for="plugin_text_string">' . __( 'Select the page to redirect logins to', 'membersignup' ) . '</label>';
		echo "<select id='plugin_text_string' name='membersignup_options[custom_member_login_page_url]' value='{$options['custom_member_login_page_url']}'>";
		if ($pages) {
			foreach ($pages as $page) {
				$guid = $page->guid; // the page URL
				$title = $page->post_title;
				echo "<option value={$guid}" . selected( $guid, $current = $options['custom_member_login_page_url'] )  . ">{$title}</option>";
			}
		}
		echo '</select>';
	}

	/**
	 * Validates the options set or entered by the user
	 * @param  string $input the input entered by the user
	 * @return string        the validated and sanitized input
	 */
	public function validate_options($input){
		return $input;
	}
}
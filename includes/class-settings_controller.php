<?php
/**
 * Settings controller class
 *
 * Adds and manages the plugin Settings
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

class membersignup_Settings_Controller {
	
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

		$this->settings = adclasses_Settings::get_instance();
		$this->filters = adclasses_Filters::get_instance();
		$this->options = adclasses_Options::get_instance();
		$this->pages = adclasses_Pages::get_instance();
		$this->functions = adclasses_Functions::get_instance();

		// Add the options page and menu item.
		$this->filters->add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		/**
		 * Add a link to the settings page for the plugin
		 */
		$plugin_basename = plugin_basename( MEMBERSIGNUP_PLUGIN_DIRPATH . 'member_signup.php' );
		$this->filters->add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/**
		 * Add the plugin settings to the previously registered page
		 */
		$this->filters->add_action( 'admin_init', array( $this, 'register_settings' ) );

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
		$this->plugin_screen_hook_suffix = $this->settings->add_option_page(
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
		$this->settings->register_setting( 'membersignup_options', 'membersignup_options', array($this, 'validate_options'));
		$this->settings->add_settings_section( 'main_settings', __( 'Login page settings', 'membersignup' ), array($this, 'the_main_section_description' ), 'membersignup' );
		$this->settings->add_settings_field( 'custom_member_login_page_url', __( 'Custom login page', 'membersignup' ), array($this, 'the_custom_login_page_select'), 'membersignup', 'main_settings' );
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
		$options = $this->options->get_option('membersignup_options');
		// get a list of pages
		$pages = $this->pages->get_pages( array() );
		echo '<label for="plugin_text_string">' . __( 'Select the page to redirect logins to', 'membersignup' ) . '</label>';
		echo "<select id='plugin_text_string' name='membersignup_options[custom_member_login_page_url]' value='{$options['custom_member_login_page_url']}'>";
		if ($pages) {
			foreach ($pages as $page) {
				$guid = $page->guid; // the page URL
				$title = $page->post_title;
				echo "<option value={$guid}" . $this->functions->selected( $guid, $current = $options['custom_member_login_page_url'] )  . ">{$title}</option>";
			}
		}
		// Add the default login page URL
		$default_login_page_url = 'default';
		echo "<option value={$default_login_page_url}" . $this->functions->selected( $default_login_page_url, $current = $options['custom_member_login_page_url'] )  . ">" . esc_html__( 'Default login page (do not redirect)',  'default' ) . "</option>";
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
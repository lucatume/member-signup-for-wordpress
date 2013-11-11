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
class membersignup_Settings_Controller
{
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
	public static function get_instance($adapters = null)
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self($adapters);
		}
		
		return self::$instance;
	}
	/**
	 * Unsets the static instance
	 * @return none
	 */
	public static function unset_instance()
	{
		if (isset(self::$instance)) {
			self::$instance = null;
		}
	}
	private function __construct($adapters = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'settings',
				'filters',
				'options',
				'pages',
				'functions'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		// Add the options page and menu item.
		$this->functions ->add_action('admin_menu', array(
			$this,
			'add_plugin_admin_menu'
		));
		/**
		 * Add a link to the settings page for the plugin
		 */
		$plugin_basename = $this->functions->plugin_basename(MEMBERSIGNUP_PLUGIN_DIRPATH . 'member_signup.php');
		$this->functions ->add_filter('plugin_action_links_' . $plugin_basename, array(
			$this,
			'add_action_links'
		));
		/**
		 * Add the plugin settings to the previously registered page
		 */
		$this->functions ->add_action('admin_init', array(
			$this,
			'register_settings'
		));
	}
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu()
	{
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		*/
		$this->plugin_screen_hook_suffix = $this->functions->add_options_page(__('Member Signup settings', 'membersignup') , __('Member Signup', 'membersignup') , 'manage_options', 'membersignup', array(
			$this,
			'display_plugin_admin_page'
		));
	}
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page()
	{
		include_once (MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/views/admin.php');
	}
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links($links)
	{
		
		return array_merge(array(
			'settings' => '<a href="' . $this->functions->admin_url('options-general.php?page=' . 'membersignup') . '">' . __('Settings', 'membersignup') . '</a>'
		) , $links);
	}
	/**
	 * Registers the settings for the plugin
	 * @return none
	 */
	public function register_settings()
	{
		$this->functions->register_setting('membersignup_options', 'membersignup_options', array(
			$this,
			'validate_options'
		));
		$this->functions->add_settings_section('main_settings', __('Login page settings', 'membersignup') , array(
			$this,
			'the_main_section_description'
		) , 'membersignup');
		$this->functions->add_settings_field('custom_member_login_page_url', __('Custom login page', 'membersignup') , array(
			$this,
			'the_custom_login_page_select'
		) , 'membersignup', 'main_settings');
	}
	/**
	 * Echoes the first section description
	 * @return none echoes in the plugin settings page
	 */
	public function the_main_section_description()
	{
		echo '';
	}
	/**
	 * Echoes the custom login page label and select control in the plugin settings page
	 * @return [type] [description]
	 */
	public function the_custom_login_page_select()
	{
		$options = $this->functions->get_option('membersignup_options');
		// get a list of pages
		$pages = $this->functions->get_pages(array());
		echo '<label for="plugin_text_string">' . __('Select the page to redirect logins to', 'membersignup') . '</label>';
		echo "<select id='plugin_text_string' name='membersignup_options[custom_member_login_page_url]' value='{$options['custom_member_login_page_url']}'>";
		if ($pages) {
			
			foreach ($pages as $page) {
				$guid = $page->guid; // the page URL
				$title = $page->post_title;
				echo "<option value={$guid}" . $this->functions->selected($guid, $current = $options['custom_member_login_page_url']) . ">{$title}</option>";
			}
		}
		// Add the default login page URL
		$default_login_page_url = 'default';
		echo "<option value={$default_login_page_url}" . $this->functions->selected($default_login_page_url, $current = $options['custom_member_login_page_url']) . ">" . esc_html__('Default login page (do not redirect)', 'default') . "</option>";
		echo '</select>';
	}
	/**
	 * Validates the options set or entered by the user
	 * @param  string $input the input entered by the user
	 * @return string        the validated and sanitized input
	 */
	public function validate_options($input)
	{
		// right now there are no options that need validation
		return $input;
	}
}

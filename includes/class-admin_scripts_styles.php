<?php
/**
 * Plugin Name.
 *
 * @package   membersignup_Admin_Scripts_Styles
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
 * @package membersignup_Admin_Scripts_Styles
 * @author  theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 */
class membersignup_Admin_Scripts_Styles
{
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
	 * Initialize the plugin by loading admin scripts & styles
	 *
	 * @since     1.0.0
	 */
	private function __construct($adapters = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'filters',
				'scripts',
				'styles',
				'functions'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		$plugin = membersignup::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		// Load admin style sheet and JavaScript.
		$this->functions->add_action('admin_enqueue_scripts', array(
			$this,
			'enqueue_admin_styles'
		));
		$this->functions->add_action('admin_enqueue_scripts', array(
			$this,
			'enqueue_admin_scripts'
		));
	}
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
	/**
	 * Instantiates the class object
	 * @param array $adapters An array of adapter objects to be used in place of the ones the constructor would fetch on itself
	 */
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles()
	{
		if (!isset($this->plugin_screen_hook_suffix)) {
			
			return;
		}
		$screen = $this->functions->get_current_screen();
		if ($this->plugin_screen_hook_suffix == $screen->id) {
			//do not load minified styles during debug
			$debug_postfix = '';
			if (!defined(WP_DEBUG) || false == WP_DEBUG) {
				$debug_postfix = '.min';
			}
			else {
				$debug_postfix = '';
			}
			$this->styles->wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url("assets/css/admin{$debug_postfix}.css", __FILE__) , array() , membersignup::VERSION);
		}
	}
	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts()
	{
		if (!isset($this->plugin_screen_hook_suffix)) {
			
			return;
		}
		$screen = $this->functions->get_current_screen();
		if ($this->plugin_screen_hook_suffix == $screen->id) {
			//do not load minified scripts during debug
			$debug_postfix = '';
			if (!defined(WP_DEBUG) || false == WP_DEBUG) {
				$debug_postfix = '.min';
			}
			else {
				$debug_postfix = '';
			}
			$this->functions->wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url("assets/js/admin{$debug_postfix}.js", __FILE__) , array(
				'jquery'
			) , membersignup::VERSION);
		}
	}
}

<?php
/**
 * Scripts and Styles
 *
 * The classes that register and enqueue the plugin front-end scripts and styles
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
class membersignup_Scripts_Styles
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
	private function __construct($adapters = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'filters',
				'styles',
				'scripts',
				'functions'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		// Load public-facing style sheet and JavaScript.
		$this->filters->add_action('wp_enqueue_scripts', array(
			$this,
			'enqueue_styles'
		));
		$this->filters->add_action('wp_enqueue_scripts', array(
			$this,
			'enqueue_scripts'
		));
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
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		//do not load minified style during debug
		$debug_postfix = '';
		if (!defined(WP_DEBUG) || false == WP_DEBUG) {
			$debug_postfix = '.min';
		}
		else {
			$debug_postfix = '';
		}
		$this->styles->wp_enqueue_style('membersignup-plugin-styles', $this->functions->plugins_url("assets/css/public{$debug_postfix}.css", __FILE__) , array() , membersignup::VERSION);
	}
	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		//do not load minified script during debug
		$debug_postfix = '';
		if (!defined(WP_DEBUG) || false == WP_DEBUG) {
			$debug_postfix = '.min';
		}
		else {
			$debug_postfix = '';
		}
		$this->scripts->wp_enqueue_script('membersignup-plugin-script', $this->functions->plugins_url("assets/js/public{$debug_postfix}.js", __FILE__) , array(
			'jquery'
		) , membersignup::VERSION);
	}
}

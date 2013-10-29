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

class membersignup_Scripts_Styles {

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

	$this->styles = adclasses_Styles::get_instance();
	$this->scripts = adclasses_Scripts::get_instance();
	$this->filters = adclasses_Filters::get_instance();

	// Load public-facing style sheet and JavaScript.
	$this->filters->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	$this->filters->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

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

		$this->styles->wp_enqueue_style( 'membersignup-plugin-styles', plugins_url( "assets/css/public{$debug_postfix}.css", __FILE__ ), array(), membersignup::VERSION );
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

		$this->scripts->wp_enqueue_script( 'membersignup-plugin-script', plugins_url( "assets/js/public{$debug_postfix}.js", __FILE__ ), array( 'jquery' ), membersignup::VERSION );
	}

}
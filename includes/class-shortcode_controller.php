<?php
/**
 * Shortcode controller
 *
 * The classes that control and manage the plugin shortcodes
 *
 * @package   membersingnup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

class membersignup_Shortcode_Controller {
	
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

		$this->filters = adclasses_Filters::get_instance();

		/**
		 * Add a shortcode to output the member login form in the page
		 */
		$this->filters->add_shortcode( 'membersignup', array( $this, 'display_login_form') );
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
}
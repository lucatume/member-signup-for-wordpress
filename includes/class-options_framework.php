<?php
/**
 * Plugin Options Framework adapter class
 *
 * This class acts as an adapter and entry point between the plugin code and karenv’s plugin options framework.
 * See https://github.com/karevn/plugin-options-framework for original code.
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

/**
 * The main class, acts as an entry point and is meant to be a singleton
 */
class membersignup_Options_Framework {

	/**
	 * The singleton instance of the object
	 * @var membersignup_Options_Framework
	 */
	protected static $instance = null;

	/**
	 * The array that contains the fields to be added to the plugin options in the format
	 *
	 * array('type' => 'text', 'name' => 'text_field', 'title' => 'Text setting field', 'default' => 'default text'), // Added a text field
     * array('type' => 'tab', 'title' => 'Other options tab'), //Started a new tab
     * array('type' => 'section', 'title' => 'some settings section'), //Started a new section at this tab
     * array('type' => 'color', 'name' => 'background', 'title' => 'Background color', 
     *	'default' => '#444',
     *	'legend' => 'A legend shown at the right side') // Added a background color
	 * see https://github.com/karevn/plugin-options-framework for examples
	 * @var array
	 */
	protected static $fields = null;
	
	/**
	 * Private constructor method also inits static attributes
	 */
	private function __construct(){
		self::$fields = array();
		add_action( 'init', array( 'membersignup_Options_Framework', 'options_init' ) );
	}

	/**
	 * Returns the instance of the plugin class
	 * @return membersignup_Options_Framework The instance of the plugin class
	 */
	public static function get_instance(){
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Adds the user set options for the plugin, called at init hook
	 * @return none
	 */
	public static function options_init(){
		global $options;
		require_once ( MEMBERSIGNUP_PLUGIN_DIRPATH . 'includes/frameworks/plugin-options-framework/plugin-options-framework.php' );
		$options = new Plugin_Options_Framework_0_2_4(
			MEMBERSIGNUP_PLUGIN_DIRNAME,
			self::$fields,
			array('page_title' => 'Plugin Settings') // TODO: a custom or better built title
			);
	}

	public function add_option( $option ){
		if ( null == $option || ! is_array($option) || empty($option) ) {
			return;
		}
		$this->fields[] = $option;
	}
}

<?php
/**
 * Actions and Filters adapter class
 *
 * Wraps WordPress methods into class methods for test purposes
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

class membersignup_Filters {
	
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

	/**
	 * Adds an action to an hook
	 * @param string  $tag             The hook to attach the function to
	 * @param string/array  $function_to_add Either a function name or an array to identify the callback function
	 * @param integer $priority        The priority of execution of the callback, the lower the earlier
	 * @param int $accepted_args   The number of args that will be passed to the callback function
	 */
	public static function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
		return add_filter($tag, $function_to_add, $priority, $accepted_args);
	}

	/**
	 * Adds an action to an hook
	 * @param string  $tag             The hook to attach the function to
	 * @param string/array  $function_to_add Either a function name or an array to identify the callback function
	 * @param integer $priority        The priority of execution of the callback, the lower the earlier
	 * @param int $accepted_args   The number of args that will be passed to the callback function
	 */
	public static function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
		return add_filter($tag, $function_to_add, $priority, $accepted_args);
	}
}
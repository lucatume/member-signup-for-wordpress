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
class membersignup_Shortcode_Controller
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
	public static function get_instance($adapters = null, $view = null)
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self($adapters, $view);
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
	private function __construct($adapters = null, $view = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'filters'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		if (null == $view) {
			$view = new membersignup_Shortcode_View();
		}
		$this->view = $view;
		/**
		 * Add a shortcode to output the member login form in the page
		 */
		$this->filters->add_shortcode('membersignup_login_form', array(
			$this,
			'display_login_form'
		));
	}
	/**
	 * Returns the custom member login form markup
	 * @param  array $atts    The shortcode attributes
	 * @param  string $content The content enclosed by the shortcode
	 * @return string          The markup to display in the page
	 */
	public function display_login_form($atts, $content = '')
	{
			echo $this->view->get_view();
		
	}
}

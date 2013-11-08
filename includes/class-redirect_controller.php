<?php
/**
 * Redirects
 *
 * Classes related to the redirection of visitors
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
class membersignup_Redirect_Controller
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
	/**
	 * Instantiates the class object
	 * @param array $adapters An array of adapter objects to be used in place of the ones the constructor would fetch on itself
	 */
	private function __construct($adapters = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'functions',
				'globals'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		/**
		 * Add an action to redirect users to the member login page
		 */
		$this->functions->add_action('wp_loaded', array(
			$this,
			'redirect_to_member_login'
		));
	}
	/**
	 * Redirects not logged-in visitors to a custom login page
	 * @return none
	 */
	public function redirect_to_member_login()
	{
		// logged-in users go their usual way
		if ($this->functions->is_user_logged_in()) {
			
			return;
		}
		if (!$this->should_redirect()) {
			
			return;
		}
		// do not attempt redirection if the redirect points to the default login page
		$custom_login_page_url = $this->get_custom_login_page_url();
		if ($custom_login_page_url == 'default') {
			
			return;
		}
		$this->functions->wp_redirect($custom_login_page_url);
		die();
	}
	/**
	 * Check for POST or GET requests to avoid blocking custom login functions
	 * original code by user Anatoly of StackOverflow
	 * http://stackoverflow.com/questions/1976781/redirecting-wordpresss-login-register-page-to-a-custom-login-registration-page
	 * @return [type] [description]
	 */
	public function should_redirect()
	{
		// only attempt redirection if visiting the login page
		if (!$this->is_login_page()) {
			
			return false;
		}
		if ($this->is_login_page() && $this->is_registering()) {
			
			return false;
		}
		if ($this->is_login_page() && $this->is_logging_out()) {
			
			return false;
		}
		if ($this->is_wp_submit()) {
			
			return false;
		}
		if ($this->is_email_confirm()) {
			
			return false;
		}
		if ($this->is_email_registered()) {
			
			return false;
		}
		
		return true;
	}
	public function is_email_registered()
	{
		$checkemail = $this->globals->get('checkemail');
		if (isset($checkemail) && $checkemail == 'registered') {
			
			return true;
		}
		
		return false;
	}
	public function is_email_confirm()
	{
		$checkemail = $this->globals->get('checkemail');
		if (isset($checkemail) && $checkemail == 'confirm') {
			
			return true;
		}
		
		return false;
	}
	public function is_wp_submit()
	{
		$post = $this->globals->post('wp-submit');
		if (isset($post) && $post == 'wp-submit') {
			
			return true;
		}
		
		return false;
	}
	public function is_loggin_out()
	{
		$action = $this->globals->get('action');
		if (isset($action) && $action == 'logout') {
			
			return true;
		}
		
		return false;
	}
	public function is_registering()
	{
		$action = $this->globals->get('action');
		if (isset($action) && $action == 'register') {
			
			return true;
		}
		
		return false;
	}
	public function is_login_page()
	{
		$pagenow = $this->globals->globals('pagenow');
		if (isset($pagenow) && $pagenow == 'wp-login.php') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Gets the user-set custom login page URL or empty string if not set
	 * @return string The user-set login page URL or an empty string
	 */
	public function get_custom_login_page_url()
	{
		// get the plugin set options or an empty array as a default
		$membersignup_options = $this->functions->get_option('membersignup_options');
		if (!is_array($membersignup_options)) {
			
			return 'default';
		}
		// if the custom login page has been set use it else default it to the default login page with redirection to the admin url
		$custom_login_page_url = 'default';
		if (isset($membersignup_options['custom_member_login_page_url'])) {
			$custom_login_page_url = $membersignup_options['custom_member_login_page_url'];
		}
		
		return $custom_login_page_url;
	}
}

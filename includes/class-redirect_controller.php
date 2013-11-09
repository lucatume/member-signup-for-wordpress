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
		// if the user should not be redirected for any other reason then return
		if (!$this->should_redirect()) {
			
			return;
		}
		// if the redirect location has not been set or the redirect location is the
		// default one then return
		$custom_login_page_url = $this->get_custom_login_page_url();
		if ($custom_login_page_url == 'default') {
			
			return;
		}
		// redirect the visitor to the set login page
		$this->functions->wp_redirect($custom_login_page_url);
		exit();
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
	/**
	 * Conditional to check if the actual get request is the checkmail registered one.
	 * @return boolean True if the get request is checkmail with the registered value, false if there is no checkemail get request or the checkemail get request is not registered.
	 */
	public function is_email_registered()
	{
		$checkemail = $this->globals->get('checkemail');
		if (isset($checkemail) && $checkemail == 'registered') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Conditional to check if the actual get request is the checkmail confirm one.
	 * @return boolean True if the checkmail request is confirm, false if there is no checkemail get request or the get request is not confirm.
	 */
	public function is_email_confirm()
	{
		$checkemail = $this->globals->get('checkemail');
		if (isset($checkemail) && $checkemail == 'confirm') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Conditional to check if the wp-submit post request is defines. Will not check the wp-submit value.
	 * @return boolean True if the wp-submit post request is not defined.
	 */	
	public function is_wp_submit()
	{
		$post = $this->globals->post('wp-submit');
		if (isset($post) && $post == 'wp-submit') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Conditional to check if the get action is logout.
	 * @return boolean True if it's logout, false if there is no get action or the get action is not logout.
	 */
	public function is_loggin_out()
	{
		$action = $this->globals->get('action');
		if (isset($action) && $action == 'logout') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Conditional to check if the action is the 'register' one.
	 * @return boolean True if the action is register, false if there is no get action or the get action is not register.
	 */
	public function is_registering()
	{
		$action = $this->globals->get('action');
		if (isset($action) && $action == 'register') {
			
			return true;
		}
		
		return false;
	}
	/**
	 * Conditional to check if the current page is the login one.
	 * @return boolean True if it's the login page, false if the requested uri is not 'wp-login.php' or the request method is not 'get'.
	 */
	public function is_login_page()
	{
		$page = basename($this->functions->server('REQUEST_URI'));
		$request_method = $this->functions->server('REQUEST_METHOD');
		if (null !== $page && $page == 'wp-login.php' && nul !== $request_method && $request_method == 'GET') {
			
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

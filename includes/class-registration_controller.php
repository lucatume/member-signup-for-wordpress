<?php
class membersignup_Registration_Controller implements adclasses_Singleton
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
	public static function get_instance($mocks = null)
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self($mocks);
		}
		
		return self::$instance;
	}
	private function __construct($adapters= null){
		if (!is_array($adapters)) {
			// instance the adapters by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array('functions','globals'));
		}
		foreach ($adapters as $key => $value) {
			$this->{$key} = $value;
		}

		/**
		 * Hook into the register filter to be able to customize the register and admin links
		 */
		$this->functions->add_filter('register', array(
			$this,
			'customize_register_link_text'
			));
		/**
		 * Hook into the login header to buffer the registration form output
		 */
		$this->functions->add_action('login_head', array($this, 'start_output_buffering'));
		/**
		 * Hook into the login footer to end output buffering and return the form output
		 */
		$this->functions->add_action('login_footer', array($this, 'end_output_buffering'));
		
	}
	public static function unset_instance()
	{
		if (isset(self::$instance)) {
			unset(self::$instance);
		}
	}
	public function customize_register_link_text($link)
	{
		$link_text = '';
		if ($this->functions->is_user_logged_in()) {
			$user_id = get_current_user_id();
			$user_data = get_userdata($user_id);
			// TODO: make string below eng
			$link_text = sprintf($this->functions->esc_html__('Accedi al profilo di %1$s %2$s', 'membersignup') , $user_data->user_firstname, $user_data->user_lastname);
		}
		else {
			// TODO: make string below eng
			$link_text = $this->functions->esc_html__('Oppure registra un nuovo socio', 'membersignup');
		}
		// replace the link text
		
		return preg_replace("~(<a[^>]*>)([^<]*?)(<\\s*/a>)~uU", "$1" . $link_text . "$3", $link);
	}
	/**
	 * Activates output buffering to be able to get the registration form markup
	 * see wp_register source to understand why `wp_register` action will not do:
	 * http://core.trac.wordpress.org/browser/tags/3.7.1/src/wp-login.php#L0
	 * @return none
	 */
	public function start_output_buffering(){
		if ($this->is_register_page()) {
			ob_start();
		}
		
	}
	/**
	 * Ends output buffering for the registration page and returns the modified registration form
	 * see wp_register source to understand why `wp_register` action will not do:
	 * http://core.trac.wordpress.org/browser/tags/3.7.1/src/wp-login.php#L0
	 * @return string 	The registration form markup
	 */
	public function end_output_buffering(){
		if ($this->is_register_page()) {

			$contents = ob_get_contents();
			ob_end_clean();
			// replace the user login label
			// TODO: should be an option
			// TODO: should be english
			$user_login_label = $this->functions->esc_html__( 'Codice fiscale della persona che si vuole iscrivere', 'membersignup' );
			$contents = preg_replace("~(<label\\s*for\\s*=[\"']\\s*user_login\\s*[\"']\\s*>)([^<]*?)~uU", "$1".$user_login_label, $contents);
			echo $contents;
		}
	}
	/**
	 * Checks if the current page is the registration one and the user is visiting to register.
	 * @return boolean True if it's the registration page and the user is visiting to register, false otherwise.
	 */
	public function is_register_page(){
		if ( null !== $_REQUEST['action'] && $_REQUEST['action'] == 'register') {
			return true;
		}
		
		return false;
	}
	public function customize_registration_fields(){
		$content = ob_get_contents();
		$contents = 'register me';
		$content = ob_get_clean();
		echo $content;
	}
}
?>
<?php
class membersignup_User_Profile_Controller implements adclasses_Singleton
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
	/**
	 * Creates and returns an instance of the user profile controller class.
	 * @param array $adapters An arrayo of already instatiated adapter class objects. Used in mocking and injection.
	 */
	private function __construct($adapters = null)
	{
		if (!is_array($adapters)) {
			// instance the adapters by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'functions',
				'globals'
			));
		}
		
		foreach ($adapters as $key => $value) {
			$this->{$key} = $value;
		}
		/**
		 * Hooks
		 */
		$this->functions->add_action('admin_head', array(
			$this,
			'start_profile_buffer'
		));
		$this->functions->add_action('admin_footer', array(
			$this,
			'end_profile_buffer'
		));
		/**
		 * Filters
		 */
		$this->functions->add_filter('user_contactmethods', array(
			$this,
			'remove_contact_methods'
		));
	}
	/**
	 * Sets the current singleton instance to null.
	 * @return none
	 */
	public static function unset_instance()
	{
		if (isset(self::$instance)) {
			self::$instance = null;
		}
	}
	/**
	 * Activates the output buffering.
	 * @return none
	 */
	public function start_profile_buffer()
	{
		if ($this->should_modify_profile_fields()) {
			// start buffering
			ob_start();
		}
	}
	/**
	 * Deactivates the output buffering and modifies it to the user settings.
	 * @return none Echoes the profile HTML markup to the page.
	 */
	public function end_profile_buffer()
	{
		if ($this->should_modify_profile_fields()) {
			$contents = ob_get_contents();
			ob_end_clean();
			// TODO: all the fields below should be options
			// TODO: please make stronger regexes
			// change the personal profile title
			$user_data = get_userdata(get_current_user_id());
			$title = sprintf($this->functions->esc_html__('Dati di iscrizione di %1$s %2$s', 'membersignup') , $user_data->user_firstname, $user_data->user_lastname);
			$contents = preg_replace("~(<div\\s*id\\s*=\\s*[\"']icon-profile.*<h2>)([^<]*)(</h2>)~uUs", "$1" . $title . "$3", $contents);
			//remove the personal informations title
			$contents = preg_replace("~(<\\s*form\\s*id\\s*=\\s*['\"]your-profile.*)(<h3>.*)(<h3)~uUs", "$1$3", $contents, 1);
			// change the username label
			$username_label = $this->functions->esc_html__('Codice Fiscale', 'membersignup');
			$username_description = $this->functions->esc_html__('Il codice fiscale non puó essere cambiato', 'membersignup');
			$contents = preg_replace("~(<label\\s*for\\s*=\\s*['\"]user_login['\"].*>)([^<]*?)(<.*<span class=\"description\">)([^<]*?)~uUs", "$1" . $username_label . "$3" . $username_description, $contents);
			// remove the nickname and the display name row
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=['\"]nickname['\"].*</tr>.*display_name.*/tr>~uUs", "", $contents);
			// remove the website row
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=[\"']url['\"].*</tr>~uUs", "", $contents);
			// remove the bio section
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=[\"']description['\"].*</tr>~uUs", "", $contents);
			// remove all the titles
			$contents = preg_replace("~(<\\s*h3\\s*>[^<]*<\\s*/h3\\s*>)\\s*(<\\s*table\\s*class\\s*=\\s*['\"]form-table)~uUs", "$2", $contents);
			// remove the admin menu
			$contents = preg_replace("~(<\\s*div\\s*id\\s*=\\s*['\"]adminmenuback.*)(<\\s*div\\s*id\\s*=\\s*['\"]wpcontent)~uUs", "$2", $contents);
			// change the button value
			$button_value = $this->functions->esc_attr__('Aggiorna Dati', 'membersignup');
			$contents = preg_replace("/(button\\s*button-primary[\"']\\s*value\\s*=['\"])([^\"']*?)/uUs", "$1$button_value", $contents);
			echo $contents;
		}
	}
	/**
	 * Conditional to check if the profile should be modified.
	 * @return bool True if eligible for profile modification, false otherwise.
	 */
	public function should_modify_profile_fields()
	{
		// TODO: do some REAL check here
		if ($this->functions->is_admin()) {
			// TODO: should be an option
			$roles = array(
				'member',
				'subscriber'
			);
			
			foreach ($roles as $role) {
				if (membersignup_User_Role_Checker::is_user_of_role($role)) {
					
					return true;
				}
			}
		}
		
		return false;
	}
	/**
	 * Removes unwanted user contact methods for some user roles
	 * @param  array $contact_methods The array of set contact methods as returned by the WordPress filter
	 * @return array                  The modified user contact methods array
	 */
	public function remove_contact_methods($contact_methods)
	{
		// TODO: the users to apply the contact method stripping to should be an option
		$roles = array(
			'subscriber',
			'member'
		);
		
		foreach ($roles as $role) {
			if (membersignup_User_Role_Checker::is_user_of_role($role)) {
				// TODO: what contact methods to remove should be an option
				$contact_methods_slugs_to_unset = array(
					'aim',
					'yim',
					'jabber'
				);
				
				foreach ($contact_methods_slugs_to_unset as $key) {
					if (isset($contact_methods[$key])) {
						unset($contact_methods[$key]);
					}
				}
			}
		}
		
		return $contact_methods;
	}
}
?>
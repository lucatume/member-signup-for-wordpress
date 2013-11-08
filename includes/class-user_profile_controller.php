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
		$this->functions->add_action('admin_head', array(
			$this,
			'start_profile_buffer'
		));
		$this->functions->add_action('admin_footer', array(
			$this,
			'end_profile_buffer'
		));
	}
	public static function unset_instance()
	{
		if (isset(self::$instance)) {
			self::$instance = null;
		}
	}
	public function start_profile_buffer()
	{
		if ($this->should_modify_profile_fields()) {
			// start buffering
			ob_start();
		}
	}
	public function end_profile_buffer()
	{
		if ($this->should_modify_profile_fields()) {
			$contents = ob_get_contents();
			ob_end_clean();
			// TODO: all the fields below should be options
			// TODO: please make stronger regexes
			// change the personal profile title
			$contents = preg_replace("~(<div\\s*id\\s*=\\s*[\"']icon-profile.*<h2>)([^<]*)(</h2>)~uUs", "$1Dati Iscrizione$3", $contents);
			//remove the personal informations title
			$contents = preg_replace("~(<\\s*form\\s*id\\s*=\\s*['\"]your-profile.*)(<h3>.*)(<h3)~uUs", "$1$3", $contents, 1);
			// change the username label
			$contents = preg_replace("~(<label\\s*for\\s*=\\s*['\"]user_login['\"].*>)([^<]*?)(<.*<span class=\"description\">)([^<]*?)~uUs", "$1Codice Fiscale$3Il codice fiscale non puo' essere modificato", $contents);
			// remove the nickname row
			// remove the public name row
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=['\"]nickname['\"].*</tr>.*display_name.*/tr>~uUs", "", $contents);
			// remove the website row
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=[\"']url['\"].*</tr>~uUs", "", $contents);
			// remove the bio section
			$contents = preg_replace("~<tr>\\s*<th>\\s*<label\\s*for\\s*=[\"']description['\"].*</tr>~uUs", "", $contents);
			// remove all the titles
			$contents = preg_replace("~(<\\s*h3\\s*>[^<]*<\\s*/h3\\s*>)\\s*(<\\s*table\\s*class\\s*=\\s*['\"]form-table)~uUs", "$2", $contents);
			echo $contents;
		}
	}
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
}
?>
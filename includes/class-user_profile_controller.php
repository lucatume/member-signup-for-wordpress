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
				'functions'
			));
		}
		
		foreach ($adapters as $key => $value) {
			$this->{$key} = $value;
		}
		// hook into admin head and footer
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
	public function admin_head()
	{
	}
	public function admin_footer()
	{
	}
}
?>
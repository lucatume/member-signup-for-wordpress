<?php
class membersignup_Shortcode_View
{
	public function __construct($adapters = null)
	{
		if (!is_array($adapters)) {
			$slugs = array(
				'functions'
			);
			$adapters = adclasses_Factory::get_instance()->get_adapters($slugs);
		}
		
		foreach ($adapters as $key => $value) {
			$this->{$key} = $value;
		}
	}
	public function get_view()
	{
		$args = array(
			'echo' => false,
			'form_id' => 'member_login_form'
		);
		
		return $this->functions->wp_login_form($args);
	}
}
?>
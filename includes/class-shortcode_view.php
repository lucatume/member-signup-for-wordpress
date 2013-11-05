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
			'label_username' => $this->functions->esc_html__('Codice Fiscale', 'membersignup') ,
			'label_remember' => $this->functions->esc_html__('Ricorda i dati', 'membersignup') ,
			'label_log_in' => $this->functions->esc_html__('Entra', 'membersignup') ,
			'form_id' => 'member_login_form'
		);
		$login = $this->functions->wp_login_form($args);
		// $register = $this->get_registration();
		$out = $login;
		return $out;
	}
}
?>
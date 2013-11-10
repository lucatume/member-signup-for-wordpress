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
		// TODO: signup section header for logged-in users should be an option
		// TODO: default should be english
		if ($this->functions->is_user_logged_in()) {
			$user_data = get_userdata(get_current_user_id());
			$this->signup_section_header = sprintf($this->functions->esc_html__('Sei entrato come %1$s %2$s', 'membersignup') , $user_data->user_firstname, $user_data->user_lastname);
		}
		else {
			// TODO: signup section header for not logged-in users should be an option
			// TODO: should be english
			$this->signup_section_header = $this->functions->esc_html__('Accedi all\'anagrafica', 'membersignup');
		}
		// get the registration links markup
		$this->signup_registration_link = $this->functions->wp_register('', '', false);
		// if the user is not logged in then show the login form
		if (!$this->functions->is_user_logged_in()) {
			// set the args for login form
			$args = array(
				// TODO: fields below should be in english
				'echo' => false,
				'label_username' => $this->functions->esc_html__('Codice Fiscale', 'membersignup') ,
				'label_remember' => $this->functions->esc_html__('Ricorda i dati', 'membersignup') ,
				'label_log_in' => $this->functions->esc_html__('Accedi', 'membersignup') ,
				'form_id' => 'member_login_form'
				);
			// get the login form markup
			$this->login_form_or_register_link = $this->functions->wp_login_form($args);
		}
		// if the user is logged in already then allow it to logout
		else {
			$logout_url = wp_logout_url(get_permalink());
			// TODO: text below should be english
			$logout_text = $this->functions->esc_html__('Esci ed accedi come altro socio od iscrivi un nuovo socio', 'membersignup');
			$this->login_form_or_register_link = '<a href="' . $logout_url . '" title="Logout">' . $logout_text . '</a>';
		}
		
		return $this->signup_view();
	}
	public function signup_view()
	{
		ob_start();
		?>
		<section id="signup">
			<h3>
				<?php
				echo $this->signup_section_header; ?>
			</h3>
		</section>
		<p>
			<?php
			echo $this->signup_registration_link; ?>
		</p>
		<p>
			<?php
			echo $this->login_form_or_register_link; ?>
		</p>
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		
		return $out;
	}
}
?>

<?php
class redirectControllerTest extends Adapter_Classes_TestCase
{
	public function setUp()
	{
		// do something before each test
		
	}
	public function test_invokes_add_action_only_once()
	{
		$mock_adapters = $this->get_mock_adapters();
		// expects 'add_action' to be called once
		// not checking parameters
		$mock_adapters['functions']->expects($this->once())->method('add_action')->will($this->returnValue(true));
		$a = membersignup_Redirect_Controller::get_instance($mock_adapters);
		$b = membersignup_Redirect_Controller::get_instance();
	}
	public function test_redirect_returns_for_logged_users()
	{
		// get mock adapters
		$mocks = $this->get_mock_adapters(array(
			'functions',
			'globals'
		));
		// set is_user_logged_in
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(true));
		// pagenow should not be called
		$mocks['globals']->expects($this->never())->method('globals')->with($this->equalTo('pagenow'));
		// get an instance and inject mocks
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$i->redirect_to_member_login();
	}
	public function test_does_not_redirect_if_not_wp_login()
	{
		$mocks = $this->get_mock_adapters();
		// user is not logged in
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(false));
		// page is not wp-login.php
		$mocks['globals']->expects($this->once())->method('globals')->with($this->equalTo('pagenow'))->will($this->returnValue('not_wp_login.php'));
		// should not redirect
		$mocks['functions']->expects($this->never())->method('wp_redirect');
		// get an instance and inject mocks
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$i->redirect_to_member_login();
	}
	public function test_get_custom_login_page_url_returns_default_if_no_options_set()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['functions']->expects($this->any())->method('get_option')->with($this->equalTo('membersignup_options'))->will($this->returnValue(null));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$cu = $i->get_custom_login_page_url();
		$this->assertEquals('default', $cu);
	}
	public function test_get_custom_login_page_url_returns_default_if_no_custom_login_page_url_set()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['functions']->expects($this->any())->method('get_option')->with($this->equalTo('membersignup_options'))->will($this->returnValue(array(
			'foo' => 'baz'
		)));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$cu = $i->get_custom_login_page_url();
		$this->assertEquals('default', $cu);
	}
	public function test_get_custom_login_page_url_returns_set_custom_login_page_url()
	{
		$mocks = $this->get_mock_adapters();
		// expect the get_option method once and return baz
		$mocks['functions']->expects($this->any())->method('get_option')->with($this->equalTo('membersignup_options'))->will($this->returnValue(array(
			'custom_member_login_page_url' => 'baz'
		)));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$cu = $i->get_custom_login_page_url();
		$this->assertEquals('baz', $cu);
	}
	public function test_redirects_to_set_custom_login_page_url()
	{
		// get the mock adapters
		$mocks = $this->get_mock_adapters();
		// set 'is_user_logged_in' to return false
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(false));
		// pagenow is 'wp-login.php'
		$mocks['globals']->expects($this->once())->method('globals')->with($this->equalTo('pagenow'))->will($this->returnValue('wp-login.php'));
		// options are set to something
		$mocks['functions']->expects($this->once())->method('get_option')->will($this->returnValue(array(
			'custom_member_login_page_url' => 'something'
		)));
		// expect wp_redirect call
		$mocks['functions']->expects($this->once())->method('wp_redirect')->with($this->equalTo('something'));
		// get a class instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// since the method exits overload the exit
		// set_exit_overload();
		// call redirect
		$i->redirect_to_member_login();
		// unset the exit overload
		// unset_exit_overload();
		
	}
	public function test_should_redirect_returns_true_for_all_null()
	{
		// get the global mock and do not set because will return null by default
		$mocks = $this->get_mock_adapters();
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	public function test_should_not_redirect_for_wp_submit_not_null()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->once())->method('post')->with($this->equalTo('wp_submit'))->will($this->returnValue('not_null'));
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$r = $i->should_redirect();
		$this->assertFalse($r);
	}
	public function test_should_redirect_for_non_null_but_not_logout_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(1))->method('get')->with($this->equalTo('action'))->will($this->returnValue('not_logout'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	public function test_should_not_redirect_for_non_null_and_logout_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(1))->method('get')->with($this->equalTo('action'))->will($this->returnValue('logout'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertFalse($r);
	}
	public function test_should_redirect_for_non_null_but_not_checkemail_confirm_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(2))->method('get')->with($this->equalTo('checkemail'))->will($this->returnValue('not_confirm'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	public function test_should_not_redirect_for_non_null_and_checkemail_confirm_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(2))->method('get')->with($this->equalTo('checkemail'))->will($this->returnValue('confirm'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertFalse($r);
	}
	public function test_should_redirect_for_non_null_but_not_checkemail_registered_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(2))->method('get')->with($this->equalTo('checkemail'))->will($this->returnValue('not_registered'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	public function test_should_not_redirect_for_non_null_and_checkemail_registered_action()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['globals']->expects($this->at(2))->method('get')->with($this->equalTo('checkemail'))->will($this->returnValue('registered'));
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		$r = $i->should_redirect();
		$this->assertFalse($r);
	}
	public function tearDown()
	{
		membersignup_Redirect_Controller::unset_instance();
	}
}
?>s
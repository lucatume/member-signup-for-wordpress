<?php
class redirectControllerTest extends Adapter_Classes_TestCase
{
	protected $all_adapters = array(
		'functions',
		'globals',
		'filters',
		'options'
	);
	public function setUp()
	{
		// do something before each test
		
	}
	public function test_invokes_add_action_only_once()
	{
		// create a mock for the 'Filters' class
		$mock_filter = $this->getMock('adclasses_Filters');
		// expects 'add_action' to be called once
		$mock_filter->expects($this->once())->method('add_action');
		$a = membersignup_Redirect_Controller::get_instance(array(
			'filters' => $mock_filter
		));
		$b = membersignup_Redirect_Controller::get_instance();
	}
	public function test_redirect_returns_for_logged_users()
	{
		// get mock adapters
		$mocks = $this->get_mock_adapters(array(
			'functions',
			'globals',
			'filters'
		));
		// set is_user_logged_in
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(true));
		// set pagenow not to be called
		$mocks['globals']->expects($this->never())->method('pagenow');
		// get an instance and inject mocks
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$i->redirect_to_member_login();
	}
	public function test_does_not_redirect_if_wp_login()
	{
		$mocks = $this->get_mock_adapters($this->all_adapters);
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(false));
		$mocks['globals']->expects($this->once())->method('pagenow')->will($this->returnValue('wp-login.php'));
		$mocks['functions']->expects($this->never())->method('wp_redirect');
		// get an instance and inject mocks
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// run the method
		$i->redirect_to_member_login();
	}
	public function test_redirect_uses_set_custom_login_page_url()
	{
		// get the mocks used by the function;
		$mocks = $this->get_mock_adapters($this->all_adapters);
		// is_user_logged_in returns true
		$mocks['functions']->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(false));
		// pagenow is not 'wp_login.php'
		$mocks['globals']->expects($this->once())->method('pagenow')->will($this->returnValue('foo'));
		// custom login page is not ''
		$options = array(
			'custom_member_login_page_url' => 'foo'
		);
		$mocks['options']->expects($this->once())->method('get_option')->with($this->equalTo('membersignup_options') , $this->equalTo(false))->will($this->returnCallback(function ($options)
		{
			
			return $options;
		}));
		$mocks['options']->expects($this->once())->method('get_option')->with($this->equalTo('membersignup_options') , $this->equalTo(false))->will($this->returnCallback($options));
		// expect wp_redirect to be called with custom login url
		$mocks['functions']->expects($this->once())->method('wp_redirect')->with($this->equalTo('foo'))->will($this->returnVale]ue());
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$i->redirect_to_member_login();
	}
	public function test_should_redirect_returns_true_for_all_null()
	{
		// get the global mock and do not set because will return null by default
		$mocks = $this->get_mock_adapters($this->all_adapters);
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	public function test_should_redirect_returns_false_when_wp_submit_not_null()
	{
		// get the globals
		$mocks = $this->get_mock_adapters($this->all_adapters);
		$mocks['globals']->expects($this->once())->method('post')->with($this->equalTo('wp_submit'))->will($this->returnValue('not_null'));
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$r = $i->should_redirect();
		$this->assertFalse($r);
	}
	public function test_should_redirect_returns_true_when_get_not_null_but_not_valid()
	{
		// get the globals
		$mocks = $this->get_mock_adapters($this->all_adapters);
		$mocks['globals']->expects($this->at(0))->method('get')->with($this->equalTo('action'))->will($this->returnValue('not_valid'));
		$mocks['globals']->expects($this->at(1))->method('get')->with($this->equalTo('action'))->will($this->returnValue('not_valid'));
		// get the instance
		$i = membersignup_Redirect_Controller::get_instance($mocks);
		// call function
		$r = $i->should_redirect();
		$this->assertTrue($r);
	}
	// public function test_should_redirect_returns_false_when_get_not_null_and_valid()
	// {
	// 	// get the globals
	// 	$mocks = $this->get_mock_adapters($this->all_adapters);
	// 	$mocks['globals']->expects($this->at(0))->method('get')->with($this->equalTo('action'))->will($this->returnValue('logout'));
	// 	$mocks['globals']->expects($this->at(1))->method('get')->with($this->equalTo('action'))->will($this->returnValue('logout'));
	// 	// get the instance
	// 	$i = membersignup_Redirect_Controller::get_instance($mocks);
	// 	// call function
	// 	$r = $i->should_redirect();
	// 	$this->assertFalse($r);
	// }
	public function tearDown()
	{
		membersignup_Redirect_Controller::unset_instance();
	}
}
?>
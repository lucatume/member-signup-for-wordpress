<?php
class redirectControllerTest extends PHPUnit_Framework_TestCase
{
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
		$this->assertTrue($mock_filter === $a->filters, 'should be using injected filter');
		$this->assertTrue($mock_filter === $b->filters, 'should be using injected filter');
	}
	public function test_redirect_returns_for_logged_users()
	{
		// mock Functions
		$mock_functions = $this->getMock('adclasses_Functions');
		// mock Globals
		$mock_globals = $this->getMock('adclasses_Globals');
		// set is_user_logged_in
		$mock_functions->expects($this->once())->method('is_user_logged_in')->will($this->returnValue(true));
		// set pagenow not to be called
		$mock_globals->expects($this->never())->method('pagenow');
		// get an instance and inject mocks
		$i = membersignup_Redirect_Controller::get_instance(array(
			'functions' => $mock_functions,
			'globals' => $mock_globals
		));
		// run the method
		$i->redirect_to_member_login();
	}
	public function tearDown()
	{
		membersignup_Redirect_Controller::unset_instance();
	}
}
?>
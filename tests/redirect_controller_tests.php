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
	public function tearDown()
	{
		membersignup_Redirect_Controller::unset_instance();
	}
}
?>
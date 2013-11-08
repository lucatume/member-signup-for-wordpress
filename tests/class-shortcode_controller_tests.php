<?php
class Shortcode_Controller_Test extends Adapter_Classes_TestCase
{
	public function setUp()
	{
		$this->mocks = $this->get_mock_adapters();
		// add the View mock to the mocks bundle
		$this->mocks['view'] = $this->getMock('membersignup_Shortcode_View');
	}
	public function test_adds_shortcode()
	{
		$this->mocks['functions']->expects($this->once())->method('add_shortcode')->will($this->returnValue(true));
		$sut = membersignup_Shortcode_Controller::get_instance($this->mocks);
		$this->assertNotNull($sut);
		$this->assertNotNull($sut->view);
	}
	public function test_calls_view_to_display_login_form()
	{
		$this->mocks['view']->expects($this->once())->method('get_view');
		$instance = membersignup_Shortcode_Controller::get_instance($this->mocks);
		$instance->display_login_form(array() , null);
	}
	public function tearDown()
	{
		membersignup_Shortcode_Controller::unset_instance();
		unset($this->mocks);
	}
}
?>
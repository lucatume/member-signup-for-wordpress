<?php
class Shortcode_Controller_Test extends Adapter_Classes_TestCase
{
	public function setUp()
	{
		$this->mock_adapters = $this->get_mock_adapters();
	}
	public function test_adds_shortcode(){
		$this->mock_adapters['filters']->expects($this->once())->method('add_shortcode');
		$sut = membersignup_Shortcode_Controller::get_instance($this->mock_adapters);
		$this->assertNotNull($sut);
		$this->assertNotNull($sut->view);
	}
	public function test_calls_view_to_display_login_form()
	{
		$mock_view = $this->getMock('membersignup_Shortcode_View');
		$mock_view->expects($this->once())->method('get_view');
		$instance = membersignup_Shortcode_Controller::get_instance($this->mock_adapters,$mock_view);
		$instance->display_login_form(array(),null);
	}
	public function tearDown()
	{
		membersignup_Shortcode_Controller::unset_instance();
	}
}
?>
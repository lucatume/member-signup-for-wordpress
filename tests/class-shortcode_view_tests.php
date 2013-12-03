<?php
class membersignup_Shortcode_View_Test extends Adapter_Classes_TestCase
{
	public function setUp()
	{
		// your code here
		
	}
	public function test_get_view_calls_wp_login_form()
	{
		$mocks = $this->get_mock_adapters();
		$mocks['functions']->expects($this->once())->method('wp_login_form');
		$sut = new membersignup_Shortcode_View($mocks);
		$sut->get_view();
	}
	public function test_get_view_return_containg_wp_login_form_output()
	{
		$mocks = $this->get_mock_adapters();
		$out = '<form action="" id="member_login_form">something</form>';
		$mocks['functions']->expects($this->once())->method('wp_login_form')->will($this->returnValue($out));
		$sut = new membersignup_Shortcode_View($mocks);
		$markup = $sut->get_view();
		$this->assertNotNull($markup);
		$this->assertContains($out, $markup);
	}
	public function tearDown()
	{
		// your code here
		
	}
}
?>


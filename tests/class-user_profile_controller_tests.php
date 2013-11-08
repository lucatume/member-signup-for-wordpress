<?php
/**
 * Member Signup Plugin User Profile Controller class unit tests
 *
 * Tests for the member signup_User_Profile_Controller class
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
class membersignup_User_Profile_Controller_TestCase extends Adapter_Classes_TestCase
{
  public function setUp()
  {
    $this->mocks = $this->get_mock_adapters(array(
      'functions',
      'globals'
    ) , true);
  }
  public function test_can_add_mock_methods()
  {
    $this->mocks['functions']->expects($this->once())->method('mockery')->will($this->returnValue('me'));
    $this->mocks['functions']->mockery();
    $this->assertTrue(method_exists($this->mocks['functions'], 'mockery'));
  }
  public function test_hooks_into_admin_head()
  {
    $this->mocks['functions']->expects($this->once())->method('add_action')->with($this->equalTo('admin_head') , $this->any())->will($this->returnValue(true));
    $sut = membersignup_User_Profile_Controller::get_instance($this->mocks);
  }
  public function test_hooks_into_admin_footer()
  {
    $this->mocks['functions']->expects($this->once())->method('add_action')->with($this->equalTo('admin_footer') , $this->any())->will($this->returnValue(true));
    $sut = membersignup_User_Profile_Controller::get_instance($this->mocks);
  }
  public function tearDown()
  {
    unset($this->mocks);
    membersignup_User_Profile_Controller::unset_instance();
  }
}

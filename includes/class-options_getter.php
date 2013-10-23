<?php
/**
 * Option Getter classses
 *
 * A class to provide convenient and easy access to user-set plugin options
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */

/**
 * The main class provides static methods to retrieve plugin options
 */
class membersignup_Options_Getter{

	/**
	 * Returns the URL to the member login page
	 * @return string Eithere the URL of a user set page or the default login
	 */
	public static function get_member_login_page_url(){
		return get_home_url( '/member_login' );
	}

	/**
	 * Returns the URL to the member admin page
	 * @return string Eithere the URL of a user set page or the default admin
	 */
	public static function get_member_admin_page_url(){
		return get_home_url( '/member_admin' );
	}
}
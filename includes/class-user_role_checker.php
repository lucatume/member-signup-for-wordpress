<?php
/**
 * User Role Checker
 *
 * Classes dedicating to retrieving information about user roles
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
class membersignup_User_Role_Checker
{
	/**
	 * Checks if a particular user has a role.
	 * Returns true if a match was found.
	 *
	 * Original function by AppThemes:
	 * http://docs.appthemes.com/tutorials/wordpress-check-user-role-function/
	 *
	 * @param  string $role    Role name
	 * @param  int $user_id The ID of the user. Defaults to the current user.
	 * @return bool
	 */
	public static function is_user_of_role($role, $user_id = null)
	{
		if (is_numeric($user_id)) $user = get_userdata($user_id);
		else $user = wp_get_current_user();
		if (empty($user)) 
		return false;
		
		return in_array($role, (array)$user->roles);
	}
}

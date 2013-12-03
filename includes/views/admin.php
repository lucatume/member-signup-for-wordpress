<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

<form action="options.php" method="post">
	<?php settings_fields( 'membersignup_options' ); ?>
	<?php do_settings_sections( 'membersignup' ); ?>
	<input name="<?php esc_attr_e( 'Submit', 'membersignup' ); ?>" type="Submit" value="<?php esc_attr_e( 'Save Changes', 'membersignup' ); ?>">
</form>

</div>

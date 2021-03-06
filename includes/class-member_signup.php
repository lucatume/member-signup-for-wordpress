<?php
/**
 * Plugin Name.
 *
 * @package   membersignup
 * @author    theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 * @license   GPL-2.0+
 * @link      http://theaveragedev.com
 * @copyright 2013 theAverageDev (Luca Tumedei)
 */
/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-membersignup-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package membersignup
 * @author  theAverageDev (Luca Tumedei) <luca@theaveragedev.com>
 */
class membersignup
{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';
	/**
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'membersignup';
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct($adapters = null)
	{
		// if no adapters are passed
		if (!is_array($adapters)) {
			// call factory by itself
			$adapters = adclasses_Factory::get_instance()->get_adapters(array(
				'filters',
				'functions',
				'globals'
			));
		}
		
		foreach ($adapters as $slug => $value) {
			$this->{$slug} = $value;
		}
		// Load plugin text domain
		$this->functions->add_action('init', array(
			$this,
			'load_plugin_textdomain'
		));
		// Activate plugin when new blog is added
		$this->functions->add_action('wpmu_new_blog', array(
			$this,
			'activate_new_site'
		));
	}
	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug()
	{
		
		return $this->plugin_slug;
	}
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance($adapters = null)
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self($adapters);
		}
		
		return self::$instance;
	}
	/**
	 * Unsets the static instance
	 * @return none
	 */
	public static function unset_instance()
	{
		if (isset(self::$instance)) {
			self::$instance = null;
		}
	}
	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate($network_wide)
	{
		if (function_exists('is_multisite') && is_multisite()) {
			if ($network_wide) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				
				foreach ($blog_ids as $blog_id) {
					switch_to_blog($blog_id);
					self::single_activate();
				}
				restore_current_blog();
			}
			else {
				self::single_activate();
			}
		}
		else {
			self::single_activate();
		}
	}
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate($network_wide)
	{
		if (function_exists('is_multisite') && is_multisite()) {
			if ($network_wide) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				
				foreach ($blog_ids as $blog_id) {
					switch_to_blog($blog_id);
					self::single_deactivate();
				}
				restore_current_blog();
			}
			else {
				self::single_deactivate();
			}
		}
		else {
			self::single_deactivate();
		}
	}
	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site($blog_id)
	{
		if (1 !== $this->functions->did_action('wpmu_new_blog')) {
			
			return;
		}
		$this->functions->switch_to_blog($blog_id);
		self::single_activate();
		$this->functions->restore_current_blog();
	}
	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids()
	{
		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0'
		AND deleted = '0'";
		global $wpdb;
		
		return $wpdb->get_col($sql);
	}
	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate()
	{
		$user_role_slug = 'member';
		/**
		 * Remove the user role if previously defined
		 */
		if (get_role($user_role_slug)) {
			remove_role($user_role_slug);
		}
		/**
		 * Add the member role to the database
		 */
		add_role($user_role_slug, esc_html__('Member', 'membersignup') , array(
			'read' => true
		));
	}
	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate()
	{
		// TODO: Define deactivation functionality here
		
	}
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = $this->functions->apply_filters('plugin_locale', $this->functions->get_locale() , $domain);
		$this->functions->load_textdomain($domain, $this->functions->trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		$this->functions->load_plugin_textdomain($domain, false, basename(dirname(__FILE__)) . '/languages');
	}
}
?>
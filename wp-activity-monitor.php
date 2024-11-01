<?php
/*
* Plugin Name: WP Activity Monitor
* Author: Premium WP Suite & Hawkeye Design
* Version: 15.06-17
* Plugin URI: http://www.premiumwpsuite.com
* Author URI: http://www.premiumwpsuite.com
* Description: Easily track all activity on your WordPress site and notify upon activity detection. Tracks login attempts, registrations, comment actions, post/page actions and much more...
*/

// Defines
define('WPS_AM_SLUG', 'wps-am');
define('WPS_AM_PLUGIN_URL', plugins_url('/', __FILE__));
define('WPS_AM_SETTINGS', WPS_AM_SLUG . '-settings');
define('WPS_AM_LOG', 'wps_am_events_log');

// Autoloader
spl_autoload_register(function($class){
  if ($class == 'wps_am_core_pro') {
	return;
  }
  
  if (strpos($class, 'wps_am') !== false) {

	$class = str_replace('wps_am_', '', $class);

	if (file_exists($class . '.php')) {
	  require_once($class . '.php');
	} else {
	  $class = str_replace('am_', '', $class);
	  require_once($class . '.php');
	}

  }
});

// Call common
require_once 'common.php';

// Main Class
class wps_am_core {

  static $version = '15.06-17';

  public static function init() {

	if (is_admin()) {

	  $wps_admin_menu = new wps_am_admin_menu();
	  $wps_admin_menu->init();

	  $wps_scripts = new wps_am_scripts();
	  $wps_scripts->init();

	  $wps_am_settings = new wps_am_settings();
	  $wps_am_settings->init();

	  $wps_am_ajax = new wps_am_ajax();
	  $wps_am_ajax->init();

	}
	
	// Hook on all actions
	add_action('all', array('wps_am_actions', 'watch_actions'), 9, 10);


  } // init


  public static function install() {
	$wps_am_settings = new wps_am_settings();
	$wps_am_settings->install_defaults();

	$wps_am_log = new wps_am_actions();
	$wps_am_log->install();
  } // install


} // wps_activity_monitor

add_action('init', array('wps_am_core', 'init'));
register_activation_hook(__FILE__, array('wps_am_core', 'install'));
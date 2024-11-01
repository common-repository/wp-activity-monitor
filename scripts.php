<?php
/*
WP Activity Monitor - Scripts
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_am_scripts extends wps_am_core {
  
  
  public static function init() {
	
	self::admin_enqueue();
	
  } // init
  
  
  public static function admin_enqueue() {
	if (is_admin()) {
	  
	  // Switch
	  wp_enqueue_script(WPS_AM_SLUG . '-switch', WPS_AM_PLUGIN_URL . 'assets/switch/switch.min.js', array('jquery'), parent::$version, true);
	  wp_enqueue_style(WPS_AM_SLUG . '-switch', WPS_AM_PLUGIN_URL . 'assets/switch/switch.min.css', array(), parent::$version);
	  wp_enqueue_script(WPS_AM_SLUG . '-switch-tpl', WPS_AM_PLUGIN_URL . 'assets/switch/jquery.tpl.js', array(), parent::$version, true);
	  
	  // Select2
	  wp_enqueue_script(WPS_AM_SLUG . '-select2', WPS_AM_PLUGIN_URL . 'assets/select2/js/select2.full.min.js', array('jquery'), parent::$version, true);
	  wp_enqueue_style(WPS_AM_SLUG . '-select2', WPS_AM_PLUGIN_URL . 'assets/select2/css/select2.min.css', array(), parent::$version);
	  
	  // Font Icons
	  wp_enqueue_style(WPS_AM_SLUG . '-fonts', WPS_AM_PLUGIN_URL . 'assets/fonts/css/font-awesome.min.css', array(), parent::$version);
	  
	  
	  // Plugin Stuff
	  wp_enqueue_script(WPS_AM_SLUG . '-admin', WPS_AM_PLUGIN_URL . 'js/admin.js', array('jquery'), parent::$version, true);
	  wp_enqueue_style(WPS_AM_SLUG . '-admin', WPS_AM_PLUGIN_URL . 'css/admin.css', array(), parent::$version);
	  
	  // Scripts
	  wp_enqueue_script('jquery');
	  wp_enqueue_script('jquery-ui-core');
	  wp_enqueue_script('jquery-ui-tabs');
	  wp_enqueue_script('jquery-ui-dialog');

	  // Styles
	  wp_enqueue_style('jquery-ui');
	  wp_enqueue_style('jquery-ui-core');
	  wp_enqueue_style('jquery-ui-tabs');
	  wp_enqueue_style('wp-jquery-ui-dialog');
	  
	}
  } // admin_enqueue
  
  
} // wps_am_scripts
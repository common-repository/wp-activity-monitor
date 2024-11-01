<?php
/*
WP Activity Monitor - Ajax
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/


if ( ! defined( 'ABSPATH' ) ) exit;

class wps_am_ajax extends wps_am_core {
  
  
  public static function init() {
	
	self::ajax_register();
	
  } // init
  
  
  public static function ajax_register() {
	if (is_admin()) {
	  
	  add_action('wp_ajax_wps_am_save_settings', array(__CLASS__, 'save_settings'));
	  
	}
  } // ajax_register
  
  
  public static function save_settings() {
	
	$params = array();
	parse_str($_POST['form'], $params);
	update_option(WPS_AM_SETTINGS, $params['wps-am']);
	
	die();
  } // save_settings
  
  
} // wps_am_ajax
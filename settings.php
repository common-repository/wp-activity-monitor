<?php
/*
WP Activity Monitor - Settings
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_am_settings extends wps_am_core {
  
  
  public static function init() {
	
	self::add_tab();
	self::get_settings();
	
  } // init
  
  
  public static function get_settings() {
	global $wps_am_settings;
	$wps_am_settings = get_option(WPS_AM_SETTINGS);
  } // get_settings
  
  
  public static function global_defaults() {
	global $wps_am_default_settings;
	
	$wps_am_default_settings['notification-email-status'] = '0';
	$wps_am_default_settings['notification-email'] = get_bloginfo('admin_email');
	$wps_am_default_settings['email-events'] = 'activate_plugin';
	$wps_am_default_settings['notification-sms-status'] = '0';
	$wps_am_default_settings['notification-sms'] = '';
	$wps_am_default_settings['sms-events'] = 'switch_theme';
	
  } // global_defaults
  
  
  public static function set_email_events() {
	$events = array();
	
	$events['installer-actions'] = array();
	$events['installer-actions']['parent'] = 'Installer Actions';
	$events['installer-actions']['activate_plugin'] = 'Plugin Activation';
	$events['installer-actions']['deactivate_plugin'] = 'Plugin Deactivation';
	$events['installer-actions']['switch_theme'] = 'Theme Change';
	$events['installer-actions']['_core_updated_successfully'] = 'WordPress Core Update';
	
	$events['user-actions'] = array();
	$events['user-actions']['parent'] = 'User Actions';
	$events['user-actions']['login_enqueue_scripts'] = 'Opened WP Login page';
	$events['user-actions']['wp_login_failed'] = 'Failed login attempt';
	$events['user-actions']['set_logged_in_cookie'] = 'Successful login';
	$events['user-actions']['user_register'] = 'New User Registered';
	$events['user-actions']['retrieve_password'] = 'Password reset request';
	$events['user-actions']['password_reset'] = 'Password reset';
	$events['user-actions']['set_user_role'] = 'Update User Role';
	
	return $events;
  } // set_email_events  
  
  
  public static function set_sms_events() {
	$events = array();
	
	$events['installer-actions'] = array();
	$events['installer-actions']['parent'] = 'Installer Actions';
	$events['installer-actions']['activate_plugin'] = 'Plugin Activation';
	$events['installer-actions']['deactivate_plugin'] = 'Plugin Deactivation';
	$events['installer-actions']['switch_theme'] = 'Theme Change';
	$events['installer-actions']['_core_updated_successfully'] = 'WordPress Core Update';
	
	$events['user-actions'] = array();
	$events['user-actions']['parent'] = 'User Actions';
	$events['user-actions']['login_enqueue_scripts'] = 'Opened WP Login page';
	$events['user-actions']['wp_login_failed'] = 'Failed login attempt';
	$events['user-actions']['set_logged_in_cookie'] = 'Successful login';
	$events['user-actions']['user_register'] = 'New User Registered';
	$events['user-actions']['retrieve_password'] = 'Password reset request';
	$events['user-actions']['password_reset'] = 'Password reset';
	$events['user-actions']['set_user_role'] = 'Update User Role';
	
	return $events;
  } // set_sms_events
  
  
  public static function install_defaults($force = false) {
	global $wps_am_default_settings;
	
	self::global_defaults();
	$settings = get_option(WPS_AM_SETTINGS);
	
	if (!$settings || empty($settings)) {
	  update_option(WPS_AM_SETTINGS, $wps_am_default_settings);
	}
	
  } // install_defaults
  
  
  public static function add_tab() {
	add_action('wps_am_tabs', array(__CLASS__, 'tab_title'));
	add_action('wps_am_tab_content', array(__CLASS__, 'tab_content'));
  } // add_tab
  
  
  public static function tab_title() {
	echo '<li><a href="#settings">Settings</a></li>';
  } // tab_title  
  
  
  public static function tab_content() {
	require_once 'templates/pages/settings.php';
  } // tab_content
  
  
} // wps_am_settings
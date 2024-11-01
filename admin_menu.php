<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_am_admin_menu extends wps_am_core {
  
  
  public static function init() {
	self::add_admin_menu();
  } // init
  
  
  public static function add_admin_menu() {
	add_action('admin_menu', array(__CLASS__, 'render_admin_menu'));
  } // add_admin_menu
  
  
  public static function render_admin_menu() {
	add_menu_page('Activity Monitor', 'Activity Monitor', 'manage_options', 'wps-am', array(__CLASS__, 'admin_render_page'), 'dashicons-chart-line');
	add_submenu_page('wps-am', 'Log', 'Activity Log', 'manage_options', 'wps-am-log', array(__CLASS__, 'admin_render_log'));
  } // render_admin_menu
  
  
  public static function admin_render_log() {
	include 'templates/log_page.php';
  } // admin_render_log
  
  
  public static function admin_render_page() {
	include 'templates/admin_page.php';
  } // admin_render_page
  
  
} // wps_admin_menu
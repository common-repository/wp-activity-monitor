<?php
/*
WP Activity Monitor - Common Functions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function wps_am_field_value($field_type, $field_name) {
  global $wps_am_settings;
  
  $value = '';
  
  if (!empty($wps_am_settings[$field_name])) {
	
	if ($field_type == 'checkbox') {
	  $value = 'checked="checked"';
	} elseif ($field_type == 'text') {
	  $value = $wps_am_settings[$field_name];
	}
	
  } else {
  	// return empty for all
  	$value = '';
  }
  
  echo $value;
} // wps_field_value


function wps_am_print_select($values, $setting_name, $grouped = true) {
  global $wps_am_settings;
  $setting = $wps_am_settings[$setting_name];

  if ($grouped) { 
    foreach ($values as $parent_key => $child_values) {
	  echo '<optgroup label="' . $child_values['parent'] . '">';
	  
	  foreach ($child_values as $child_key => $child_value) {
	  	
	  	if ($child_key == 'parent') continue;
	  	
	  	if (in_array($child_key, $setting)) {
		  echo '<option value="' . $child_key . '" selected="selected">' . $child_value . '</option>';
		} else {
		  echo '<option value="' . $child_key . '">' . $child_value . '</option>';
		}
	  }
	  
	  echo '</optgroup>';
    }
  }
  
  
}
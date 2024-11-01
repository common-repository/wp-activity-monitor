<?php
/*
WP Activity Monitor - Actions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_am_actions extends wps_am_core {
  
  
  public static function write($event_type, $event_action, $event_params, $description, $user_id) {
    global $wpdb;

    
    if (!empty($event_params)) {
      var_dump(json_encode($event_params));
      $event_params = json_encode($event_params);
    }
    
    // Send notification
    self::send_notification(array('event_type' => $event_type, 
      	  	  	  	  	  	  	  'event_action' => $event_action, 
      	  	  	  	  	  	  	  'description' => $description, 
      	  	  	  	  	  	  	  'user_ID' => $user_id,
      	  	  	  	  	  	  	  'ip' => $_SERVER['REMOTE_ADDR'],
      	  	  	  	  	  	  	  'date' => current_time('mysql')));

    $insert = $wpdb->insert($wpdb->prefix . WPS_AM_LOG,
              array(
                'date' => current_time('mysql'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_ID' => $user_id,
                'event_type' => $event_type,
                'event_action' => $event_action,
                'event_params' => $event_params,
                'description' => $description),
              array('%s', '%s', '%d', '%s', '%s', '%s', '%s'));
              
    return;
  } // write_log
  
  
  public static function send_notification($params) {
	$options = get_option(WPS_AM_SETTINGS);
	
	if (!empty($options)) {
	  if (!empty($options['notification-email-status']) && $options['notification-email-status'] == '1') {
		
		// Send E-mail
		if (!empty($options['notification-email'])) {
		  
		  $to = $options['notification-email'];
		  
		  $message = "WP Activity Monitor has detected an event.\r\n";
		  $message .= "--------------\r\n";
		  $message .= "Date: " . $params['date'] . "\r\n";
		  $message .= "IP: " . $params['ip'] . "\r\n";
		  $message .= "Event Type: " . $params['event_type'] . "\r\n";
		  $message .= "Event Action: " . $params['event_action'] . "\r\n";
		  $message .= "Event Description: " . strip_tags($params['description']) . "\r\n";
		  
		  if (empty($params['user_ID']) || $params['user_ID'] == '0') {
		  	$user = 'Unknown user.'; 
		  } else {
			$user = get_user_by('id', $params['user_ID']);
			$user = $user->user_login . ' <' . $user->user_email . '>';
		  }
		  
		  $message .= "User: " . $user . "\r\n";
		  
		  wp_mail($to, 'Event Detected', $message);
		}
		
	  }
	}
	
  } // send_notification
  
  
  public static function watch_actions() {

    $user_actions = array('user_register',
                          'wp_login_failed',
                          'profile_update',
                          'password_reset',
                          'retrieve_password',
                          'set_logged_in_cookie',
                          'clear_auth_cookie',
                          'delete_user',
                          'deleted_user',
                          'set_user_role',
                          'login_enqueue_scripts');
                   
    $file_editor_actions = array('wp_redirect');
                        
    $media_actions = array('add_attachment',
                           'edit_attachment',
                           'delete_attachment',
                           'wp_save_image_editor_file');

    $installer_actions = array('upgrader_process_complete',
                               'activate_plugin',
                               'deactivate_plugin',
                               'switch_theme',
                               '_core_updated_successfully');
                       
    $comments_actions = array('comment_flood_trigger',
                              'wp_insert_comment',
                              'edit_comment',
                              'delete_comment',
                              'trash_comment',
                              'untrash_comment',
                              'spam_comment',
                              'unspam_comment',
                              'transition_comment_status',
                              'comment_duplicate_trigger');
                      
    $settings_actions = array('whitelist_options',
                              'update_site_option',
                              'update_option_permalink_structure',
                              'update_option_category_base',
                              'update_option_tag_base');

    $posts_actions = array('transition_post_status',
                           'deleted_post');

    // Get args
    $args = func_get_args();
    
    // What is happening?
    if (in_array(current_action(), $user_actions)) {
      
      // User actions
      self::user_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $file_editor_actions)) {
      
      // File Editor Actions
      self::file_editor_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $media_actions)) {
      
      // Media Actions
      self::media_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $installer_actions)) {
      
      // Installer Actions
      self::installer_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $comments_actions)) {
      
      // Comments Actions
      self::comments_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $settings_actions)) {
      
      // Settings Actions
      self::settings_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $posts_actions)) {

      // Posts Actions
      self::posts_actions(current_action(), $args);
    }
    
  } // watch_actions


  public static function user_actions($action, $params) {
    $description = '';
    $user_id     = null;

    switch ($action) {
      // Opened Login Page
      case 'login_enqueue_scripts';
        $description = 'Opened WP Login page.';
      break;
      // Failed to login with username
      case 'wp_login_failed':
        $description = 'Failed login attempt with username <strong>' . $params[1] . '</strong>.';
      break;
      // User logged in with username
      case 'set_logged_in_cookie':
        $user = get_user_by('id', $params[4]);
        $description = '<strong>' . $user->user_login . '</strong> logged in.';
        $user_id = $user->ID;
      break;
      // User logged out with username
      case 'clear_auth_cookie':
        $user = wp_get_current_user();
        if (empty($user) || ! $user->exists()) {
          return;
        }
        $user_id = $user->ID;
        $description = '<strong>' . $user->user_login . '</strong> logged out.';
      break;
      // User registered
      case 'user_register':
        $user = get_user_by('id', $params[1]);
        $user_id = $user->ID;
        $description = 'New user registered - ' . $user->user_login . '.';
      break;
      // User updated profile
      case 'profile_update':
        $user = get_user_by('id', $params[1]);
        $user_id = $user->ID;
        $description = '<strong>' . $user->user_login . '\'s</strong> profile was updated.';
      break;
      // User requested password reset
      case 'retrieve_password':
        $description = '<strong>' . $params[1] . '\'s</strong> password was requested to be reset.';
      break;
      // User reset the password
      case 'password_reset':
        $user = get_user_by('user_login', $params[1]->data->user_login);
        $user_id = $user->ID;
        $description = '<strong>' . $params[1]->data->user_login . '\'s</strong> password was reset.';
      break;
      // User with username has been deleted
      case 'delete_user':
        $user = get_user_by('id', $params[1]);
        $user_id = $user->ID;
        $description = '<strong>' . $user->display_name . '\'s</strong> account was deleted.';
      break;
      // User with username has been deleted
      case 'deleted_user':
        $description = 'Unknown description.';
      break;
      // User with username changed role
      case 'set_user_role':
        if (!isset($params[3][0]) || !$params[3][0]) {
          return;
        }
        $user = get_user_by('id', $params[1]);
        $user_id = $user->ID;
        $description = '<strong>' . $user->display_name . '\'s</strong> role was changed from ' . $params[3][0] . ' to ' . $params[2] . '.';
      break;
        // Unknown action
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    $event = '';
    switch($action) {
	  case 'wp_login_failed':
	    $event = 'failed-login';
	  break;
	  case 'password_reset':
	    $event = 'password-reset';
	  break;
	  case 'set_logged_in_cookie':
	    $event = 'successful-login';
	  break;
    }
    
    self::write('user', $action, $params, $description, $user_id);
  } // user_actions
  
  
  public static function file_editor_actions($action, $params) {
    $description = '';
    $user  = wp_get_current_user();

    switch ($action) {
      case 'wp_redirect':
        if (strpos($params[1], 'plugin-editor.php?') !== false) {

          list($url, $query) = explode('?', $params[1]);
          $query = wp_parse_args($query);
          $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $query['file']);

          if (!$plugin['Name']) {
            return;
          }

          $description = 'File <strong>' . $query['file'] . '</strong> in plugin <strong>' . $plugin['Name'] . '</strong> edited.';

        } elseif (strpos($params[1], 'theme-editor.php?') !== false) {

          list($url, $query) = explode('?', $params[1]);
          $query = wp_parse_args($query);
          $theme = wp_get_theme($query['theme']);

          if (!$theme->exists() || ($theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code())) {
            return;
          }

          $description = 'File <strong>' . $query['file'] . '</strong> in theme <strong>' . $theme->get('Name') . '</strong> edited.';

        } else {
          return;
        }
      break;
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('editor', $action, $params, $description, $user->ID);
  } // file_editor_actions
  
  
  public static function media_actions($action, $params) {
    $description = '';
    $user  = wp_get_current_user();

    switch ($action) {
      case 'add_attachment':
        $media = get_post($params[1]);
        $description = 'Added media <strong>' . $media->post_title . '</strong>.';
      break;
      case 'edit_attachment':
        $media = get_post($params[1]);
        $description = 'Updated media <strong>' . $media->post_title . '</strong>.';
      break;
      case 'delete_attachment':
        $media = get_post($params[1]);
        $description = 'Deleted media <strong>' . $media->post_title . '</strong>.';
      break;
      case 'wp_save_image_editor_file':
        $media = get_post($params[5]);
        $description = 'Edited image <strong>' . $media->post_title . '</strong>.';
      break;
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('editor', $action, $params, $description, $user->ID);
  } // media_actions  
  
  
  public static function installer_actions($action, $params) {
    $description = '';
    $user  = wp_get_current_user();

    switch ($action) {
      // Activate plugin
      case 'activate_plugin':
        $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $params[1]);
        
        if (!$plugin['Name']) {
          #return;
        }

        $description = 'Plugin <strong>' . $plugin['Name'] . '</strong> activated.';
      break;
      // Deactivate plugin
      case 'deactivate_plugin':
        $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $params[1]);

        if (!$plugin['Name']) {
          #return;
        }

        $description = 'Plugin <strong>' . $plugin['Name'] . '</strong> deactivated.';
      break;
      // Change theme
      case 'switch_theme':
        $description = 'Theme <strong>' . $params[1] . '</strong> activated.';
      break;
      // WP Core Update
      case '_core_updated_successfully':
        $description = 'WordPress core updated to v'. $params[1] . '.';
      break;
      // Update Theme/Plugin
      case 'upgrader_process_complete':
        // If not theme/plugin return
        if (@$params[2]['action'] != 'update' || (@$params[2]['type'] != 'plugin' && @$params[2]['type'] != 'theme')) {
          #return;
        }

        // if theme and bulk
        if (@$params[2]['type'] == 'theme' && isset($params[2]['themes']) && @$params[2]['action'] == 'update' && isset($params[2]['bulk']) &&$params[2]['bulk']) {
          foreach ($params[2]['themes'] as $theme_name) {
            $theme = wp_get_theme($theme_name);
            if (!$theme->exists() || ($theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code())) {
              #return;
            }
            $description[] = 'Theme <strong>' . $theme->get('Name') . '</strong> updated.';
          } // foreach themes
          #break;
        }

        // if theme and update
        if (@$params[2]['type'] == 'theme' && isset($params[2]['theme']) && @$params[2]['action'] == 'update') {
          $theme = wp_get_theme($params[2]['theme']);
          if (!$theme->exists() || ($theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code())) {
            #return;
          }
          $description = 'Theme <strong>' . $theme->get('Name') . '</strong> updated.';
          break;
        }

        // If pluginb and bulk
        if (isset($params[2]['plugins']) && is_array($params[2]['plugins'])) {
          foreach ($params[2]['plugins'] as $plugin_file) {
            $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
            if (!$plugin['Name']) {
              #return;
            }
            $description[] = 'Plugin <strong>' . $plugin['Name'] . '</strong> updated.';
          }
        } elseif (isset($params[2]['plugin'])) {
          $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $params[2]['plugin']);
          if (!$plugin['Name']) {
            #return;
          }
          $description = 'Plugin <strong>' . $plugin['Name'] . '</strong> updated.';
        } else {
          // Unkown plugin
          $description = 'Unknown plugin updated.';
        }
      break;
      // Unknown
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('installer', $action, $params, $description, $user->ID);
  } // installer_actions  
  
  
  public static function comments_actions($action, $params) {
    $description = '';
    $user  = wp_get_current_user();

    switch ($action) {
      // Comment duplicate trigger
      case 'comment_duplicate_trigger':
        $post_title = ($post = get_post($params[1]['comment_post_ID']))? $post->post_title : 'untitled';

        $description = 'Duplicate comment by <i>' . $params[1]['comment_author_email'] . '</i> prevented on <i>' . $post_title . '</i>.';
      break;

      // Comment flood trigger
      case 'comment_flood_trigger':
        $post_title = ($post = get_post(sanitize_text_field($_POST['comment_post_ID'])))? $post->post_title : 'untitled';

        $description = 'Comment flooding by <i>' . sanitize_text_field($_POST['email']) . '</i> prevented on <i>' . $post_title . '</i>.';
      break;

      // Insert comment
      case 'wp_insert_comment':
        $post_title = ($post = get_post($params[2]->comment_post_ID))? $post->post_title : 'untitled';

        if ($params[2]->comment_parent) {
          $description = 'New comment reply by <i>' . $params[2]->comment_author_email . '</i> created on <i>' . $post_title . '</i>.';
        } else {
          $description = 'New comment by <i>' . $params[2]->comment_author_email . '</i> created on <i>' . $post_title . '</i>.';
        }
      break;

      // Edit comment
      case 'edit_comment':
        $post_title = ($post = get_post(sanitize_text_field($_POST['comment_post_ID'])))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . sanitize_text_field($_POST['newcomment_author_email']) . '</i> on <i>' . $post_title . '</i> edited.';
      break;

      // Trash comment
      case 'trash_comment':
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($comment->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $comment->comment_author_email . '</i> on <i>' . $post_title . '</i> trashed.';
      break;

      // Untrash comment
      case 'untrash_comment':
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($comment->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $comment->comment_author_email . '</i> on <i>' . $post_title . '</i> restored.';
      break;

      // Delete comment
      case 'delete_comment':
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($comment->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $comment->comment_author_email . '</i> on <i>' . $post_title . '</i> permanently deleted.';
      break;

      // Spam comment
      case 'spam_comment':
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($comment->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $comment->comment_author_email . '</i> on <i>' . $post_title . '</i> marked as spam.';
      break;

      // Unspam comment
      case 'unspam_comment':
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($comment->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $comment->comment_author_email . '</i> on <i>' . $post_title . '</i> unmark as spam.';
      break;

      // Comment status changed
      case 'transition_comment_status':
        if ($params[1] != 'approved' && $params[1] != 'unapproved' || 'trash' == $params[2] || 'spam' == $params[2] ) {
          return;
        }
        $comment = get_comment($params[1]);
        $post_title = ($post = get_post($params[3]->comment_post_ID))? $post->post_title : 'untitled';
        $description = 'Comment by <i>' . $params[3]->comment_author_email . '</i> on <i>' . $post_title . '</i> ' . $params[1] . '.';
      break;

      // Unkown action
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('comments', $action, $params, $description, $user->ID);
  } // comments_actions
  
  
  public static function settings_actions($action, $params) {
    $description = '';
    $user = wp_get_current_user();

    switch ($action) {
      // Permalink update
      case 'update_option_permalink_structure':
        $description = 'Permalink settings updated.';
      break;

      // Whitelist update
      case 'whitelist_options':

        if (in_array(sanitize_text_field($_POST['option_page']), array('general', 'discussion', 'media', 'reading', 'writing'))) {
          $description = ucfirst(sanitize_text_field($_POST['option_page'])) . ' settings updated.';
        } else {
          $description = '<i>' . sanitize_text_field($_POST['option_page']) . '</i> settings updated.';
        }

      break;

      // Tag base option update
      case 'update_option_tag_base':
        $description = 'Tag base option updated.';
      break;

      // Category base option update
      case 'update_option_category_base':
        $description = 'Category base option updated.';
      break;

      // Site option
      case 'update_site_option':
        return;
      break;

      // Unkown
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('settings', $action, $params, $description, $user->ID);
  } // settings_actions


  public static function posts_actions($action, $params) {
    $description = '';
    $description_action = '';
    $raw_data = null;
    $user  = wp_get_current_user();

    switch ($action) {
      // Change status action
      case 'transition_post_status':

        $new = $params[1];
        $old = $params[2];

        if ($new == 'auto-draft' || $new == 'inherit') {
          return;
        } elseif ($old == 'auto-draft' && $new == 'draft' ) {
          $description_action = 'drafted';
        } elseif ($old == 'auto-draft' && ($new == 'publish' || $new == 'private')) {
          $description_action  = 'published';
        } elseif ($old == 'draft' && ($new == 'publish' || $new == 'private')) {
          $description_action = 'published';
        } elseif ($old == 'publish' && ($new == 'draft')) {
          $description_action = 'unpublished';
        } elseif ($new == 'trash') {
          $description_action  = 'trashed';
        } elseif ($old == 'trash' && $new != 'trash') {
          $description_action  = 'restored from trash';
        } else {
          $description_action = 'updated';
        }

        if (empty($params[3]->post_title)) {
          $title = 'no title';
        } else {
          $title = $params[3]->post_title;
        }

        if (post_type_exists($params[3]->post_type)) {
          $post_type = get_post_type_object($params[3]->post_type);
          $type = strtolower($post_type->labels->singular_name);
        } else {
          $type = 'post';
        }

        if (in_array($type, array('nav_menu_item', 'attachment', 'revision'))) {
          return;
        }

        $description = '<strong>' . $title . '</strong> ' . $type . ' ' . $description_action . '.';
      break;
      // Delete action
      case 'deleted_post':
        $post = get_post($params[1]);

        if (post_type_exists($post->post_type)) {
          $post_type = get_post_type_object($post->post_type);
          $type = strtolower($post_type->labels->singular_name);
        } else {
          $type = 'post';
        }

        if (in_array($type, array('nav_menu_item', 'attachment', 'revision'))) {
          return;
        }

        $description = '<strong>' . $post->post_title . '</strong> ' . $type . ' deleted from trash.';
      break;
      default:
        $description = 'Unknown action or filter - ' . $action . '.';
    }

    self::write('posts', $action, $params, $description, $user->ID);
  } // posts_actions
  
  
  public static function install() {
	global $wpdb;

    $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . WPS_AM_LOG . " (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
	`user_ID` int(11) DEFAULT NULL,
	`event_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`event_action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`event_params` text COLLATE utf8_unicode_ci NOT NULL,
	`description` text COLLATE utf8_unicode_ci NOT NULL,
	`email` tinyint(4) DEFAULT NULL,
	`sms` tinyint(4) DEFAULT NULL,
	PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  } // install
  
  
} // wps_am_actions
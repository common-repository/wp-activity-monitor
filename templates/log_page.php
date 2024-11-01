<div class="wrap wps-am-admin-page">
  <h2><i class="dashicons dashicons-chart-line"></i>WP Activity Log</h2>
  <hr/>
  
  <table class="wp-list-table widefat fixed posts">
  
    <thead>
      <tr>
        <th>User</th>
        <th>Event</th>
        <th>Event Group</th>
        <th>Description</th>
        <th>Date</th>
        <th>E-Mailed</th>
        <th>SMS</th>
      </tr>
    </thead>
    
    <?php
    global $wpdb;
    
    $events = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . WPS_AM_LOG . " ORDER BY date DESC LIMIT 100");
    if ($events) {
      foreach ($events as $event) {
      	
      	if (!empty($event->user_ID) && $event->user_ID != '0') {
      	  $user = get_user_by('id', $event->user_ID);
		  $user = $user->user_login;
      	} else {
		  $user = 'Not logged in.';
      	}
      	
      	$critical = '';
      	if ($event->event_action == 'wp_login_failed'
      	    || $event->event_action == 'set_logged_in_cookie') {
		  $critical = 'danger';
      	}
      	
    ?>
    
    <tbody>
      <tr class="<?php echo $critical; ?>">
        <td><?php echo $user; ?></td>
        <td><?php echo $event->event_action; ?></td>
        <td><?php echo $event->event_type; ?></td>
        <td><?php echo $event->description; ?></td>
        <td><?php echo date('d.m.Y', strtotime($event->date)) . ' @ ' . date('H:i', strtotime($event->date)); ?></td>
        <td>Yes</td>
        <td>No</td>
      </tr>
    </tbody>
    
    <?php
	  } 
	} else {
	?>
	
	<tbody>
	  <tr>
	    <td colspan="7" style="text-align: center;">Currently there are no events logged.</td>
	  </tr>
	</tbody>
	
	<?php
	}
	?>
  
  </table>
  
</div>
<div id="settings" class="tab-container">

  <form method="POST" action="#" class="wps-am-settings-form">
  <table class="form-table">

	<tbody>

	  <tr>
		<td colspan="2" style="text-align: left;">
		  <h3>Send E-Mail Notification:</h3>
		</td>
	  </tr>

	  <tr>
		<th>Notification by E-Mail:</th>
		<td><input type="checkbox" name="wps-am[notification-email-status]" class="widefat wps-am-checkbox" value="1" <?php wps_am_field_value('checkbox', 'notification-email-status'); ?>  /></td>
	  </tr>

	  <tr>
		<th>Send E-Mail to:</th>
		<td><input type="text" name="wps-am[notification-email]" class="widefat" value="<?php wps_am_field_value('text', 'notification-email'); ?>" /></td>
	  </tr>

	  <tr>
		<th>Send E-Mail to events:</th>
		<td>
		  <select class="js-multiple form-control" id="send-email-to-events" name="wps-am[email-events][]" multiple="multiple">
			<?php wps_am_print_select(wps_am_settings::set_email_events(), 'email-events'); ?>
		  </select>
		</td>
	  </tr>

	  <tr>
		<td colspan="2" style="text-align: left;">
		  <h3>Send SMS Notification:</h3>
		  <p class="info">SMS Notifications are <b>ONLY</b> sent on critical events such as failed login attempt.</p>
		</td>
	  </tr>

	  <?php
	  	if (!class_exists('wps_am_core_pro')) {
	  ?>
	  <tr>
		<td colspan="2">
		  <h4 class="error">This option is only available in <a href="http://www.wpactivitymonitor.com/#premium" target="_blank">WP Activity Monitor Premium</a></h4>
		</td>
	  </tr>
	  <?php } else {
	  	wps_am_core_pro::sms_settings();
	  } ?>

	  <tr>
		<td colspan="2" style="text-align: right;">
		  <input type="submit" name="text" value="Save" class="button button-primary" />
		</td>
	  </tr>

	</tbody>

  </table>
  </form>

</div>
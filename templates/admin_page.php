<div class="wrap wps-am-admin-page">
  <h2><i class="dashicons dashicons-chart-line"></i> WP Activity Monitor</h2>
  <hr/>
  
  <div class="wps-tabs">
  
    <ul>
      <li><a href="#welcome">Welcome</a></li>
      <?php do_action('wps_am_tabs'); ?>
    </ul>
    
    <?php require_once 'pages/welcome.php'; ?>
    
    <?php do_action('wps_am_tab_content'); ?>
  
  </div>
  
</div>
jQuery(document).ready(function($){

  // Switch
  $('.wps-am-checkbox').switchbutton();

  // Select2
  $('.js-multiple').select2();

  // Tabs
  if ($('.wps-tabs').length) {

	$(".wps-tabs").tabs();

	$( ".wps-tabs" ).on( "tabsactivate", function( event, ui ) {
	  var id = $('a', ui.newTab).attr('href');
	  window.location.hash = id;
	});

  }

  // Function for overlay
  function setup_overlay(message, show) {
	if (show=='true') {
	  $('body').prepend('<div class="wps-window-overlay"><div class="wps-wo-inner">' + message + '</div></div>');
	} else {
	  $('body>div.wps-window-overlay').remove();
	}
  } // setup_overlay


  // Settings Form
  $('.wps-am-settings-form').on('submit', function(e){
	e.preventDefault();

	// Fetch form
	var form = $(this);

	// Setup Overlay
	setup_overlay('Saving settings... Please wait!', 'true');

	// Get checkboxes
	$('input[type="checkbox"]', form).each(function(i, item) {
	  var name = $(item).attr('name');

	  if ($(item).attr('checked') == '' || typeof $(item).attr('checked') == 'undefined') {

		if ($('input[name="' + name + '"][type="hidden"]',form).length == 0) {
		  $(form).append('<input type="hidden" name="' + name + '" value=""/>');
		}

	  } else {
		// Remove hidden
		$('input[name="' + name + '"][type="hidden"]',form).remove();
	  }
	});

	// Serialize form
	var form_serialized = $(form).serialize();

	$.post(ajaxurl, {action: 'wps_am_save_settings', form:form_serialized}, function(response){

	  // Remove Overlay
	  setup_overlay('', 'false');

	});

	return false;
  });


});
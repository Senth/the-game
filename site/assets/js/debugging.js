function init_debug_window() {
	var DIV_OFFSET_X = 100;
	var DIV_OFFSET_Y = 25;

	var $debug_html = '<div id="debug_message_wrapper"></div>';

	$('#wrap').append($debug_html);

	$(document).bind('mousemove', function(event) {
		var messageOffset =  {
			left: event.pageX + DIV_OFFSET_X,
			top: event.pageY + DIV_OFFSET_Y
		};

		$('#debug_message_wrapper').offset(messageOffset);
	});
}

/**
 * Displays a debug message in a window beside the mouse
 * @param message the debug message to display
 */
function debugMessage(message) {
	if (document.getElementById('debug_message_wrapper') == null) {
		init_debug_window();
	}

	html = '<p class="debug_message">' + message + '</p>';
	$html = $(html);

	$('#debug_message_wrapper').append($html);
	$html.delay(10000).slideUp(1000);
	setTimeout(function() {
		$html.remove();
	}, 12000);
}

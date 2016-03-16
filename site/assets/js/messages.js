var SLIDE_TIME = 500;
var FADE_TIME = 4000;
var MESSAGE_TIME = 10000;
var FADE_OPACITY = 0.05;

/**
 * Creates a new message and displays it.
 * @param message the message to display
 * @param type the type of message, should be error/success. If type isn't defined
 * 	the message should already be formatted into <p>.
 * @param time seconds to display the message. If time isn't defined the message
 * will use the standard time
 */
function addMessage(message, type, $time) {
	addMessageTo(message, $('#messages'), type, $time);
}

/**
 * Creates a new message and displays it in the specified element
 * @param message the message to display
 * @param element the element to display the message in, the element should be a jQuery element
 * @param type the type of message, should be error/success. If type isn't defined
 * 	the message should already be formatted into <p>.
 * @param time seconds to display the message. If time isn't defined the message
 * will use the standard time
 */
function addMessageTo(message, element, type, time) {
	if (type === undefined) {
		var $jqMessage = $(message);
		element.append($jqMessage);
		fadeMessage($jqMessage, time);
	} else {
		var $jqMessage = $('<p class="' + type + '">' + message + '</p>');
		element.append($jqMessage);
		fadeMessage($jqMessage, time);
	}
}

/**
 * Updates the page and then fades the message after the timer runs out.
 * @param $jqMessage jQuery element with the message
 * @param time seconds to display the message. If time isn't defined the message
 * will use the standard time
 */
function fadeMessage($jqMessage, time) {
	if (time === undefined) {
		time = MESSAGE_TIME;
	}

	triggerEvent('pageChanged');
	$jqMessage
		.delay(time)
		.fadeTo(FADE_TIME, FADE_OPACITY)
		.slideUp(SLIDE_TIME)
		.queue(function() {
			triggerEvent('pageChanged');
		});
}

/**
 * Displays all the ajax return messages
 * @param json object containing all the return values from ajax
 * @param element the element where to display the success and error messages.
 * 	If it is left undefined it will default to the default message place
 */
function displayAjaxReturnMessages(json, element) {
	// Display success messages if it succeeded
	if (json.success_message !== undefined) {
		if (element === undefined) {
			addMessage(json.success_message);
		} else {
			addMessageTo(json.success_message, element);
		}
	}
	// Display error messages if it failed
	if (json.error_messages !== undefined) {
		if (element === undefined) {
			addMessage(json.error_messages);
		} else {
			addMessageTo(json.error_messages, element);
		}
	}	
}

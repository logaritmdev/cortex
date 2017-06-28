(function($) {
"use strict"

//------------------------------------------------------------------------------
// Variables
//------------------------------------------------------------------------------

/**
 * The attached element ids.
 * @var selectors
 * @since 1.1.0
 */
var ids = 1

/**
 * The selector bounds to callbacks.
 * @var selectors
 * @since 0.1.0
 */
var selectors = []

//------------------------------------------------------------------------------
// Functions
//------------------------------------------------------------------------------

/**
 * Attach a callback to a selector.
 * @function attach
 * @since 0.1.0
 */
$.attach = function(selector, callback) {

	var element = {
		selector: selector,
		callback: callback
	}

	selectors.push(element)
}

/**
 * Executes all callbacks from a specific element.
 * @function attach.refresh
 * @since 0.1.0
 */
$.attach.refresh = function(root) {

	var element = $(root || document.body)

	var process = function(elements) {

		elements.each(function(i, element) {

			element = $(element)

			$.each(selectors, function(i, builder) {
				var selector = builder.selector
				var callback = builder.callback
				if (selector && callback) {
					if (element.is(selector)) {
						element.attr('data-attach-id', ids++)
						callback(i, element)
					}
				}
			})

			process(element.children())
		})
	}

	process(element)
}

/**
 * Triggers the detach listener on all attached element.
 * @function detach
 * @since 0.1.0
 */
$.detach = function(root) {

	var element = $(root || document.body)

	var process = function(elements) {

		elements.each(function(i, element) {

			element = $(element)

			if (element.is('[data-attach-id]')) {
				element.trigger('detach')
			}

			process(element.children())
		})
	}

	process(element)
}

$(document).ready(function() {
	$.attach.refresh()
})

})(jQuery);

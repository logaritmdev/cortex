(function($) {
"use strict"

$.attach('.cortex-modal', function(i, element) {

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * @callback onPresent
	 * @since 0.1.0
	 */
	element.on('present', function() {
		element.toggleClass('cortex-modal-visible', true)
	})

	/**
	 * @callback onDismiss
	 * @since 0.1.0
	 */
	element.on('dismiss', function() {
		element.toggleClass('cortex-modal-visible', false)
	})

	//--------------------------------------------------------------------------
	// Events
	//--------------------------------------------------------------------------

	element.on('click', '.cortex-modal-close', function() {
		element.trigger('dismiss')
	})
})

})(jQuery);
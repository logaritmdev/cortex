(function($) {
"use strict"

$.attach('.cortex-post-selector', function(i, element) {

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * @callback onPresent
	 * @since 0.1.0
	 */
	element.on('present', function(e) {

		element.addClass('cortex-modal-loading')
		element.addClass('cortex-modal-visible')

		$.post(ajaxurl, {
			'action': 'get_documents'
		}, function(result) {

			element.removeClass('cortex-modal-loading').find('.cortex-post-selector-content').html(result)

		})
	})

	/**
	 * @callback onDismiss
	 * @since 0.1.0
	 */
	element.on('dismiss',function(e) {
		element.find('.cortex-post-selector-content').html('')
	})

	//--------------------------------------------------------------------------
	// Events
	//--------------------------------------------------------------------------

	/**
	 * @function onSelectButtonClick
	 * @since 0.1.0
	 */
	var onSelectButtonClick = function(e) {
		element.trigger('dismiss', $(e.target).closest('a').attr('data-document'))
	}

	//--------------------------------------------------------------------------
	// Initialization
	//--------------------------------------------------------------------------

	element.on('click', '.cortex-post-selector-post-list-item-check a', onSelectButtonClick)

})

})(jQuery);
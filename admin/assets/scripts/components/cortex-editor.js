(function($) {
"use strict"

$.attach('.cortex-editor', function(i, element) {

	var content = element.find('iframe')

	//--------------------------------------------------------------------------
	// Functions
	//--------------------------------------------------------------------------

	/**
	 * @function present
	 * @since 0.1.0
	 * @hidden
	 */
	var present = function(src) {
		element.trigger('present', src)
	}

	/**
	 * @function dismiss
	 * @since 0.1.0
	 * @hidden
	 */
	var dismiss = function() {
		element.trigger('dismiss')
	}

	/**
	 * @function refresh
	 * @since 0.1.0
	 * @hidden
	 */
	var refresh = function(block) {
		$('.cortex-block-list').trigger('reloadblock', block)
	}

	//--------------------------------------------------------------------------
	// Events
	//--------------------------------------------------------------------------

	/**
	 * @function onPresent
	 * @since 0.1.0
	 * @hidden
	 */
	var onPresent = function(e, src) {
		content.on('load', onContentLoad).attr('src', src)
		element.addClass('cortex-modal-loading')
	}

	/**
	 * @function onDismiss
	 * @since 0.1.0
	 * @hidden
	 */
	var onDismiss = function() {
		content.off('load', onContentLoad).attr('src', '')
		element.addClass('cortex-modal-loading')
	}

	/**
	 * @function onContentLoad
	 * @since 0.1.0
	 * @hidden
	 */
	var onContentLoad = function() {

		var contents = content.contents()

		var message = contents.find('#message.notice.notice-success.updated')
		if (message.length) {
			dismiss()
			refresh(contents.find('#post_ID').val())
			return
		}

		element.toggleClass('cortex-modal-loading', false)

		contents.find('#publish').on('click', function() {
			element.toggleClass('cortex-modal-loading', true)
		})
	}

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * Opens the block editor.
	 * @function onEdit
	 * @since 0.1.0
	 */
	element.on('edit', function(e, id, document) {
		present(CORTEX.admin_url + 'post.php?&post=' + id + '&action=edit')
	})

	element.on('present', onPresent)
	element.on('dismiss', onDismiss)

})

})(jQuery);
(function($) {
"use strict"

$.attach('.cortex-block-list-item:not(.cortex-block-list-item-template)', function(i, element) {

	/**
	 * @function cancel
	 * @since 0.1.0
	 */
	var cancel = function(e) {
		e.preventDefault()
		e.stopPropagation()
	}

	//--------------------------------------------------------------------------
	// Events
	//--------------------------------------------------------------------------

	/**
	 * Shows the preview.,
	 * @function onShowButtonClick
	 * @since 0.1.0
	 */
	var onShowButtonClick = function(e) {
		element.removeClass('cortex-block-list-item-collapsed')
	}

	/**
	 * Hides the preview.
	 * @function onShowButtonClick
	 * @since 0.1.0
	 */
	var onHideButtonClick = function(e) {
		element.addClass('cortex-block-list-item-collapsed')
	}

	/**
	 * Triggers the moveblock event from the block list.
	 * @function onMoveButtonClick
	 * @since 0.1.0
	 */
	var onMoveButtonClick = function(e) {

		var onPresent = function() {}
		var onDismiss = function(e, destination) {

			if (destination) {
				$('.cortex-block-list').trigger('moveblock', [element.attr('data-id'), destination])
			}

			selector.off('present', onPresent)
			selector.off('dismiss', onDismiss)
		}

		var selector = $('.cortex-post-selector-move')
		selector.on('present', onPresent)
		selector.on('dismiss', onDismiss)
		selector.trigger('present')
	}

	/**
	 * Triggers the copyblock event from the block list.
	 * @function onCopyButtonClick
	 * @since 0.1.0
	 */
	var onCopyButtonClick = function(e) {

		var onPresent = function() {}
		var onDismiss = function(e, destination) {

			if (destination) {
				$('.cortex-block-list').trigger('copyblock', [element.attr('data-id'), destination])
			}

			selector.off('present', onPresent)
			selector.off('dismiss', onDismiss)
		}

		var selector = $('.cortex-post-selector-copy')
		selector.on('present', onPresent)
		selector.on('dismiss', onDismiss)
		selector.trigger('present')
	}

	/**
	 * Triggers the removeblock event from the block list.
	 * @function onRemoveButtonClick
	 * @since 0.1.0
	 */
	var onRemoveButtonClick = function(e) {
		$('.cortex-block-list').trigger('removeblock', element.attr('data-id'))
	}

	/**
	 * Triggers the edit event from the editor.
	 * @function onUpdateButtonClick
	 * @since 0.1.0
	 */
	var onUpdateButtonClick = function(e) {
		$('.cortex-editor').trigger('edit', element.attr('data-id'))
	}

	/**
	 * Triggers the present event from the library.
	 * @function onUpdateButtonClick
	 * @since 0.1.0
	 */
	var onInsertIntoButtonClick = function(e) {

		cancel(e)

		var parent = $(e.target).closest('.cortex-block-list-item-region')

		$('.cortex-library').trigger('present', [
			parent.attr('data-layout'),
			parent.attr('data-region')
		])
	}

	//--------------------------------------------------------------------------
	// Initialization
	//--------------------------------------------------------------------------

	var menu = element.find('> .cortex-block-list-item-menu')

	menu.on('click', 'a', cancel)
	menu.on('click', '.cortex-block-list-item-menu-button-show a', onShowButtonClick)
	menu.on('click', '.cortex-block-list-item-menu-button-hide a', onHideButtonClick)
	menu.on('click', '.cortex-block-list-item-menu-button-copy a', onCopyButtonClick)
	menu.on('click', '.cortex-block-list-item-menu-button-move a', onMoveButtonClick)
	menu.on('click', '.cortex-block-list-item-menu-button-remove a', onRemoveButtonClick)
	menu.on('click', '.cortex-block-list-item-menu-button-update a', onUpdateButtonClick)

	element.find('.cortex-block-list-item-region-button').each(function(i, wrapper) {

		var button = $(wrapper)
		if (button.closest('.cortex-block-list-item').is(element)) {
			button.on('click', 'a', onInsertIntoButtonClick)
		}

	})

})

})(jQuery);
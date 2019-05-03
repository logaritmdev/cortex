(function ($) {

	$.attach('.cortex-admin-blocks-page, .edit-php.post-type-acf-field-group', function (i, element) {

		var createBlockModal = $('.cortex-create-block-modal')
		var updateBlockModal = $('.cortex-update-block-modal')

		var onCreateBlockLinkClick = function (e) {
			e.preventDefault()
			createBlockModal.trigger('present', $(e.target).attr('href'))
		}

		var onUpdateBlockLinkClick = function (e) {
			e.preventDefault()
			updateBlockModal.trigger('present', $(e.target).attr('href'))
		}

		element.on('click', '.cortex-create-block-link', onCreateBlockLinkClick)
		element.on('click', '.cortex-update-block-link', onUpdateBlockLinkClick)

		/*
		 * Opens blocks in ACF field group list in a popup
		 */

		element.find('.wp-list-table a').each(function (i, element) {

			element = $(element)

			var regex = /post\.php\?post=(\d+)&action=edit/mig
			var value = element.attr('href')

			var link = regex.exec(value);
			if (link == null) {
				return
			}

			var post = link[1]

			$.each(CORTEX.blocks, function (i, block) {
				if (block.ID == post) {
					element.on('click', onUpdateBlockLinkClick)
				}
			})
		})
	})

	$.attach('.cortex-create-block-modal, .cortex-update-block-modal', function (i, element) {

		var content = element.find('iframe')

		//----------------------------------------------------------------------
		// Functions
		//----------------------------------------------------------------------

		/**
		 * @function present
		 * @since 0.1.0
		 * @hidden
		 */
		var present = function (src) {
			element.trigger('present', src)
		}

		/**
		 * @function dismiss
		 * @since 0.1.0
		 * @hidden
		 */
		var dismiss = function () {
			element.trigger('dismiss')
		}

		//--------------------------------------------------------------------------
		// Events
		//--------------------------------------------------------------------------

		/**
		 * @function onPresent
		 * @since 0.1.0
		 * @hidden
		 */
		var onPresent = function (e, src) {
			content.on('load', onContentLoad).attr('src', src)
			element.addClass('cortex-modal-loading')
		}

		/**
		 * @function onDismiss
		 * @since 0.1.0
		 * @hidden
		 */
		var onDismiss = function () {
			content.off('load', onContentLoad).attr('src', '')
			element.addClass('cortex-modal-loading')
		}

		/**
		 * @function onContentLoad
		 * @since 0.1.0
		 * @hidden
		 */
		var onContentLoad = function () {

			var contents = content.contents()

			var message = contents.find('#message.notice.notice-success.updated')
			if (message.length) {
				location.reload()
				dismiss()
				return
			}

			element.toggleClass('cortex-modal-loading', false)
		}

		element.on('present', onPresent)
		element.on('dismiss', onDismiss)
	})

})(jQuery);
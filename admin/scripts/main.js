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
			// Otherwise when validation fails the loading state remains
			// element.toggleClass('cortex-modal-loading', true)
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

		var lang = element.attr('data-lang')
		if (lang) {
			lang = '&lang=' + lang
		}

		present(CORTEX.admin_url + 'post.php?&post=' + id + '&action=edit' + lang)
	})

	element.on('present', onPresent)
	element.on('dismiss', onDismiss)

})

})(jQuery);
(function($) {
"use strict"

$.attach('.cortex-library', function(i, element) {

	var group = 'all'
	var query = ''

	var layout = null
	var region = null

	var cells = element.find('.cortex-library-grid-cell')
	var filter = element.find('.cortex-library-filter')
	var inputs = element.find('.cortex-library-query-input')

	//--------------------------------------------------------------------------
	// Functions
	//--------------------------------------------------------------------------

	/**
	 * @function present
	 * @since 0.1.0
	 */
	var present = function() {
		element.closest('.cortex-modal').trigger('present')
	}

	/**
	 * @function dismiss
	 * @since 0.1.0
	 */
	var dismiss = function() {
		element.closest('.cortex-modal').trigger('dismiss')
		layout = null
		region = null
	}

	/**
	 * @function update
	 * @since 0.1.0
	 */
	var update = function() {

		var regex = RegExp(query, 'mig')

		cells.each(function(i, element) {

			element = $(element)

			var visible = true

			if (group !== 'all') {
				if (visible) {
					visible = element.attr('data-group').toLowerCase() === group
				}
			}

			if (visible && query) {
				visible = (
					element.find('.cortex-library-grid-item-name').text().match(regex) !== null ||
					element.find('.cortex-library-grid-item-hint').text().match(regex) !== null
				)
			}

			element.toggleClass('cortex-library-grid-cell-hidden', visible === false)
		})

		filter.val(group)
	}

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * @callback setgroup
	 * @since 0.1.0
	 */
	element.on('setgroup', function(e, value) {
		group = value.toLowerCase()
		update()
	})

	/**
	 * @callback setquery
	 * @since 0.1.0
	 */
	element.on('setquery', function(e, value) {
		query = value.toLowerCase()
		update()
	})

	/**
	 * @callback present
	 * @since 0.1.0
	 */
	element.on('present', function(e, l, r) {
		layout = l
		region = r
	})

	//--------------------------------------------------------------------------
	// Events
	//--------------------------------------------------------------------------

	/**
	 * @function onFilterChange
	 * @since 0.1.0
	 */
	var onFilterChange = function(e) {
		element.trigger('setgroup', $(e.target).val())
	}

	/**
	 * @function onInputInput
	 * @since 0.1.0
	 */
	var onInputInput = function(e) {
		element.trigger('setquery', $(e.target).val())
	}

	/**
	 * @function onInsertButtonClick
	 * @since 0.1.0
	 */
	var onInsertButtonClick = function(e) {
		var cell = $(e.target).closest('.cortex-library-grid-cell')
		$('.cortex-block-list').trigger('insertblock', [cell.attr('data-template'), layout, region, cell.attr('data-name')])
		dismiss()
	}

	//--------------------------------------------------------------------------
	// Initialization
	//--------------------------------------------------------------------------

	filter.on('change', onFilterChange)
	inputs.on('input', onInputInput)

	element.on('click', '.cortex-library-grid-item-insert-button', onInsertButtonClick)

	update()
})

})(jQuery);
(function($) {
"use strict"

$.attach('.cortex-block-list', function(i, element) {

	var parentLayout = null
	var parentRegion = null

	//--------------------------------------------------------------------------
	// Functions
	//--------------------------------------------------------------------------

	/**
	 * @function getOrder
	 * @since 0.1.0
	 * @hidden
	 */
	var getOrder = function() {
		return element.find('.cortex-block-list-item').map(function(i, element) { return $(element).attr('data-id') }).toArray()
	}

	/**
	 * @function toggleEmptyListMessage
	 * @since 0.1.0
	 * @hidden
	 */
	var toggleEmptyListMessage = function() {
		element.toggleClass('cortex-block-list-empty', element.find('.cortex-block-list-item:not(.cortex-block-list-item-template)').length === 0)
	}

	/**
	 * @function toggleEmptyRegionMessage
	 * @since 0.1.0
	 * @hidden
	 */
	var toggleEmptyRegionMessage = function() {
		element.find('.cortex-block-list-item-region').each(function(i, element) {
			$(element).toggleClass('cortex-block-list-item-region-empty', $(element).find('.cortex-block-list-item').length == 0)
		})
	}

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * Reloads the specific blocks from the server.
	 * @callback reloadblock
	 * @since 0.1.0
	 */
	element.on('reloadblock', function(e, id) {

		var document = element.attr('data-document')

		var current = element.find('.cortex-block-list-item[data-id="' + id + '"]')
		var content = element.find('.cortex-block-list-item[data-id="' + id + '"] .cortex-block-list-item-preview')
		if (current.length === 0) {
			return
		}

		var event = $.Event('reloadblock')

		current.trigger(event)

		if (event.isDefaultPrevented()) {
			return
		}

		current.toggleClass('cortex-block-list-item-loading', true)

		$.post(ajaxurl, {

			'action': 'preview_block',
			'document': document,
			'id': id

		}, function(result) {

			content.html(result)
			current.toggleClass('cortex-block-list-item-loading', false)

			var preview = content.find('.cortex-block-preview')
			if (preview.length) {
				$.attach.refresh(preview)
			}

		})
	})

	/**
	 * Inserts a block server side then update the display.
	 * @callback insertblock
	 * @since 0.1.0
	 */
	element.on('insertblock', function(e, template, layout, region, name) {

		var document = element.attr('data-document')

		var item = element.find('.cortex-block-list-item-template').clone(true)
			.toggleClass('cortex-block-list-item-template', false)
			.toggleClass('cortex-block-list-item-loading', true)

		item.find('.cortex-block-list-item-title span').html(name)

		if (layout && region) {
			item.appendTo(element.find('.cortex-block-list-item-region[data-layout="' + layout + '"][data-region="' + region + '"] .cortex-block-list-item-region-content'))
		} else {
			item.appendTo(element.find('.cortex-block-list-items'))
		}

		toggleEmptyListMessage()

		$.post(ajaxurl, {

			'action': 'insert_block',
			'template': template,
			'document': document,
			'parent_layout': layout,
			'parent_region': region,

		}, function(result) {

			var html = $(result)
			var id = html.attr('data-id')
			var group = html.attr('data-group')

			item.removeClass('cortex-block-list-item-loading').find('.cortex-block-list-item-preview').replaceWith(html)

			$.attach.refresh(item)

			toggleEmptyRegionMessage()

			if (group === 'layout') {
				item.find('.cortex-block-list-item-menu-button-copy').remove()
				item.find('.cortex-block-list-item-menu-button-move').remove()
			}

			item.attr('data-id', id)

			element.sortable('refresh')

		})
	})

	/**
	 * Removes a block server side then updates the display.
	 * @callback removeblock
	 * @since 0.1.0
	 */
	element.on('removeblock', function(e, id) {

		var value = confirm(CORTEX.messages.remove_block)
		if (value == false) {
			return
		}

		var document = element.attr('data-document')

		$.post(ajaxurl, {
			'action': 'remove_block',
			'document': document,
			'id': id
		})

		element.find('.cortex-block-list-item[data-id="' + id + '"]').remove()

		toggleEmptyListMessage()
		toggleEmptyRegionMessage()
	})

	/**
	 * Moves a block server side then updates the display.
	 * @callback moveblock
	 * @since 0.1.0
	 */
	element.on('moveblock', function(e, id, dst) {

		var src = element.attr('data-document')

		$.post(ajaxurl, {
			'action': 'move_block',
			'src_document': src,
			'dst_document': dst,
			'id': id
		})

		element.find('.cortex-block-list-item[data-id="' + id + '"]').remove()

		toggleEmptyListMessage()
		toggleEmptyRegionMessage()
	})

	/**
	 * Copies a block server side then updates the display.
	 * @callback copyblock
	 * @since 0.1.0
	 */
	element.on('copyblock', function(e, id, dst) {

		var src = element.attr('data-document')

		$.post(ajaxurl, {
			'action': 'copy_block',
			'src_document': src,
			'dst_document': dst,
			'id': id
		})

	})

	/**
	 * Saves the block order server side.
	 * @callback orderblocks
	 * @since 0.1.0
	 */
	element.on('orderblocks', function(e, order) {

		var document = element.attr('data-document')

		$.post(ajaxurl, {
			'action': 'order_blocks',
			'document': document,
			'order': order
		})

	})

	/**
	 * Sets the block that contains the specified block server side.
	 * @callback set_parent_block
	 * @since 0.1.0
	 */
	element.on('setparentblock', function(e, id, layout, region) {

		var document = element.attr('data-document')

		$.post(ajaxurl, {
			'action': 'set_parent_block',
			'document': document,
			'parent_layout': layout,
			'parent_region': region,
			'id': id
		}, function() {

			element.trigger('orderblocks', JSON.stringify(getOrder()))

		})

	})

	/**
	 * Presents the library.
	 * @function onInsertButtonClick
	 * @since 0.1.0
	 */
	var onInsertButtonClick = function() {
		$('.cortex-library').trigger('setgroup', 'all').trigger('present')
	}

	/**
	 * Presents the library.
	 * @function onLayoutButtonClick
	 * @since 0.1.0
	 */
	var onLayoutButtonClick = function() {
		$('.cortex-library').trigger('setgroup', 'layout').trigger('present')
	}

	//--------------------------------------------------------------------------
	// Initialization
	//--------------------------------------------------------------------------

	toggleEmptyListMessage()
	toggleEmptyRegionMessage()

	element.find('.cortex-block-list-empty-message .button').on('click', onInsertButtonClick)
	element.find('.cortex-block-list-menu-button-insert').on('click', onInsertButtonClick)
	element.find('.cortex-block-list-menu-button-layout').on('click', onLayoutButtonClick)

	element.on('mousedown', '.cortex-block-list-item', function() {
		var parent = $(this).closest('.cortex-block-list')
		var region = $(this).closest('.cortex-block-list-item-region-content')
		parent.css('height', parent.height())
		region.css('height', region.height())
	})

	element.on('mouseup', '.cortex-block-list-item', function() {
		var parent = $(this).closest('.cortex-block-list')
		var region = $(this).closest('.cortex-block-list-item-region-content')
		parent.css('height', '')
		region.css('height', '')
	})

	var animationFrameId = null

	$(window).on('resize', function() {
		animationFrameId = cancelAnimationFrame(animationFrameId)
		animationFrameId = requestAnimationFrame(function() {
			element.find('.cortex-block-list-item-region-content').each(function(i, region) {
				$(region).css('height', '')
			})
		})
	})

	var options = {

		items: '.cortex-sortable-item',
		handle: '.cortex-sortable-handle',
		placeholder: 'cortex-block-list-item-placeholder',
		toleranceElement: '> div',

		start: function(e, ui) {
			ui.placeholder.height(ui.item.outerHeight())
		},

		stop: function(event, ui) {

			var region = ui.item.closest('.cortex-block-list-item-region');

			element.trigger('setparentblock', [
				ui.item.attr('data-id'),
				region.attr('data-layout') || '',
				region.attr('data-region') || ''
			])

			toggleEmptyRegionMessage()

		}
	}

	element.sortable(options)

})

})(jQuery);
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

		$('.cortex-library').trigger('setgroup', 'all')
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
(function($) {
"use strict"

$.attach('.cortex-post-selector', function(i, element) {

	var searchBar = element.find('.cortex-post-selector-search')
	var search = element.find('.cortex-post-selector-search-input')
	var searchRequest = null
	var searchTimeout = null

	//--------------------------------------------------------------------------
	// Callbacks
	//--------------------------------------------------------------------------

	/**
	 * @callback onPresent
	 * @since 0.1.0
	 */
	element.on('present', function(e) {

		search.val('')

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

	/**
	var onSearch = function() {
	 * @function onSelectButtonClick
	 * @since 0.1.0
	 */
	var onSearch = function() {

		var query = function() {

			if (searchRequest) {
				searchRequest.abort()
			}

			element.addClass('cortex-post-selector-searching')

			searchRequest = $.post(ajaxurl, {
				'action': 'get_documents',
				'search': search.val()
			}, function(result) {

				element.removeClass('cortex-post-selector-searching').find('.cortex-post-selector-content').html(result)

			})
		}

		searchTimeout = clearTimeout(searchTimeout)
		searchTimeout = setTimeout(query, 500)
	}

	//--------------------------------------------------------------------------
	// Initialization
	//--------------------------------------------------------------------------

	search.on('input', onSearch)

	element.on('click', '.cortex-post-selector-post-list-item-check a', onSelectButtonClick)
})

})(jQuery);
(function($) {
"use strict"

$.attach('body.cortex-create-block-page', function(i, element) {
	$('.wp-heading-inline').text(CORTEX.messages.create_block_template)
	element.addClass('cortex-create-block-page-ready')
})

$.attach('body.cortex-update-block-page', function(i, element) {
	$('.wp-heading-inline').text(CORTEX.messages.update_block_template)
	element.addClass('cortex-update-block-page-ready')
})

$(document).ready(function() {

	var fields = [
		{
			mode: 'ace/mode/twig',
			editor: '#cortex-block-editor',
			textarea: '#cortex-block',
		},
		{
			mode: 'ace/mode/scss',
			editor: '#cortex-style-editor',
			textarea: '#cortex-style',
		},
		{
			mode: 'ace/mode/javascript',
			editor: '#cortex-script-editor',
			textarea: '#cortex-script',
		},
		{
			mode: 'ace/mode/twig',
			editor: '#cortex-preview-editor',
			textarea: '#cortex-preview',
		}
	]

	fields.forEach(function(field) {

		var element = $(field.editor)
		var textarea = $(field.textarea)

		if (element.length == 0) {
			return
		}

		/**
		 * Updates the textarea using the editor value.
		 * @function update
		 * @since 0.1.0
		 */
		var update = function() {
			textarea.val(editor.getSession().getValue())
		}

		/**
		 * Reloads the content.
		 * @function reload
		 * @since 0.1.0
		 */
		var reload = function() {

			reloading = true

			button.addClass('cortex-reload-reloading')

			$.post(ajaxurl, {

				'action': 'get_block_template_file_content',
				'guid': guid,
				'file': file

			}, function(code) {

				$.post(ajaxurl, {

					'action': 'get_block_template_file_date',
					'guid': guid,
					'file': file

				}, function(d) {

					date = d

					element.removeClass('invalid')
					editor.getSession().setValue(code)
					button.removeClass('cortex-reload-reloading')
					button.detach()

					reloading = false
				})
			})
		}

		/**
		 * Check whether the local block template file is out of sync.
		 * @function check
		 * @since 0.1.0
		 */
		var check = function() {

			$.post(ajaxurl, {

				'action': 'get_block_template_file_date',
				'guid': guid,
				'file': file

			}, function(d) {

				setTimeout(check, 10000)

				if (reloading) {
					return
				}

				var invalid = parseInt(d) > parseInt(date)

				if (invalid) {
					element.toggleClass('invalid', true)
					element.append(button)
				} else {
					element.toggleClass('invalid', false)
					button.detach()
				}
			})
		}

		var reloading = false

		var value = textarea.val()

		var editor = ace.edit(element.get(0))
		editor.setTheme('ace/theme/tomorrow_night')
		editor.getSession().setMode(field.mode)
		editor.getSession().setValue(value)
		editor.getSession().on('change', update)

		var file = element.attr('data-file')
		var guid = element.attr('data-guid')
		var date = element.attr('data-date')

		var button = $('<div class="cortex-reload"></div>')

		button.on('click', reload)

		setTimeout(check, 10000)

		update()

	})
})

})(jQuery);
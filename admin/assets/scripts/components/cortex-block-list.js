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

		current.toggleClass('cortex-block-list-item-loading', true)

		$.post(ajaxurl, {

			'action': 'preview_block',
			'document': document,
			'id': id

		}, function(result) {
			content.html(result)
			current.toggleClass('cortex-block-list-item-loading', false)
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

		item.find('.cortex-block-list-item-title').html(name)

		if (layout && region) {
			item.appendTo(element.find('.cortex-block-list-item-region[data-layout="' + layout + '"][data-region="' + region + '"] .cortex-block-list-item-region-content'))
		} else {
			item.appendTo(element)
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
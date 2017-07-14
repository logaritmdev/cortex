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

			if (query) {
				if (visible) visible = element.find('.cortex-library-grid-item-name').text().match(regex) !== null
				if (visible) visible = element.find('.cortex-library-grid-item-hint').text().match(regex) !== null
			}

			element.toggleClass('cortex-library-grid-cell-hidden', visible == false)
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
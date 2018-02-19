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
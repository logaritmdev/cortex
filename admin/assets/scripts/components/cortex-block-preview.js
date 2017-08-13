(function($) {
"use strict"

$.attach('.cortex-block-preview', function(i, element) {

	var loaded = false
	var iframe = $('<iframe frameborder="0" allowtransparency="true" scrolling="no" width="100%" height="100%"></iframe>').appendTo(element)

	var source = ajaxurl + '?action=render_single_block&document=' + element.attr('data-document') + '&id=' + element.attr('data-id')

	var load = function() {
		if (loaded === false) {
			loaded = true
			iframe.on('load', onFrameLoad)
			iframe.attr('src', source)
		}
	}

	var getContent = function() {
		return iframe.contents()
	}

	var getContentBody = function() {
		return getContent().find('body').get(0)
	}

	var resize = function() {
		var body = getContentBody()
		if (body) {
			element.css('height', body.scrollHeight)
		}
	}

	var visible = function() {
    	var etop = $(element).offset().top
    	var ebot = etop + $(element).outerHeight()
	    var vtop = $(window).scrollTop()
    	var vbot = vtop + $(window).height()
	    return ebot > vtop && etop < vbot
	}

	var onFrameLoad = function() {
		element.addClass('cortex-block-preview-loaded')
		resize()
	}

	var resizer = null

	var onResize = function() {
		resizer = cancelAnimationFrame(resizer)
		resizer = requestAnimationFrame(resize)
	}

	var onScroll = function() {
		if (loaded === false && visible()) load()
	}

	if (document.readyState === 'complete') {
		if (loaded === false && visible()) load()
	}

	$(window).on('load', function() {
		if (loaded === false && visible()) load()
	})

	$(window).on('resize', onResize)
	$(window).on('scroll', onScroll)

})

})(jQuery);
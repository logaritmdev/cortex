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

})(jQuery);
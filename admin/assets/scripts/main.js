(function ($) {
	"use strict"

	$.attach('body.cortex-create-block-page', function (i, element) {
		element.addClass('cortex-create-block-page-ready')
	})

	$.attach('body.cortex-update-block-page', function (i, element) {
		element.addClass('cortex-update-block-page-ready')
	})

	$(document).ready(function () {

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
			}
		]

		fields.forEach(function (field) {

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
			var update = function () {
				textarea.val(editor.getSession().getValue())
			}

			/**
			 * Reloads the content.
			 * @function reload
			 * @since 0.1.0
			 */
			var reload = function () {

				reloading = true

				button.addClass('cortex-reload-reloading')

				$.post(ajaxurl, {

					'action': 'get_block_file_data',
					'id': id,
					'file': file

				}, function (code) {

					$.post(ajaxurl, {

						'action': 'get_block_file_date',
						'id': id,
						'file': file

					}, function (d) {

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
			var check = function () {

				$.post(ajaxurl, {

					'action': 'get_block_file_date',
					'id': id,
					'file': file

				}, function (d) {

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

			var id = element.attr('data-id')
			var file = element.attr('data-file')
			var date = element.attr('data-date')

			var button = $('<div class="cortex-reload"></div>')

			button.on('click', reload)

			setTimeout(check, 10000)

			update()

		})
	})

})(jQuery);
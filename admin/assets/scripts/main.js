(function ($) {

	"use strict"

	var Cortex = window.Cortex = {

		generatePreview: function (id, post, hash, url, vars) {

			var element = $('[data-hash="' + hash + '"]')

			/**
			 * Loads the block preview url within an iframe. Once loaded it
			 * will be used to generate a canvas based screen shot.
			 */

			var iframe = $('<iframe></iframe>')
			iframe.css('width', '1440px')
			iframe.css('height', 'auto')
			iframe.css('pointer-events', 'none')
			iframe.css('position', 'absolute')
			iframe.css('opacity', '0')
			iframe.appendTo(document.body)

			iframe.attr('src', url + '&mode=preview').on('load', function () {

				var body = iframe.contents().find('body').get(0)
				if (body == null) {
					return
				}

				/**
				 * Uses html2canvas library to generate a screenshot. Display
				 * the canvas and send the image data to the server so it
				 * can store it as an image.
				 */

				html2canvas(body).then(function (canvas) {

					iframe.remove()

					$(canvas).addClass('cortex-preview-image')
					$(canvas).css('width', '100%')
					$(canvas).css('height', 'auto')

					element.empty()
					element.append(canvas)

					var w = $(canvas).attr('width')
					var h = $(canvas).attr('height')

					element.css('padding-bottom', (h / w) * 100 + '%')
					element.addClass('loaded')

					var data = canvas.toDataURL()

					$.ajax({
						url: ajaxurl,
						method: 'post',
						data: {
							action: 'save_preview',
							id: id,
							post: post,
							hash: hash,
							data: data,
							w: w,
							h: h
						}
					})
				})
			})
		}
	}

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
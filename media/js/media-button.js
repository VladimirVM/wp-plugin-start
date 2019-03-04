/**
 * Create: Vladimir
 *
 *
 *
 */
(function ($, wp) {
	$(function () {
		var mediaButton = function ($content, args) {
			args = $.extend({
				$image: $('.js-media-button-image', $content),
				$id: $('.js-media-button-id', $content),
				$content: $content,
				image: 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D',
			}, args);

			var app = {
				args: args,

				init: function () {
					app.event();
				},

				update: function (image) {
					if (!image) {
						image = {
							url: args.image,
							id: 0,
						}
					}

					args.$image.attr('src', image.url);
					args.$id.val(image.id);
				},

				event: function () {
					$content.on('click', '.js-media-button-change-image', function () {
						var _attachment = wp.media.editor.send.attachment;

						wp.media.editor.send.attachment = function (props, attache) {
							app.update(attache);
							wp.media.editor.send.attachment = _attachment;
						};

						wp.media.editor.open($(this));
						return false;
					});

					$content.on('click', '.js-media-button-remove-image', function () {
						app.update();
					});


				}


			};


			app.init();

			return app;
		};


		$('.js-media-button').each(function () {
			var $this = $(this);
			mediaButton($this, $this.data());
		});

	});
})(jQuery, wp);

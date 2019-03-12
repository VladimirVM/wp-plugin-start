/**
 * Project: md.innovecs.com
 * Create: Vladimir
 * Date: 2019-03-11
 */
'use strict';


(function ($, plugin) {
	var fieldAutoComplete = function ($content, args) {
		args = $.extend(true, {
			$content: $content,
			class: {
				listRemoveItem: '.js-list-remove-item',
				autoCompleteFieldText: '.js-auto-complete-field-text',
				autoCompleteFieldId: '.js-auto-complete-field-id',
				listAddItem: '.js-list-add-item',
				listItems: '.js-list-items',
				listItemTemplate: '.js-list-item-template',
			},
			settings: {
				select: function (e, ui) {
					if (ui.item) {
						var template = args.template;

						template = template.replace('{{value}}', ui.item.user_id).replace('{{title}}', ui.item.name + ' ' + ui.item.surname + ' (' + ui.item.email + ')');

						args.$listItems.append(template);

					}
				},
				source: null,
				minLength: 2
			},
			$fieldText: null,
			$fieldId: null,
			$addItem: null,
			$listItems: null,
			$listItemTemplate: null,

			template: ''
		}, args);

		args.$fieldText = $('.js-auto-complete-field-text', $content);
		args.$fieldId = $('.js-auto-complete-field-id', $content);
		args.$addItem = $('.js-list-add-item', $content);
		args.$listItems = $('.js-list-items', $content);
		args.$listItemTemplate = $('.js-list-item-template', $content);

		args.template = $('<textarea />').html(args.$listItemTemplate.text()).text();

		var app = {
			args: args,

			init: function () {
				app.event();
				app.autoComplete();
			},

			event: function () {
				$content.on('click', args.class.listRemoveItem, function () {
					var $this = $(this);
					$this.closest('.js-list-item').remove();
				});

				$content.on('click', args.class.listAddItem, function () {

					var template = args.template;

					var value = args.$fieldId.val();
					var title = args.$fieldText.val();

					template = template.replace('{{value}}', value).replace('{{title}}', title);

					args.$listItems.append(template);
				});
			},

			autoComplete: function () {
				
				args.$fieldText
					.autocomplete(args.settings)
					.autocomplete('instance')._renderItem = function (ul, item) {
					return $('<li>')
						.text(item.name + ' ' + item.surname + ' (' + item.email + ')')
						.appendTo(ul);
				};
				
			},

			select: function (event, ui) {

			}

		};


		app.init();

		return app;
	};

	$('.js-field-auto-complete').each(function () {
		var $this = $(this);
		fieldAutoComplete($this, $this.data());
	});

	plugin.fieldAutoComplete = fieldAutoComplete;

})(jQuery, window.wp_plugin_start = window.wp_plugin_start || {});

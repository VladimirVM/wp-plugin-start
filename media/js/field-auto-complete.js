/**
 * Project: md.innovecs.com
 * Create: Vladimir
 * Date: 2019-03-11
 */
'use strict';


(function ($, plugin) {
	var fieldAutoComplete = function ($content, args) {
		args = $.extend({
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
				source: null,
				minLength: 2
			},
			$fieldText: null,
			$fieldId: null,
			$addItem: null,
			$listItems: null,
			$listItemTemplate: null,

			template: ''
		}, args, true);

		args.$fieldText = $('.js-auto-complete-field-text', $content);
		args.$fieldId = $('.js-auto-complete-field-id', $content);
		args.$addItem = $('.js-list-add-item', $content);
		args.$listItems = $('.js-list-items', $content);
		args.$listItemTemplate = $('.js-list-item-template', $content);

		args.template = args.$listItemTemplate.text();

		var app = {
			args: args,

			init: function () {
				app.event();
				args.settings
			},

			event: function () {
				$content.on('click', args.class.listRemoveItem, function () {
					var $this = $(this);
					$this.closest('.js-list-item').remove();
				});

				$content.on('click', args.class.listAddItem, function () {
					var $this = $(this);

					var template = args.template;

					var value = args.$fieldId.val();
					var title = args.$fieldText.val();

					template = template.replace('{{value}}', value).replace('{{title}}', title);

					args.$listItems.append(template);
				});
			},

			autoComplete: function () {
				args.$fieldText.autocomplete(args.settings /*{
					minLength: 2,
					select: function (event, ui) {

					}
				}*/);
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

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
				listItems: '.js-list-items',
				listItem: '.js-list-item'
			},
			settings: {
				select: function (e, ui) {
					if (ui.item) {
						var template = args.template;
						
						template = template
							.replace('{{value}}', ui.item[args.idKey])
							.replace('{{title}}', app.template(args.templateSelectedItem, ui.item));

						args.$listItems.append(template);

					}
				},
				source: null,
				minLength: 2,
				messages: {
					noResults: window.uiAutocompleteL10n.noResults,
					results: function (number) {
						if (number > 1) {
							return window.uiAutocompleteL10n.manyResults.replace('%d', number);
						}

						return window.uiAutocompleteL10n.oneResult;
					}
				}
			},
			$fieldText: null,
			$listItems: null,

			idKey: 'id',

			templateAutoCompleteItem: '{{name}}',
			templateSelectedItem: '{{name}}',

			template: '<span class="js-list-remove-item">&Cross;</span><input type="hidden" name="item[]" value="{{value}}"> {{title}}'
		}, args);

		args.$fieldText = $(args.class.autoCompleteFieldText, $content);
		args.$listItems = $(args.class.listItems, $content);

		var app = {
			args: args,

			init: function () {
				app.event();
				app.autoComplete();
			},

			event: function () {
				$content.on('click', args.class.listRemoveItem, function () {
					var $this = $(this);
					$this.closest(args.class.listItem).remove();
				});

			},

			autoComplete: function () {

				args.$fieldText
					.autocomplete(args.settings)
					.autocomplete('instance')
					._renderItem = function (ul, item) {
					var content = app.template(args.templateAutoCompleteItem, item);

					return $('<li>', {html: content})
						.appendTo(ul);
				};

			},

			template: function (html, values) {
				$.each(values, function (k, v) {
					html = html.replace('{{' + k + '}}', v);
				});
				
				return html;
			},


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

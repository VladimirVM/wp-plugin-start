<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\content\field;

use WPPluginStart\Plugin;
use WPPluginStart\Plugin\Content\Field;
use WPPluginStart\Plugin\Media;

class WPAutoComplete
{
	/**
	 * @var Field
	 */
	private $field;
	private $media = [
		'js' => [
			'field-auto-complete.js',
		],
	];
	private static $once = false;
	private static $template_list_item = false;

	/**
	 * WPMediaImage constructor.
	 * @param $field Field
	 */
	public function __construct($field = null)
	{
		$this->field = $field;
		$this->prepare();
	}

	public function prepare()
	{
		if (!self::$once) {
			self::$once = true;
			$this->doOnce();
		}

	}

	function doOnce()
	{
		Media::add(Plugin::class, $this->media, 'admin');

		$view = Plugin::component('content/field/WPAutoComplete/list-item.view', false);

		if ($view) {
			self::$template_list_item = file_get_contents($view);
		}

	}


	public function render()
	{
		$field = $this->field;
		$attr = $field->getAttr();

		$values = $field->getData('value');
		$name = Field::name($attr['name'] ?? '');

		$list_item_template = $field->getData('template-list-item', self::$template_list_item);
		$list_item_template = str_replace('{{name}}', esc_attr($name), $list_item_template);
		$list_items = '';

		if ($values) {
			foreach ($values as $list_id => $list_item) {
				$list_items .= str_replace(
					['{{value}}', '{{title}}'],
					[$list_id, $list_item],
					$list_item_template
				);
			}
		}

		$attr['class'] = (array)($attr['class'] ?? []);
		$attr['class'][] = 'media-button-field-container';
		$attr['class'][] = 'js-field-auto-complete';
		if (empty($attr['data-settings'])) {
			$attr['data-settings'] = [];
		}
		if (empty($attr['data-template'])) {
			$attr['data-template'] = $list_item_template;
		}

		unset($attr['name']);

		$view = Plugin::component('content/field/WPAutoComplete/view', false);

		ob_start();

		include $view;

		$out = ob_get_clean();

		return $out;
	}
}
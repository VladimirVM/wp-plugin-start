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

	function doOnce ()
	{
		Media::add(Plugin::class, $this->media, 'admin');
	}
	

	public function render()
	{
		$field = $this->field;
		
		$node = [
			'tag' => 'div',
//			'name' => [],
			'value' => null,
			'content' => '',
			'attr' => $field->getAttr(),
		];

		$template = '<%1$s %2$s>%3$s</%1$s>';

		$data = $field->getData();
//
		if (!empty($data['value'])) {
			$node['value'] = $data['value'];
		}

		$content = '
<div>
<div class="field-wrap"><input type="text" class="js-auto-complete-field-text"/></div>
<ul class="js-list-items">%1$s</ul>
</div>
';

		$list_item_template = $data['template-list-item'] ?? '<li class="js-list-item">
<span class="js-list-remove-item"><span class="dashicons-before dashicons-no"></span></span>
<input type="hidden" name="{{name}}[]" value="{{value}}"/> {{title}}
</li>';
		$list_item_template = str_replace('{{name}}', esc_attr($data['field']['name']), $list_item_template);
		$list_items = '';

		if ($node['value']) {
			foreach ($node['value'] as $list_id => $list_item) {
				$list_items .= str_replace(
					['{{value}}', '{{title}}'],
					[$list_id, $list_item],
					$list_item_template
				);
			}
		}

		$node['content'] = sprintf($content, $list_items);

		$node['attr']['class'] = (array)($node['attr']['class'] ?? []);
		$node['attr']['class'][] = 'media-button-field-container';
		$node['attr']['class'][] = 'js-field-auto-complete';
		if (empty($node['attr']['data-settings'])) {
			$node['attr']['data-settings'] = [];
		}
		if (empty($node['attr']['data-template'])) {
			$node['attr']['data-template'] = $list_item_template;
		}

		unset($node['attr']['name']);
		$attr = Field::attr($node['attr']);

		$out = sprintf($template, $node['tag'], $attr, $node['content']);

		if (isset($data['field']['description'])) {
			$out .= '<p class="description">' . $data['field']['description'] . '</p>';
		}

		return $out;
	}
}
<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\content\field;

use WPPluginStart\Plugin;
use WPPluginStart\Plugin\Content\Field;
use WPPluginStart\Plugin\Media;

class WPMediaImage
{
	private static $once = false;
	/**
	 * @var Field
	 */
	private $field;
	private $media = [
		'css' => [
			'field-media-button.css',
		],
		'js' => [
			'field-media-button.js',
		],
	];

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
		if (!did_action('wp_enqueue_media')) {
			wp_enqueue_media();
		}

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


		if (!empty($node['attr']['value'])) {
			$node['value'] = $node['attr']['value'];
		}

		$data = $field->getData('field', []);

		$image_src = $data['src'] ?? 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
		$image_id = $field->getData('value', null);
		$image_width = (int)($data['width'] ?? 50);
		$image_height = (int)($data['height'] ?? 50);
		
		if ($image_id) {
			$image_data = wp_get_attachment_image_src($image_id, array($image_width, $image_height));

			if (!empty($image_data[0])) {
				$image_src = $image_data[0];
			} else {
				$image_id = null;
			}
		}



		$content = '<input type="hidden" name="%2$s" value="%3$s" class="js-media-button-id">
		<span class="wrap" style="display: inline-block">
		<span class="image" style="display: inline-block; height: %4$s; width: %5$s;"><img src="%1$s" class="js-media-button-image"></span>
		<span class="acton" style="display: inline-block">
		<button type="button" class="button button-with-icon js-media-button-change-image"><div class="dashicons-before dashicons-plus"></div></button>
		<button type="button" class="button button-with-icon js-media-button-remove-image"><div class="dashicons-before dashicons-no"></div></button>
		</span>
		</span>';

		$node['content'] = sprintf($content, $image_src, Field::name($node['attr']['name']) ?? null, $image_id, $image_height . 'px', $image_width . 'px');

		$node['attr']['class'] = (array)($node['attr']['class'] ?? []);
		$node['attr']['class'][] = 'media-button-field-container';
		$node['attr']['class'][] = 'js-field-media-button';

		unset($node['attr']['name']);
		$attr = Field::attr($node['attr']);

		$out = sprintf($template, $node['tag'], $attr, $node['content']);

		if (isset($data['description'])) {
			$out .= '<p class="description">' . $data['description'] . '</p>';
		}

		return $out;
	}


}
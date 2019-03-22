<?php

namespace WPPluginStart\Plugin\Content;

use WPPluginStart\plugin\content\field\WPAutoComplete;
use WPPluginStart\plugin\content\field\WPMediaImage;

class Field
{
	static $without_close_tag = [
		'img' => true,
		'input' => true,
		'meta' => true,
		'link' => true,
		'base' => true,
		'hr' => true,
		'br' => true,
	];

	static $input_types = [
		'button' => true,
		'checkbox' => true,
		'color' => true,
		'date' => true,
		'datetime-local' => true,
		'email' => true,
		'file' => true,
		'hidden' => true,
		'image' => true,
		'month' => true,
		'number' => true,
		'password' => true,
		'radio' => true,
		'range' => true,
		'reset' => true,
		'search' => true,
		'submit' => true,
		'tel' => true,
		'text' => true,
		'time' => true,
		'url' => true,
		'week' => true,
	];
	static $form_tags = [
		'input' => true,
		'textarea' => true,
		'select' => true,
	];
	static $custom_tags = [
		'wp-media-image' => WPMediaImage::class,
		'wp-auto-complete' => WPAutoComplete::class,
	];

	private $tag = '';
	private $attr = [];
	private $data = [];
	private $render = null;

	public function __construct($tag, $attr = [], $data = [])
	{
		$this->tag = $tag;
		$this->attr = $attr;

		if (is_scalar($data)) {
			$data = ['value' => $data];
		}

		$this->data = (array)$data;

		$this->prepare();
	}

	function prepare()
	{
		if (!empty(static::$custom_tags[$this->tag]) && is_callable(self::$custom_tags[$this->tag], true)) {
			$this->render = new static::$custom_tags[$this->tag]($this);
		}

		do_action('wps-admin-field-prepare', $this);

	}

	function view()
	{
		echo $this->render();
	}


	function render()
	{
		if ($this->render) {
			return call_user_func([$this->render, 'render']);
		}

		if (isset($this->data['field']['items']) && $this->tag !== 'select') {
			return static::renderListTags($this);
		}

		return static::renderFormTag($this);
	}

	/**
	 * @param Field $self
	 *
	 * @return string
	 */
	static function renderFormTag($self = null)
	{
		if (!$self->tag) {
			return '';
		}

		$node = [
			'tag' => $self->tag,
			'name' => [],
			'value' => null,
			'label' => '',
			'content' => '',
			'attr' => $self->attr,
		];

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$node['tag']])) {
			$template .= '%3$s</%1$s>';
		}

		if (!empty($node['attr']['name'])) {
			$node['name'] = $node['attr']['name'];
		}

		$node['attr']['name'] = self::name($node['name']);

		if ($node['tag'] === 'select' && isset($node['attr']['multiple'])) {
			$node['attr']['name'] .= '[]';
		}

		if (!empty($node['attr']['value'])) {
			$node['value'] = $node['attr']['value'];
		}

		$node = self::prepareAttrValue($node, $self->data['value'] ?? null, $self->data['field']['items'] ?? []);

		$attr = self::attr($node['attr']);

		$out = sprintf($template, $self->tag, $attr, $node['content']);

		if (isset($self->data['field']['description'])) {
			$out .= '<p class="description">' . $self->data['field']['description'] . '</p>';
		}

		return $out;
	}

	static function renderListTags($self)
	{
		$out = '';
		if (!$self->tag) {
			return $out;
		}
		$attr = $self->attr;
		$value = null;

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$self->tag])) {
			$template .= '%3$s</%1$s>';
		}

		$name = !empty($attr['name']) ? (array)$attr['name'] : [];
		$out = '';

		$values = array_flip($self->data['value'] ?? []);

		foreach ($self->data['field']['items'] as $key => $item) {
			$node = [
				'tag' => $self->tag,
				'name' => array_merge($name, [$key]),
				'value' => '',
				'label' => '',
				'content' => '',
				'attr' => $attr,
			];

			if (is_array($item)) {
				if (isset($item['value'])) {
					$node['value'] = $item['value'];
				}
				if (isset($item['label'])) {
					$node['label'] = $item['label'];
				}
			} else {
				$parts = explode(':', $item, 2);
				$node['value'] = $parts[0];
				$node['label'] = $parts[1] ?? $node['value'];
			}

			$node = self::prepareAttrValue($node, $values[$node['value']] ?? null);

			$node['attr']['name'] = self::name($node['name']);

			unset($node['attr']['id']);

			$tag_attr = self::attr($node['attr']);

			$out .= '<p><label>' . sprintf($template, $self->tag, $tag_attr, $node['content']) . '<span>' . $node['label'] . '</span></label></p>';
		}

		if (isset($self->data['field']['description'])) {
			$out .= '<p class="description">' . $self->data['field']['description'] . '</p>';
		}

		return $out;
	}

	static function prepareAttrValue($node, $value = null, $items = [])
	{
//		$value = self::value($node['name'], $value);

		if (isset($node['attr']['type']) && $node['tag'] === 'input') {
			if ($value !== null) {
				$node['attr']['value'] = $value;
			}
			if (($node['attr']['type'] === 'radio' || $node['attr']['type'] === 'checkbox')) {
				if ($value !== null) {
					$node['attr']['checked'] = true;
				}
				$node['attr']['value'] = $node['value'];
			}
		} elseif ($node['tag'] === 'select') {
			if (is_array($value)) {
				$value = array_flip($value);
			}
			foreach ($items as $key => $label) {
				$attr = ' value=' . esc_attr($key);
				if (is_array($value)) {
					if (isset($value[$key])) {
						$attr .= ' selected';
					}
				} elseif ($value === $key) {
					$attr .= ' selected';
				}
				$node['content'] .= sprintf('<%1$s %2$s>%3$s</%1$s>', 'option', $attr, esc_html($label));
			}
		} elseif ($value !== null) {
			if (!isset(self::$without_close_tag[$node['tag']])) {
				$node['content'] = $value;
			} else {
				$node['attr']['value'] = $value;
			}
		}

		return $node;
	}


	static function name($name)
	{
		if (empty($name)) {
			return null;
		}
		$name = (array)$name;
		$out = array_shift($name);
		if (!empty($name)) {
			$out .= '[' . implode('][', $name) . ']';
		}
		return $out;
	}


	static function value($name, $value)
	{

		if (empty($name)) {
			return null;
		}

		if ($value === null) {
			return null;
		}

		$name = (array)$name;

		if (is_array($value)) {
			foreach ($name as $key) {
				if (!isset($value[$key])) {
					$value = null;
					break;
				} else {
					$value = $value[$key];
				}
			}
		}

		return $value;
	}

	static function attr($attrs)
	{
		$out = '';

		if (empty($attrs)) {
			return $out;
		}

		foreach ((array)$attrs as $name => $value) {
			$name = preg_replace('/[^a-z0-9\-_]/i', '', $name);
			if ($value === true) {
				$out .= ' ' . $name;
			} elseif ($value === null) {
				continue;
			} else {
				if ($name === 'class' && is_array($value)) {
					$value = implode(' ', $value);
				}
				if (substr($name, 0, 5) === 'data-' && !is_scalar($value)) {
					$value = json_encode($value);
				}
				$out .= ' ' . $name . '="' . esc_attr($value) . '" ';
			}
		}
		return $out;
	}


	static function init($page_slug, $key, $data = [])
	{
		$section = $data['section'] ?? null;
		$field = $data['field'];

		$self = Field::wpOptionBuild([
			'label_for' => $data['id'],
			'field' => $field,
			'key' => $key,
			'section' => $section,
		]);

		add_settings_field(
			$data['id'],
			$field['label'],
			[$self, 'view'],
			$page_slug,
			$key,
			[
				'label_for' => $data['id'],
				'field' => $field,
				'key' => $key,
				'section' => $section,
			]
		);

		if (empty($section) || empty($section->name)) {
			register_setting($page_slug, $field['name'], $section->args);
		}
	}

	static function wpOptionBuild($data)
	{
		static $values = [];
		$field = $data['field'] ?? [];
		$field['section'] = $data['section']->name ?? null;

		$option_name = '';
		if ($field['section']) {
			$option_name = $field['section'];
		} elseif (!empty($field['attr']['name'])) {
			$field['attr']['name'] = (array)$field['attr']['name'];
			$option_name = reset($field['attr']['name']);
		} elseif (!empty($field['name'])) {
			$field['name'] = (array)$field['name'];
			$option_name = reset($field['name']);
		}

		if (isset($data['label_for'])) {
			if (empty($field['attr'])) {
				$field['attr'] = [];
			}
			$field['attr']['id'] = $data['label_for'];
		}

		if ($option_name && empty($values[$option_name])) {
			$values[$option_name] = get_option($option_name);
		}

		return self::build($field, $values, $data, false);
	}


	static function build($field, $value = [], $data = [], $render = true)
	{
		$tag = '';
//		$data = [];

		$attr = $field['attr'] ?? [];
		$section_name = $field['section'] ?? null;
		$name = [];

		if (empty($data['field'])) {
			$data['field'] = $field;
		}

		if (isset($field['tag'])) {
			$tag = $field['tag'];
		}

		if (!$tag && isset($field['type'])) {
			$type = $field['type'];

			if (isset(self::$input_types[$type])) {
				$tag = 'input';
				$attr['type'] = $type;
			}

		}

		if (isset(self::$form_tags[$tag]) || !empty($attr['name']) || !empty($field['name'])) {

			if ($section_name) {
				$name[] = $section_name;
			}

			if (!empty($attr['name'])) {
				$name = array_merge($name, (array)$attr['name']);
			} elseif (!empty($field['name'])) {
				$name = array_merge($name, (array)$field['name']);
			}

			$attr['name'] = $name;
		}

		if (!empty($name) && !empty($value)) {
			$data['value'] = self::value($name, $value);
		}

		$tag = new self($tag, $attr, $data);

		if ($render) {
			return $tag->render();
		}

		return $tag;
	}

	/**
	 * @return string
	 */
	public function getTag(): string
	{
		return $this->tag;
	}

	/**
	 * @param string $tag
	 */
	public function setTag(string $tag): void
	{
		$this->tag = $tag;
	}

	/**
	 * @param string|null $name
	 * @param null $default
	 * @return array
	 */
	public function getAttr(string $name = null, $default = null)
	{
		if ($name !== null) {
			return $this->attr[$name] ?? $default;
		}
		return $this->attr;
	}

	/**
	 * @param $value
	 * @param null|string $name
	 */
	public function setAttr($value, $name = null): void
	{
		if ($name !== null) {
			$this->attr[$name] = $value;
		} else {
			$this->attr = $value;
		}
	}

	/**
	 * @param string|null $name
	 * @param null $default
	 * @return array
	 */
	public function getData(string $name = null, $default = null)
	{
		if ($name !== null) {
			return $this->data[$name] ?? $default;
		}
		return $this->data;
	}

	/**
	 * @param $value
	 * @param null|string $name
	 */
	public function setData($value, $name = null): void
	{
		if ($name !== null) {
			$this->data[$name] = $value;
		} else {
			$this->data = $value;
		}
	}

	/**
	 * @return object|null
	 */
	public function getRender()
	{
		return $this->render;
	}

	/**
	 * @param object|null $render
	 */
	public function setRender($render)
	{
		$this->render = $render;
	}


}
<?php

namespace WPPluginStart\Plugin\Admin;


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
	static $renders = [
		'default' => [__CLASS__, 'renderFormTag'],
		'list' => [__CLASS__, 'renderListTags'],
	];

	private $tag = '';
	private $attr = [];
	private $data = [];
	/**
	 * @var Option
	 */
	private $section = null;

	public function __construct($tag, $attr = [], $data = [])
	{
		$this->tag = $tag;
		$this->attr = $attr;
		
		if (is_scalar($data)) {
		    $data = ['value' => $data];
		}
		
		$this->data = (array)$data;
	}

	function render()
	{
		if (isset($this->data['field']['items']) && $this->tag !== 'select') {
			return self::renderListTags($this);
		}
		return self::renderFormTag($this);
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
			'attr' => $self->attr
		];

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$node['tag']])) {
			$template .= '%3$s</%1$s>';
		}

		if (!empty($node['attr']['name'])) {
			$node['name'] = $node['attr']['name'];
		}

		if (!empty($node['attr']['value'])) {
			$node['value'] = $node['attr']['value'];
		}

		$node['attr']['name'] = self::name($node['name']);
		
		if ($node['tag'] === 'select' && isset($node['attr']['multiple'])) {
			$node['attr']['name'] .= '[]';
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
		foreach ($self->data['field']['items'] as $key => $item) {
			$node = [
				'tag' => $self->tag,
				'name' => array_merge($name, [$key]),
				'value' => '',
				'label' => '',
				'content' => '',
				'attr' => $attr
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
			
			$node = self::prepareAttrValue($node, $self->data['value'][$key] ?? null);

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

		if (isset($node['attr']['type'])) {
			$node['attr']['value'] = $value;
			if (($node['attr']['type'] === 'radio' || $node['attr']['type'] === 'checkbox')) {
				if ($value === $node['value']) {
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
				$out .= ' ' . $name . '="' . esc_attr($value) . '" ';
			}
		}
		return $out;
	}


	static function init($page_slug, $key, $data = [])
	{
		$section = $data['section'];
		$field = $data['field'];

		add_settings_field(
			$data['id'],
			$field['label'],
			[self::class, 'build'],
			$page_slug,
			$key,
			[
				'label_for' => $data['id'],
				'field' => $field,
				'key' => $key,
				'section' => $section
			]
		);

		if (!$section->name) {
			register_setting($page_slug, $field['name'], $section->args);
		}

	}

	static function build($data)
	{
		static $values = [];
		$tag = '';
		$attr = $data['field']['attr'] ?? [];
		$field = $data['field'] ?? [];
		$section = $data['section'] ?? null;
		$value = [];
		$name = [];

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

		if (isset(self::$form_tags[$tag])) {

			if (isset($data['label_for'])) {
				$attr['id'] = $data['label_for'];
			}

			$name = [];

			if (!empty($section->name)) {
				$name[] = $section->name;
			}

			if (!empty($attr['name'])) {
				$name = array_merge($name, (array)$attr['name']);
			} elseif (!empty($field['name'])) {
				$name = array_merge($name, (array)$field['name']);
			}

			if (!empty($name)) {
				$first_name = reset($name);
				if (!isset($values[$first_name])) {
					$values[$first_name] = get_option($first_name, '');
				}
				$value[$first_name] = $values[$first_name];
			}

			$attr['name'] = $name;
		}

		$data['value'] = self::value($name, $value);

		$tag = new self($tag, $attr, $data);

		echo $tag->render();
	}


}
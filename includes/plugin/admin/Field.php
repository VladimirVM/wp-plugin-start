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
		$this->data = $data;
	}

	function render()
	{
		if (isset($this->data['field']['items'])) {
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
		$content = '';
		$attrs = $self->attr;
		$value = null;

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$self->tag])) {
			$template .= '%3$s</%1$s>';
		}

		if (!empty($attrs['name'])) {
			$value = self::value($attrs['name']);

			$attrs['name'] = self::name($attrs['name']);
		}

		if ($value !== null) {
			if (!isset(self::$without_close_tag[$self->tag])) {
				$content = $value;
			} else {
				$attrs['value'] = $value;
			}
		}

		$tag_attr = self::attr($attrs);

		$out = sprintf($template, $self->tag, $tag_attr, $content);

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
		$content = '';
		$attrs = $self->attr;
		$value = null;

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$self->tag])) {
			$template .= '%3$s</%1$s>';
		}

		$name = !empty($attrs['name']) ? (array)$attrs['name'] : [];
		$out = '';
		foreach ($self->data['field']['items'] as $key => $item) {
			$item_name = array_merge($name, [$key]);
			$item_value = '';
			$item_label = '';
			$item_attrs = $attrs;
			
			unset($item_attrs['id']);

			if (is_array($item)) {
				if (isset($item['value'])) {
					$item_value = $item['value'];
				}
				if (isset($item['label'])) {
					$item_label = $item['label'];
				}
			} else {
				$parts = explode(':', $item, 2);
				$item_value = $parts[0];
				$item_label = $parts[1] ?? $item_value;
			}

			$value = self::value($name);

			if (isset($attrs['type'])) {
				$item_attrs['value'] = $value;
				if (($attrs['type'] === 'radio' || $attrs['type'] === 'checkbox')) {
					if (isset($value[$key]) && $value[$key] === $item_value) {
						$item_attrs['checked'] = true;
					}
					$item_attrs['value'] = $item_value;
				}
			} elseif ($value !== null) {
				if (!isset(self::$without_close_tag[$self->tag])) {
					$content = $value;
				} else {
					$item_attrs['value'] = $value;
				}
			}

			$item_attrs['name'] = self::name($item_name);

			$tag_attr = self::attr($item_attrs);

			$out .= '<p><label>' . sprintf($template, $self->tag, $tag_attr, $content) . '<span>' . $item_label . '</span></label></p>';
		}

		if (isset($self->data['field']['description'])) {
			$out .= '<p class="description">' . $self->data['field']['description'] . '</p>';
		}

		return $out;
	}


	static function name($attr_name)
	{
		$attr_name = (array)$attr_name;
		$name = array_shift($attr_name);
		if (!empty($attr_name)) {
			$name .= '[' . implode('][', $attr_name) . ']';
		}
		return $name;
	}


	static function value($name)
	{

		if (empty($name)) {
			return null;
		}

		$name = (array)$name;
		$_name = array_shift($name);

		$value = get_option($_name, null);

		if ($value === null) {
			return null;
		}

		if (!count($name)) {
			return $value;
		}

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
		$tag = '';
		$attr = [];
		$field = $data['field'] ?? [];
		$section = $data['section'] ?? null;

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
			if (!empty($attr['name'])) {
				$name[] = (array)$attr['name'];
			}

			if (isset($field['name'])) {
				$name[] = $field['name'];
			}

			if ($section && !empty($section->name)) {
				array_unshift($name, $section->name);
			}

			$attr['name'] = $name;
		}
		
		$tag = new self($tag, $attr, $data);

		echo $tag->render();
	}


}
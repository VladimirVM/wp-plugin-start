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

	private $tag = '';
	private $attr = [];
	private $data = [];
	private $content = '';


	public function __construct($tag, $attr = [], $data = [])
	{
		$this->tag = $tag;
		$this->attr = $attr;
		$this->data = $data;

		$this->render();
	}

	function prepare()
	{
		
	}

	function render()
	{
		if (!$this->tag) {
			return;
		}
		$content = '';
		$tag_attr = '';
		$attrs = $this->attr;
		$value = null;

		$template = '<%1$s %2$s>';
		if (!isset(self::$without_close_tag[$this->tag])) {
			$template .= '%3$s</%1$s>';
		}
		
		if (isset($attrs['name']) and is_array($attrs['name'])) {
			$value = self::value($attrs['name']);

			$attrs['name'] = self::name($attrs['name']);
		}
		
		if ($value !== null) {
			if (!isset(self::$without_close_tag[$this->tag])) {
				$content = $value;
			} else {
				$attrs['value'] = $value;
			}
		}
		

		foreach ($attrs as $k => $v) {
			$tag_attr .= ' ' . preg_replace('/[^a-z0-9\-_]/i', '', $k) . '="' . esc_attr($v) . '" ';
		}
		
		$out = sprintf($template, $this->tag, $tag_attr, $content);
		
		echo $out;
	}
	
	static function name ($attr_name)
	{
		$attr_name = (array)$attr_name;
		$name = array_shift($attr_name);
		if (!empty($attr_name)) {
			$name .= '[' . implode('][', $attr_name) . ']';
		}
		return $name;
	}
	
	
	static function value ($name)
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
	

	static function init($page_slug, $key, $data)
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

		$form_tags = [
			'input' => true,
			'textarea' => true,
		];

		if (isset($form_tags[$tag])) {

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


		new self($tag, $attr, $data);
	}


}
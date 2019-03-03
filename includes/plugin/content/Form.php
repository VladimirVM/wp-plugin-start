<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\content;


use Valitron\Validator;
use WPPluginStart\Plugin\Admin\Field;

class Form
{
	private $method = 'post';
	private $fields = [];
	private $values = [];
	private $index = 0;
	/**
	 * @var Validator
	 */
	private $validate = null;

	public $template = [
		'field' => '<div class="item-field item-field-default {%field-class%}">
<label class="wrap">
<span class="label">{%label%}</span>
<span class="element">
<span class="field">{%field%}</span>
<span class="message">{%message%}</span>
</span>
</label>
</div>',
		'field-wp-option-table' => '<tr class="item-field item-field-wp-option-table {%field-class%}">
<th scope="row"><label class="label" for="{%id%}">{%label%}</label></th>
<td>
<div class="field">{%field%}</div>
<p class="message">{%message%}</p>
</td>
</tr>',
	];

	public function __construct($fields = [], $values = null, $method = 'post')
	{
		$this->method = strtolower($method);
		$this->fields = $fields;
		$this->values = $values;

		if ($this->values === null) {
			if ($this->method === 'post') {
				$this->values = $_POST ?? [];
			} elseif ($this->method === 'get') {
				$this->values = $_GET ?? [];
			}
		}

		$this->validateInit();
	}

	function validateInit()
	{
		$v = new Validator($this->values);

		foreach ($this->fields as $name => $field) {
			if (empty($field['validate'])) {
				continue;
			}
			if (empty($field['name']) && empty($data['attr']['name']) && is_numeric($name)) {
				continue;
			}
			if (!empty($field['name'])) {
			    $name = $field['name'];
			} elseif(!empty($data['attr']['name'])) {
				$name = $data['attr']['name'];
			}
			
			foreach ($field['validate'] as $rule => $args) {
				$args = array_merge([
					$rule,
					$name,
				], (array)$args);
				call_user_func_array([$v, 'rule'], $args);
			}

		}
		
		$this->validate = $v;
		
		$this->isValid();		
	}

	function start($attr = [])
	{
//		$attr = [];
		if (is_string($attr)) {
			$attr = ['action' => $attr];
		}
		if (empty($attr['method'])) {
			$attr['method'] = $this->method;
		}

		$form = (new Field('form', $attr))->render();

		return str_replace('</form>', '', $form);
	}

	function field($name, $template = null, $attr = [])
	{
		if (empty($this->fields[$name])) {
			return;
		}
		
		$this->index++;

		$data = $this->fields[$name];

		$id = $data['attr']['id'] ?? 'form-field-id-' . $this->index;

		if (empty($data['attr'])) {
			$data['attr'] = [];
		}

		if (!empty($attr)) {
			$data['attr'] = array_merge($data['attr'], $attr);
		}

		if (empty($data['attr']['id'])) {
			$data['attr']['id'] = $id;
		}
		
		if (empty($data['name']) && empty($data['attr']['name'])) {
			$data['name'] = $name;
		}


		$field = Field::build($data, $this->values);
		
		if ((isset($data['type']) && $data['type'] === 'hidden') || (isset($data['attr']['type']) && $data['attr']['type'] === 'hidden')) {
		    return $field;
		}


		$class = implode(' ', [
			$data['label'] ? '' : 'without-label',
			'item-field-name-' . $name,
		]);

		$template = $this->template[$template] ?? $this->template['field'];

		$message = '';
		
		if ($error = $this->error($name)) {
			$message = '<div class="error-message"><div>' . implode('</div><div>', $error) . '</div></div>';
		}
		

		$out = str_replace(
			[
				'{%field-class%}',
				'{%label%}',
				'{%field%}',
				'{%message%}',
				'{%id%}',
				'{%index%}'
			],
			[
				$class,
				$data['label'] ?? '',
				$field,
				$message,
				$id,
				$this->index,
			],
			$template
		);


		return $out;
	}


	function close()
	{
		$out = (new Field('input', ['type' => 'hidden', 'name' => ['form', 'action'], 'value' => '1']))->render();
		return $out . '</form>';
	}

	function action($text = 'Save', $action = 'submit', $attr = [])
	{
		$attr['type'] = $action;
		return (new Field('button', $attr, $text))->render();
	}


	function isValid()
	{
		static $is = null;
		
		if ($is === null) {
		    $is = $this->validate->validate();
		}
		
		return $is;
	}

	function error($name = null)
	{
		return $this->validate->errors($name);
	}


}
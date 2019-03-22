<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\content;


use Valitron\Validator;
use WPPluginStart\Plugin\Content\Field;

class Form
{
	private $method = 'post';
	private $fields = [];
	private $_fields = [];
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

		$this->prepare();
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
			} elseif (!empty($data['attr']['name'])) {
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

	function prepare()
	{
		foreach ($this->fields as $name => $field) {
			$field['name'] = $field['name'] ?? $name;
			$this->_fields[$name] = Field::build($field, $this->values, [], false);
		}
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
		if (empty($this->_fields[$name])) {
			return;
		}

		$this->index++;

		/**
		 * @var Field $field
		 */
		$field = $this->_fields[$name];

		if (!empty($attr)) {
			$field->setAttr(array_merge($field->getAttr(), $attr));
		}

		if (null === ($id = $field->getAttr('id'))) {
			$id = 'form-field-id-' . $this->index;
			$field->setAttr($id, 'id');
		}

		$out = $field->render();

		if ($field->getAttr('type') === 'hidden') {
			return $out;
		}

		$label = $this->fields[$name]['label'] ?? '';

		$class = implode(' ', [
			$label ? '' : 'without-label',
			'item-field-name-' . $name,
			'item-field-tag-' . $field->getTag()
		]);

		$template = $this->template[$template] ?? $this->template['field'];

		$message = '';

		if ($error = $this->error($name)) {
			$message = '<div class="error-message"><div>' . implode('</div><div>', $error) . '</div></div>';
		}


		$_out = str_replace(
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
				$label,
				$out,
				$message,
				$id,
				$this->index,
			],
			$template
		);


		return $_out;
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
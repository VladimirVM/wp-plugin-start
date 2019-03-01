<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\content;


use WPPluginStart\Plugin\Admin\Field;

class Form
{
	private $method = 'post';
	private $fields = [];
	private $values = [];
	private $validate = [];
	
	public function __construct($fields = [], $validate = [], $method = 'post', $values = null)
	{
		$this->method = strtolower($method);
		$this->fields = $fields;
		$this->values = $values;
		$this->validate = $validate;
		
		if ($this->values === null) {
		    if ($this->method === 'post') {
		        $this->values = $_POST??[];
		    } elseif ($this->method === 'get') {
		        $this->values = $_GET??[];
		    }
		}
	}
	
	function start ($attr = [])
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
	
	function field ($name)
	{
	    if (empty($this->fields[$name])) {
	        return ;
	    }
	    
	    $field = Field::build($this->fields[$name], $this->values);
	    
	    return $field;
	}
	
	
	function close ()
	{
	    return '</form>';
	}
	
	function isValid ()
	{
	    
	}
	
	

}
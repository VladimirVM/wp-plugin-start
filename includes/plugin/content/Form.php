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
	private $data = [];
	private $validate = [];
	
	public function __construct($fields = [], $validate = [], $method = 'post', $data = null)
	{
		$this->method = strtolower($method);
		$this->fields = $fields;
		$this->data = $data;
		$this->validate = $validate;
		
		if ($this->data === null) {
		    if ($this->method === 'post') {
		        $this->data = $_POST??[];
		    } elseif ($this->method === 'get') {
		        $this->data = $_GET??[];
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
	    
	}
	
	
	function close ()
	{
	    return '</form>';
	}
	
	function isValid ()
	{
	    
	}
	
	

}
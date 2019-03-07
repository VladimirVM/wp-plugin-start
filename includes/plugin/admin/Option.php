<?php

namespace WPPluginStart\Plugin\Admin;


use WPPluginStart\Plugin;
use WPPluginStart\Plugin\Settings;

class Option
{

	static $page_slug;
	static $page_blocks;

	public $uid = null;
	public $key = '';
	public $name = null;
	public $title = '';
	public $args = [];
	public $fields = [];
	public $description = '';
	public $plugin_key = '';


	function __construct($option_key, $settings = [], $uid = null)
	{
		$this->key = $option_key;
		$this->uid = $uid;
		
		foreach ($settings as $key => $value) {
			$this->{$key} = $value;
		}

		$this->prepare();
		$this->register();
	}

	function prepare()
	{
		if ($this->name === null) {
			$this->name = $this->plugin_key . '_' . $this->key;
		}
		if ($this->uid === null) {
			$this->uid = self::$page_slug;
		}
		
	}

	function register()
	{
		if ($this->name) {
			register_setting($this->uid, $this->name, $this->args);
		}

		$section_key = $this->key . '_section';

		add_settings_section(
			$section_key,
			$this->title,
			[$this, 'sectionRented'],
			$this->uid
		);

		if (!empty($this->fields)) {
			foreach ($this->fields as $field) {

				$id = $this->plugin_key . '_' . $field['name'];

				Field::init($this->uid, $section_key, [
					'section' => $this,
					'field' => $field,
					'id' => $id,
				]);
			}
		}

	}

	function sectionRented()
	{
		echo $this->description;
	}
	
	function render ()
	{
	    include Plugin::template('admin/option.php', false);
	}
	
	
	static function build ($key, $settings, $uid, $args = [])
	{
		$self = new self($key, $settings, $uid);
		
		return [$self, 'render'];
	}
	

//	static function init($slug = '', $blocks = [])
//	{
//		self::$page_slug = $slug;
//		self::$page_blocks = array_combine($blocks, $blocks);
//		add_action('admin_init', [__CLASS__, 'load']);
//	}
//
//	static function load()
//	{
//		$blocks = Settings::get('blocks', []);
//
//		$blocks = array_filter($blocks, function ($value, $key) {
//			return (isset($value['type']) && $value['type'] === 'option' && self::$page_blocks[$key]);
//		}, ARRAY_FILTER_USE_BOTH);
//
//
//		foreach ($blocks as $key => $block) {
//			new self($key, $block);
//		}
//	}

}
<?php

namespace WPPluginStart\Plugin\Admin;


use WPPluginStart\Plugin;

class Option
{

	static $page_slug;
	static $page_blocks;

	/**
	 * @var Page
	 */
	public $page = null;
	public $key = '';
	public $name = null;
	public $title = '';
	public $args = [];
	public $fields = [];
	public $description = '';
	public $plugin_key = '';

	public $uid = null;

	function __construct($option_key, $settings = [], $page = null)
	{
		$this->key = $option_key;
		$this->page = $page;

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
			$this->uid = $this->plugin_key . '/' . $this->page->slug . '/' . $this->key;
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

	function render()
	{
		$views = Plugin::component('block/option/' . $this->key . '/view', false);
		if ($views) {
			include $views;
		}
	}


	static function build($key, $settings, $page, $args = [])
	{
		$self = new self($key, $settings, $page);

		return [$self, 'render'];
	}

}
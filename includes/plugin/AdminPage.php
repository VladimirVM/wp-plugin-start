<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin;

use WPPluginStart\Plugin;

class AdminPage
{
	private static $pages = [];
	private static $path_to_template = 'admin/page';
	private static $default = [
		'parent' => false,
		'page' => '',
		'menu' => '',
		'capability' => 'manage_options',
		'slug' => '',
		'function' => null,
		'icon' => '',
		'position' => null,
	];
	/**
	 * @var array self::$default
	 */
	private $settings = [];


	function __construct($settings)
	{
		if (empty($settings)) {
		    return;
		}
		
		$this->settings = array_merge(self::$default, $settings);

		$this->settings();
		$this->add();
	}

	function settings()
	{
		if ($this->settings['function'] === null) {
			$this->settings['function'] = [$this, 'content'];
		}
	}


	function content()
	{

	}

	function add()
	{
		if (!empty($this->settings['parent'])) {
			add_submenu_page(
				$this->settings['parent'],
				$this->settings['page'],
				$this->settings['menu'],
				$this->settings['capability'],
				$this->settings['slug'],
				$this->settings['function']
			);
		} else {
			add_menu_page(
				$this->settings['page'],
				$this->settings['menu'],
				$this->settings['capability'],
				$this->settings['slug'],
				$this->settings['function'],
				$this->settings['icon'],
				$this->settings['position']
			);
		}
	}


	static function init()
	{
		add_action('admin_menu', [__CLASS__, 'addAll']);
	}

	static function addAll()
	{
		$pages = Settings::get('pages', []);
		if (empty($pages)) {
		    return;
		}
		foreach ($pages as $page) {
			new self($page);
		}
	}

	static function generateItem($key, $parent = false, $add_plugin_key = true)
	{
		$args = self::$default;
		$args['parent'] = $parent;
		
		if (is_scalar($key)) {
			$args['slug'] = $key;
		} else {
			$args = array_merge($args, (array)$key);
			$key = $args['slug'];
		}
		
		$args['slug'] = sanitize_key($args['slug']);
		
		if (empty($args['slug'])) {
		    return false;
		}
		
		if (empty($args['menu'])) {
			$args['menu'] = $key;
		}

		if (empty($args['page'])) {
			$args['page'] = $args['menu'];
		}

		if (isset($args['template'])) {
			$args['template'] = Plugin::template($args['template'], false);
		}

		if (empty($args['template'])) {
			$args['template'] = Plugin::template(self::$path_to_template . '/' . $args['slug'] . '.php', false);
		}
		
		if ($add_plugin_key) {
			$args['slug'] = Settings::$plugin_key . '--' . $args['slug'];
		}

		return $args;
	}

}
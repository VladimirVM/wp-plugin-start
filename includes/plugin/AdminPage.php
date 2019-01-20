<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin;

class AdminPage
{
	private $pages = [];

	function __construct()
	{
		$this->pages = Settings::get('pages', []);
		if (!empty($this->pages)) {
			add_action('admin_menu', [$this, 'add']);
		}

	}

	function add()
	{
		foreach ($this->pages as $page) {
			add_submenu_page(
				'tools.php',
				'plugin_theme_name',
				'plugin_theme_name',
				'manage_options',
				'plugin_theme_name',
				[$this, 'content']
			);
			add_menu_page(
				'plugin_theme_name',
				'plugin_theme_name',
				'manage_options',
				'plugin_theme_name',
				[$this, 'content']
			);
		}
	}

	function content()
	{

	}

	static function generateItem($key, $parent = false)
	{
		$args = [
			'parent' => $parent,
			'page' => '',
			'menu' => '',
			'capability' => 'manage_options',
			'slug' => '',
			'function' => '',
		];
		
		if (is_scalar($key)) {
		    $args['slug'] = strtolower(preg_replace('#\s+#', '-', $key));
		} else {
			$args = array_merge((array)$key, $args);
			$key = $args['slug'];
		}
		
		if (empty($args['menu'])) {
			$args['menu'] = $key;
		}
		
		if (empty($args['page'])) {
			$args['page'] = $args['menu'];
		}
		
		return $args;
	}

}
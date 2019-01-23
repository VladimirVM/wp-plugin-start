<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin\Admin;

use WPPluginStart\Plugin;
use WPPluginStart\Plugin\Settings;

class Page
{
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
//		echo '<pre>' . __FILE__ . '(' . __LINE__ . ')';//zzz
//		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($settings, true), 3, 'UTF-8');
//		echo '</pre>';
		
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
//		echo '<pre>' . __FILE__ . '(' . __LINE__ . ')';//zzz
//		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($this->settings, true), 3, 'UTF-8');
//		echo '</pre>';
		
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
//		echo '<pre>' . __FILE__ . '(' . __LINE__ . ')';//zzz
//		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($pages, true), 3, 'UTF-8');
//		echo '</pre>';
		
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
		if ($parent) {
			$args['parent'] = self::parentSlug($parent, $add_plugin_key);
		}
		
		if (is_scalar($key)) {
			$args['slug'] = $key;
		} else {
			$args = array_merge($args, (array)$key);
			$key = $args['slug'];
		}
		
		$slug = self::pageSlug($args['slug'], false);
		$slug_and_key = $slug;
		
		if ($add_plugin_key) {
			$slug_and_key = self::pageSlug($args['slug'], $add_plugin_key);
		}

		if (empty($slug)) {
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
			$args['template'] = Plugin::template(self::$path_to_template . '/' . $slug . '.php', false);
		}

		$args['slug'] = $slug_and_key;

		return $args;
	}
	
	static function pageSlug ($slug, $add_plugin_key = true)
	{
		$slug = preg_replace('#\s+#', '-', $slug);
		$slug = sanitize_key($slug);

		if ($add_plugin_key) {
			$slug = Settings::$plugin_key . '--' . $slug;
		}
		
		return $slug;
	}
	
	static function parentSlug ($slug,  $add_plugin_key = true)
	{
	    if (substr($slug, -4) === '.php') {
	        return $slug;
	    }
	    
	    return self::pageSlug($slug,  $add_plugin_key);
	}
	
	
}
<?php

namespace WPPluginStart\Plugin\Admin;

use WPPluginStart\Plugin;
use WPPluginStart\Plugin\Settings;

class Page
{
	private static $default = [
		'parent' => false,
		'page' => '',
		'menu' => '',
		'capability' => 'manage_options',
		'slug' => '',
		'render' => null,
		'icon' => '',
		'position' => null,
		'css' => [],
		'js' => [],
	];
	static $hooks = [];
	static $blocks_type = [
		'option' => ['\WPPluginStart\Plugin\Admin\Option', 'build'],
	];
	private $blocks = [];
	static $slug = '';

	/**
	 * @var array self::$default
	 */
	private $settings = [];
	/**
	 * @var Plugin
	 */
	private $plugin = null;


	function __construct($settings, $plugin)
	{

		if (empty($settings)) {
			return;
		}

		$this->plugin = $plugin;

		$this->settings = array_merge(static::$default, $settings);

		$this->prepare();
		$this->add();
	}

	function prepare()
	{
		if ($this->settings['render'] === null) {
			$this->settings['render'] = [$this, 'render'];
		}
		static::$slug = $this->settings['slug'];
	}

	function media()
	{
		$media = [
			'css' => $this->settings['css'],
			'js' => $this->settings['js'],
		];

		Plugin\Media::init($media, 'admin');
	}

	function build()
	{
		if (!empty($this->settings['blocks'])) {
			$blocks = $this->plugin::settings('blocks', []);

			foreach ($this->settings['blocks'] as $key => $name) {
				$args = null;

				if (!is_scalar($name)) {
					$args = $name;
					$name = $key;
				}

				if (empty($blocks[$name])) {
					$this->plugin::notice('Block name "' . $name . '" in undefined ');
					continue;
				}

				$block = $blocks[$name];

				$build = null;
				if (empty($block['type'])) {
					$this->plugin::notice('Block type is empty. For block: ' . $name);
					continue;
				}

				if (is_callable($block['type'])) {
					$build = $block['type'];
				} elseif (isset(static::$blocks_type[$block['type']])) {
					$build = static::$blocks_type[$block['type']];
				}

				if (!$build) {
					$this->plugin::notice('Can not build block');
					continue;
				}

				$this->blocks[] = call_user_func($build, $name, $block, $args);

			}
		}
	}

	function render()
	{
		include $this->plugin::template('admin/page.php', false);
	}


	function content()
	{
		foreach ($this->blocks as $block) {
			if (is_callable($block)) {
				call_user_func($block);
			} elseif (is_scalar($block)) {
				echo $block;
			}
		}
	}

	function add()
	{
		if (!empty($this->settings['parent'])) {
			$hook = add_submenu_page(
				$this->settings['parent'],
				$this->settings['page'],
				$this->settings['menu'],
				$this->settings['capability'],
				$this->settings['slug'],
				$this->settings['render']
			);
		} else {
			$hook = add_menu_page(
				$this->settings['page'],
				$this->settings['menu'],
				$this->settings['capability'],
				$this->settings['slug'],
				$this->settings['render'],
				$this->settings['icon'],
				$this->settings['position']
			);
		}

		static::$hooks[$this->settings['slug']] = $hook;
		
		if ($this->isLoad()) {
			$this->media();
		}

		add_action('admin_init', [$this, 'build']);
	}

	function isLoad()
	{
		$page = filter_input(INPUT_GET, 'page');

		return ($page === $this->settings['slug']);
	}


	static function init($plugin)
	{
		$pages = $plugin::settings('pages', []);

		add_action('admin_menu', function () use ($plugin, $pages) {
			foreach ($pages as $page) {
				new Page($page, $plugin);
			}
		});


	}

//	static function addAll()
//	{
//		$pages = $this->plugin::settings('pages', []);
//
//		if (empty($pages)) {
//			return;
//		}
//		foreach ($pages as $page) {
//			new self($page);
//		}
//	}

	static function generateItem($key, $parent = false, $add_plugin_key = '')
	{
		$args = static::$default;
		if ($parent) {
			$args['parent'] = static::parentSlug($parent, $add_plugin_key);
		}

		if (is_scalar($key)) {
			$args['slug'] = $key;
		} else {
			$args = array_merge($args, (array)$key);
			$key = $args['slug'];
		}

		$slug = static::pageSlug($args['slug'], false);
		$slug_and_key = $slug;

		if ($add_plugin_key) {
			$slug_and_key = static::pageSlug($args['slug'], $add_plugin_key);
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

//		if (isset($args['template'])) {
//			$args['template'] = $this->plugin::template($args['template'], false);
//		}
//
//		if (empty($args['template'])) {
//			$args['template'] = $this->plugin::template('page/' . $slug . '.php', false);
//		}

		$args['slug'] = $slug_and_key;

		return $args;
	}

	static function pageSlug($slug, $add_plugin_key = '')
	{
		$slug = preg_replace('#\s+#', '-', $slug);
		$slug = sanitize_key($slug);

		if ($add_plugin_key) {
			$slug = $add_plugin_key . '--' . $slug;
		}

		return $slug;
	}

	static function parentSlug($slug, $add_plugin_key = true)
	{
		if (substr($slug, -4) === '.php') {
			return $slug;
		}

		return static::pageSlug($slug, $add_plugin_key);
	}

	static function isPluginPage()
	{
		$page = filter_input(INPUT_GET, 'page');

		if (isset(static::$hooks[$page])) {
			return static::$hooks[$page];
		}
		return false;
	}


}
<?php

namespace WPPluginStart\Plugin;

use WPPluginStart\Plugin;

class Media
{
	static $css = [
		'folder' => 'css',
		'default' => ['ver' => null, 'deps' => [], 'media' => 'all'],
		'admin' => [],
		'front' => [],
	];

	static $js = [
		'folder' => 'js',
		'default' => ['ver' => null, 'deps' => [], 'in_footer' => true],
		'admin' => [],
		'front' => [],
	];
	/**
	 * @var array [
	 *  'page-key' => [
	 *      'css' => [],
	 *      'js' => [],
	 *  ]
	 * ]
	 */
	static $admin_pages = [];

	static function init()
	{
		$action = 'wp_enqueue_scripts';
		if (is_admin()) {
			$action = 'admin_enqueue_scripts';
		}

		add_action($action, [__CLASS__, 'load']);
	}

	static function add($plugin, $items, $page_type, $page_key = null)
	{

		if ($page_key) {
			if (!empty($items['css'])) {
				foreach ($items['css'] as $key => $item) {
					self::$admin_pages[$page_key]['css'][] = self::prepare_item($item, $key, 'css', $plugin);
				}
			}
			if (!empty($items['js'])) {
				foreach ($items['js'] as $key => $item) {
					self::$admin_pages[$page_key]['js'][] = self::prepare_item($item, $key, 'js', $plugin);
				}
			}
		} else {
			if (!empty($items['css'])) {
				foreach ($items['css'] as $key => $item) {
					self::$css[$page_type][] = self::prepare_item($item, $key, 'css', $plugin);
				}
			}
			if (!empty($items['js'])) {
				foreach ($items['js'] as $key => $item) {
					self::$js[$page_type][] = self::prepare_item($item, $key, 'js', $plugin);
				}
			}
		}

	}

	static function key($file)
	{
		return sanitize_key(str_replace('/', '--', $file));

	}

	/**
	 * @param array $args
	 * @param string $key
	 * @param string $media_type css|js
	 * @param Plugin $plugin
	 * @return array|bool|mixed|string
	 */
	static function prepare_item($args, $key, $media_type, $plugin)
	{
		/**
		 * @var Plugin $plugin
		 */

		if ($media_type === 'css') {
			$default = self::$css['default'];
			$folder = '/' . self::$css['folder'] . '/';
		} else {
			$default = self::$js['default'];
			$folder = '/' . self::$js['folder'] . '/';
		}
		if (is_string($args)) {
			$args = ['file' => $args];
		}

		$args += $default;
		if (empty($args['file'])) {
			return false;
		}
		if ($args['ver'] === null) {
			$args['ver'] = $plugin::$version;
		}

		if (empty($args['key'])) {
			$args['key'] = self::key($plugin::$key . '--' . (!is_numeric($key) ? $key : $args['file']));
		}

		if (strpos($args['file'], '.') === false) {
			$args['key'] = $args['file'];
			$args['file'] = '';
			$args['deps'] = [];
			$args['ver'] = false;
			$args['in_footer'] = false;
		}

		if ($args['file']) {
			$dir_key = 'media';
			if ($args['file']{1} === '/') {
				$folder = '';
				$dir_key = '';
			}

			if (is_file($plugin::dir($dir_key) . $folder . $args['file'])) {
				$args['file'] = $plugin::url($dir_key) . $folder . $args['file'];
			}
		}

		return $args;
	}


	static function load()
	{
		$is_admin = is_admin();
		$key = $is_admin ? 'admin' : 'front';

		if (!empty(self::$css[$key])) {
			foreach (self::$css[$key] as $args) {
				if (empty($args)) {
					continue;
				}
				wp_enqueue_style($args['key'], $args['file'], $args['deps'], $args['ver'], $args['media']);
			}
		}

		if (!empty(self::$js[$key])) {
			foreach (self::$js[$key] as $args) {
				if (empty($args)) {
					continue;
				}
				wp_enqueue_script($args['key'], $args['file'], $args['deps'], $args['ver'], $args['in_footer']);
			}
		}

		if ($is_admin) {
			$page_key = Admin\Page::currentKey();

			if (!empty(self::$admin_pages[$page_key]['css'])) {
				foreach (self::$admin_pages[$page_key]['css'] as $args) {
					if (empty($args)) {
						continue;
					}
					wp_enqueue_style($args['key'], $args['file'], $args['deps'], $args['ver'], $args['media']);
				}
			}

			if (!empty(self::$admin_pages[$page_key]['js'])) {
				foreach (self::$admin_pages[$page_key]['js'] as $args) {
					if (empty($args)) {
						continue;
					}
					wp_enqueue_script($args['key'], $args['file'], $args['deps'], $args['ver'], $args['in_footer']);
				}
			}

		}

	}

}
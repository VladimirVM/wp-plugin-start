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

//	/**
//	 * @var Plugin $plugin
//	 */
//	private $plugin = null;
//
//	function __construct($plugin/*, $media, $type*/)
//	{
//		$this->plugin = $plugin;
////	    $this->type = $type;
//
//	}


	static function init()
	{
		$action = 'wp_enqueue_scripts';
		if (is_admin()) {
			$action = 'admin_enqueue_scripts';
		}

//		$call = null;
//		if (is_callable($media)) {
//			$call = $media;
//		} else {
//			$call = $this->loadOnPluginPage($media);
//		}

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

//
//	function register($items, $type = 'admin')
//	{
//		if (!empty($items['css'])) {
//			self::$css[$type] = array_merge(self::$css[$type], (array)$items['css']);
//		}
//		if (!empty($items['js'])) {
//			self::$js[$type] = array_merge(self::$js[$type], (array)$items['js']);
//		}
//		$this->init([$this, '_register_all'], $type);
//
//	}
//
//	function _register_all()
//	{
//		if (!empty($items['css'])) {
//			foreach ($items['css'] as $key => $css) {
//				$args = $this->prepare($css, $key, 'css');
//				if ($args) {
//					wp_register_style($args['key'], $args['file'], $args['deps'], $args['ver'], $args['media']);
//				}
//			}
//		}
//
//		if (!empty($items['js'])) {
//			foreach ($items['js'] as $key => $js) {
//				$args = $this->prepare($js, $key, 'js');
//				if ($args) {
//					wp_register_script($args['key'], $args['file'], $args['deps'], $args['ver'], $args['in_footer']);
//				}
//			}
//		}
//	}


	static function key($file)
	{
		return sanitize_key(str_replace('/', '--', $file));

	}

//	function prepare($args, $key, $type)
//	{
//		/**
//		 * @var Plugin $plugin
//		 */
//		$plugin = $this->plugin;
//		if ($type === 'css') {
//			$default = self::$css['default'];
//			$folder = '/' . self::$css['folder'] . '/';
//		} else {
//			$default = self::$js['default'];
//			$folder = '/' . self::$js['folder'] . '/';
//		}
//		if (is_string($args)) {
//			$args = ['file' => $args];
//		}
//
//		$args += $default;
//		if (empty($args['file'])) {
//			return false;
//		}
//		if ($args['ver'] === null) {
//			$args['ver'] = $plugin::$version;
//		}
//
//		if (empty($args['key'])) {
//			$args['key'] = self::key($plugin::$key . '--' . !is_numeric($key) ? $key : $args['file']);
//		}
//
//		if (is_file($plugin::$media_dir . $folder . $args['file'])) {
//			$args['file'] = $plugin::$media_url . $folder . $args['file'];
//		}
//
//		return $args;
//	}

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
			$args['key'] = self::key($plugin::$key . '--'  . (!is_numeric($key) ? $key : $args['file']));
		}

		if (is_file($plugin::dir('media') . $folder . $args['file'])) {
			$args['file'] = $plugin::url('media') . $folder . $args['file'];
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

//	static function loadOnPluginPage($items)
//	{
//		return function () use ($items) {
//			if (!Admin\Page::isPluginPage()) {
//				return;
//			}
//
//			self::load($items);
//		};
//	}
//
//	static function loadOnAllPage($items)
//	{
//		return function () use ($items) {
//			self::load($items);
//		};
//	}


}
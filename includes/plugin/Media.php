<?php

namespace WPPluginStart\Plugin;

class Media
{
	static $css = [
		'folder' => 'css',
		'default' => ['ver' => null, 'deps' => [], 'media' => 'all'],
	];

	static $js = [
		'folder' => 'js',
		'default' => ['ver' => null, 'deps' => [], 'in_footer' => true],
	];

	static function init($media, $type)
	{
		$action = 'wp_enqueue_scripts';
		if ($type === 'admin') {
			$action = 'admin_enqueue_scripts';
		}

		$call = null;
		if (is_callable($media)) {
			$call = $media;
		} else {
			$call = Media::loadOnPluginPage($media);
		}

		add_action($action, $call);
	}


	static function register($items)
	{

		if (!empty($items['css'])) {
			foreach ($items['css'] as $key => $css) {
				$args = self::prepare($css, $key, 'css');
				if ($args) {
					wp_register_style($args['key'], $args['file'], $args['deps'], $args['ver'], $args['media']);
				}
			}
		}

		if (!empty($items['js'])) {
			foreach ($items['js'] as $key => $js) {
				$args = self::prepare($js, $key, 'js');
				if ($args) {
					wp_register_script($args['key'], $args['file'], $args['deps'], $args['ver'], $args['in_footer']);
				}
			}
		}

	}

	static function key($file)
	{
		return sanitize_key(Settings::$plugin_key . '--' . str_replace('/', '--', $file));

	}

	static function prepare($args, $key, $type)
	{
		if ($type === 'css') {
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
			$args['ver'] = Settings::$version;
		}

		if (empty($args['key'])) {
			$args['key'] = self::key(!is_numeric($key) ? $key : $args['file']);
		}

		if (is_file(Settings::$plugin_media_dir . $folder . $args['file'])) {
			$args['file'] = Settings::$plugin_media_url . $folder . $args['file'];
		}

		return $args;
	}


	static function load($items)
	{

		if (!empty($items['js'])) {
			foreach ($items['js'] as $item) {
				wp_enqueue_script($item);
			}
		}
		if (!empty($items['css'])) {
			foreach ($items['css'] as $item) {
				wp_enqueue_style($item);
			}
		}
	}

	static function loadOnPluginPage($items)
	{
		return function () use ($items) {
			if (!Admin\Page::isPluginPage()) {
				return;
			}

			self::load($items);
		};
	}

	static function loadOnAllPage($items)
	{
		return function () use ($items) {
			self::load($items);
		};
	}


}
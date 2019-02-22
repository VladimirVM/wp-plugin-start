<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;


trait Build
{

	static $url = '';
	static $dir = '';
	static $key = '';
	static $main_file = '';
	static $basename = '';
	static $template_folder = 'templates';
	static $media_folder = 'media';
	static $template_url = '';
	static $template_dir = '';
	static $media_url = '';
	static $media_dir = '';
	static $version = 1.0;

	static $settings = null;
	static $_settings = 'settings.php';

	static public function settings($key = null, $default = null)
	{
		if (static::$settings === null) {
			static::$settings = new Plugin\Settings(static::$_settings);
		}

		return static::$settings->get($key, $default);
	}

	static public function dir($key = '')
	{
		
	}

	static public function url($key = '')
	{

	}

	static $_notice = [];

	static function notice($message, $type = 'warning', $class = '')
	{
		$class .= 'notice notice-' . $type;
		static::$_notice[] .= sprintf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
	}


}


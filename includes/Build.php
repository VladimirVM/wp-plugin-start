<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;


trait Build
{

	static $url = '';
	static $dir = '';
	static $dirs = [];
	static $urls = [];
	static $key = '';
	static $main_file = '';
	static $basename = '';
	static $template_folder = 'templates';
	static $media_folder = 'media';
//	static $template_url = '';
//	static $template_dir = '';
//	static $media_url = '';
//	static $media_dir = '';
	static $version = 1.0;

	static $settings = null;
	static $_settings = 'settings.php';

	/**
	 * @var Plugin
	 */
	static $instant = null;

	static public function settings($key = null, $default = null)
	{
		if (static::$settings === null) {
			static::$settings = new Plugin\Settings(static::$_settings);
		}

		return static::$settings->get($key, $default);
	}

	static public function dir($key = null, $set = null)
	{
		if ($set === null) {
		    if (is_string($key)) {
		    	if (isset(static::$dirs[$key])) {
		        	return static::$dirs[$key];
		    	} else {
		    		return static::$dir . '/' . (string)$key;
				}
		    } else {
		    	return static::$dir;
			}
		} else {
			return static::$dirs[$key] = static::$dir . '/' . (string)$set;
		}
	}

	static public function url($key = '', $set = null)
	{
		if ($set === null) {
			if (is_string($key)) {
				if (isset(static::$urls[$key])) {
					return static::$urls[$key];
				} else {
					return static::$url . '/' . (string)$key;
				}
			} else {
				return static::$url;
			}
		} else {
			return static::$urls[$key] = static::$url . '/' . (string)$set;
		}
	}

	static $_notice = [];

	static function notice($message, $type = 'warning', $class = '')
	{
		$class .= 'notice notice-' . $type;
		$notice = sprintf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
		static::$_notice[] .= $notice;
		return $notice;
	}


}


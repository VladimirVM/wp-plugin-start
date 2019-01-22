<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin;

class Settings
{
	public $settings_file_name = 'settings.php';
	static $plugin_dir = '';
	static $plugin_key = '';
	static $plugin_template = 'template';
	static $plugin_main_file = '';
	static $plugin_url = '';
	static $settings = [];
	static $options = [];

	public function __construct($plugin_main_file, $settings_file_name = null)
	{
		self::$plugin_dir = dirname($plugin_main_file);
		self::$plugin_template = self::$plugin_dir . '/' . self::$plugin_template;
		self::$plugin_main_file = $plugin_main_file;
		self::$plugin_key = basename($plugin_main_file, '.php');
		self::$plugin_url = plugin_dir_url($plugin_main_file);

		if ($settings_file_name !== null) {
			$this->settings_file_name = $settings_file_name;
		}

		self::$settings = $this->loadFromFile();
	}

//	static $instance = null;
//    static function instance($plugin_main_file, $settings_file_name = null)
//    {
//
//        if (null === static::$instance) {
//            static::$instance = new Settings($plugin_main_file, $settings_file_name);
//        }
//
//        return static::$instance;
//    }

	static function get($key = null, $default = null)
	{
		if ($key !== null) {
			if (isset(self::$settings[$key])) {
				return self::$settings[$key];
			} else {
				return $default;
			}
		}

		return self::$settings;
	}

	static function option($key = null)
	{
		if ($key !== null) {
			if (!isset(self::$options[$key])) {
				self::$options[$key] = get_option($key);
			}
			return self::$options[$key];
		}

		return self::$options;
	}

	static function setOption($key, $value, $autoload = false)
	{
		add_option($key, $value, '', $autoload);
		self::$options[$key] = $value;
	}

	static function delOption($key)
	{
		delete_option($key);
		unset(self::$options[$key]);
	}

	static function table($name)
	{
		$tables = self::get('tables');

		if (isset($tables[$name]['name'])) {
			return $tables[$name]['name'];
		} else {
			return false;
		}
	}

	function loadFromFile()
	{

		$settings = [];

		$path = self::$plugin_dir . '/' . $this->settings_file_name;

		if (is_file($path)) {
			ob_start();
			@$settings = require $path;
			ob_get_clean();
		}

		return (array)$settings;
	}


}
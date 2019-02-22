<?php

namespace WPPluginStart\Plugin;

class Settings
{
	private $settings = [];
	private $options = [];

	public function __construct($settings = null)
	{
		if (is_string($settings)) {
			$this->settings = self::loadFromFile($settings);
		} elseif (is_array($settings)) {
			$this->settings = $settings;
		}

	}

	public function get($key = null, $default = null)
	{
		if ($key !== null) {
			if (isset($this->settings[$key])) {
				return $this->settings[$key];
			} else {
				return $default;
			}
		}

		return $this->settings;
	}

	public function set($key, $value = null)
	{
		return $this->settings[$key] = $value;
	}


	public function option($key = null)
	{
		if ($key !== null) {
			if (!isset($this->options[$key])) {
				$this->options[$key] = get_option($key);
			}
			return $this->options[$key];
		}

		return $this->options;
	}

	public function setOption($key, $value, $autoload = false)
	{
		add_option($key, $value, '', $autoload);
		$this->options[$key] = $value;
	}

	public function delOption($key)
	{
		delete_option($key);
		unset($this->options[$key]);
	}

	public function table($name)
	{
		$tables = $this->get('tables');

		if (isset($tables[$name]['name'])) {
			return $tables[$name]['name'];
		} else {
			return false;
		}
	}

	static function loadFromFile($file_name)
	{

		$settings = [];

		$path = $file_name;

		if (is_file($path)) {
			ob_start();
			@$settings = require $path;
			ob_get_clean();
		}

		return (array)$settings;
	}


}
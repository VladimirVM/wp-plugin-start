<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;

use WPPluginStart\Plugin\AdminPage;
use WPPluginStart\Plugin\Route;
use WPPluginStart\Plugin\Settings;
use WPPluginStart\Plugin\Control;

class Plugin
{

	public function __construct($main_file)
	{
		new Settings($main_file);
		new Control();

		$this->init();
	}

	public function init()
	{
		$routers = Settings::get('routers');

		if (is_array($routers)) {
			new Route($routers);
		}
		
		AdminPage::init();

	}

	static function outJSON($data, $die = true)
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		if ($die) {
			die;
		}
	}

	static function template($names, $find_in_theme = true)
	{
		$template = false;
		if ($find_in_theme) {
			$_names = [];
			foreach ((array)$names as $name) {
				$_names[] = Settings::$plugin_key . '/' . $name;
			}
			$template = locate_template($_names);
		}
		
		if ($template) {
			foreach ((array)$names as $name) {
				$file = Settings::$plugin_template . '/' . $name;
				if (is_file($file)) {
				    $template = $file;
				    break;
				}
			}
		}
		
		return $template;
	}


}

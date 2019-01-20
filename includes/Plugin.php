<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;

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

	}

	static function outJSON($data, $die = true)
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		if ($die) {
			die;
		}
	}


}

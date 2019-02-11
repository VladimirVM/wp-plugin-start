<?php

namespace WPPluginStart;

use WPPluginStart\Plugin\Admin\Page as AdminPage;
use WPPluginStart\Plugin\Route;
use WPPluginStart\Plugin\Settings;
use WPPluginStart\Plugin\Control;
use WPPluginStart\Plugin\Media;

class Plugin
{
	static private $_notice = [];
	

	public function __construct($main_file, $config = [])
	{
		new Settings($main_file, $config);
		new Control();

		$this->init();
	}

	public function init()
	{
		$routers = Settings::get('routers');

		if (is_array($routers)) {
			new Route($routers);
		}


		if (is_admin()) {
			$this->initAdmin();
		} else {
			$this->initFront();
		}

	}

	public function initAdmin()
	{
		AdminPage::init();

		$this->action_links();

		$media = Settings::get('media', []);

		if (!empty($media['admin'])) {
			Media::register($media, 'admin');
			Media::init($media['admin'], 'admin');
		}

		add_action( 'admin_notices', function () {
		    if (!empty(self::$_notice)) {
		        implode('', self::$_notice);
		    }
		});
	}

	public function initFront()
	{
		$media = Settings::get('media', []);

		if (!empty($media['front'])) {
			Media::register($media, 'front');
			Media::init($media['front'], 'front');
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

		if (!$template) {
			foreach ((array)$names as $name) {
				$file = Settings::$plugin_template_dir . '/' . $name;
				
				if (is_file($file)) {
					$template = $file;
					break;
				}
			}
		}

		return $template;
	}

	function action_links()
	{
		add_filter('plugin_action_links_' . Settings::$plugin_basename, function ($links) {
			$items = Settings::get('action_links');

			if (empty($items)) {
				return $links;
			}

			foreach ((array)$items as $key => $item) {
				if (is_numeric($key)) {
					$key = Settings::$plugin_key . '-' . $key;
				}
				if (is_scalar($item)) {
					$links[$key] = $item;
				} else {
					$item = (array)$item;
					if (isset($item['link']) && isset($item['title'])) {
						$target = '';
						if (!empty($item['target'])) {
							$target = ' target="_blank" ';
						}
						$links[$key] = '<a href="' . esc_url($item['link']) . '" ' . $target . '>' . esc_html($item['title']) . '</a>';
					}
				}
			}

			return $links;
		});
	}
	
	static function notice ($message, $type = 'warning', $class = '')
	{
		$class .= ' notice-' . $type;
		self::$_notice[] .= sprintf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
	


}

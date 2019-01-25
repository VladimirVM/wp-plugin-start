<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;

use WPPluginStart\Plugin\Admin\Page as AdminPage;
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

		if (is_admin()) {
			$this->initAdmin();
		} else {
			$this->initFront();
		}

	}

	public function initAdmin()
	{
		AdminPage::init();

		// @todo add check on plugin list
		$this->action_links();
		
		$this->registerMedia(Settings::get('media', []));
	}

	public function initFront()
	{

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

	function registerMedia($items)
	{

		$ver = Settings::$version;

		if (!empty($items['css'])) {
			$css_url = Settings::$plugin_url . '/media/css/';
			foreach ($items['css'] as $key => $css) {
				if (is_string($css)) {
					$css = ['file' => $css];
				}
				$css += ['ver' => $ver, 'deps' => [], 'media' => 'all'];
				if (empty($css['file'])) {
					continue;
				}
				if (!is_numeric($key)) {
					$css['key'] = $key;
				}
				if (empty($css['key'])) {
					$css['key'] = sanitize_key(Settings::$plugin_key . '--' . str_replace('/', '--', $css['file']));
				}
				wp_register_style($css['key'], $css_url . $css['file'], $css['deps'], $css['ver'], $css['media']);
			}
		}

		if (!empty($items['js'])) {
			$js_url = Settings::$plugin_url . '/media/js/';
			foreach ($items['js'] as $key => $js) {
				if (is_string($js)) {
					$js = ['file' => $js];
				}
				$js += ['ver' => $ver, 'deps' => [], 'in_footer' => true];
				if (empty($js['file'])) {
					continue;
				}
				if (!is_numeric($key)) {
					$js['key'] = $key;
				}
				if (empty($js['key'])) {
					$js['key'] = sanitize_key(Settings::$plugin_key . '--' . str_replace('/', '--', $js['file']));
				}
				wp_register_script($js['key'], $js_url . $js['file'], $js['deps'], $js['ver'], $js['in_footer']);
			}
		}


	}


}

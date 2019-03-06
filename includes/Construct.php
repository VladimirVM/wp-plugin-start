<?php
/**
 * Class Construct
 * @uses Build
 *
 * @property string $main_file
 *
 */

namespace WPPluginStart;

use WPPluginStart\Plugin\Admin\Page as AdminPage;
use WPPluginStart\Plugin\Route;
//use WPPluginStart\Plugin\Settings;
use WPPluginStart\Plugin\Control;
use WPPluginStart\Plugin\Media;


class Construct
{
//	static $url = '';
//	static $dir = '';
//	static $key = '';
//	static $main_file = '';
//	static $basename = '';
//	static $template_folder = 'templates';
//	static $media_folder = 'media';
//	static $media_url = '';
//	static $media_dir = '';
//	static $version = 1.0;
//
//	static $settings = null;
//	static $_settings = 'settings.php';

	/**
	 * @var Media
	 */
	private $media = null;

	public function __construct($main_file, $config = [], $settings = null)
	{
		static::$main_file = $main_file;
		static::$dir = dirname($main_file);
		static::$key = basename($main_file, '.php');
		static::$url = rtrim(plugin_dir_url($main_file), '\\/');

		if (!empty($config)) {
			foreach ($config as $key => $value) {
				if (property_exists(get_called_class(), $key)) {
					static::$$key = $value;
				}
			}
		}

		static::dir('template', static::$template_folder);
		static::url('template', static::$template_folder);
		static::dir('media', static::$media_folder);
		static::url('media', static::$media_folder);

		static::$basename = plugin_basename($main_file);

		if ($settings !== null) {
			static::$_settings = $settings;
		} else {
			static::$_settings = static::$dir . '/' . static::$_settings;
		}

		static::$instant = $this;

		// @todo Control

		$this->init();

	}

	public function init()
	{
		$routers = static::settings('routers');

		if (is_array($routers)) {
			new Route($routers);
		}


		if (is_admin()) {
			$this->initAdmin();
		} else {
			$this->initFront();
		}

		Media::init();

	}

	public function initAdmin()
	{

		AdminPage::init($this);

		$this->action_links();

		$media = static::settings('media', []);

		if (!empty($media['admin'])) {
			Media::add($this, $media['admin'], 'admin');
		}

		add_action('admin_notices', function () {
			if (!empty(static::$_notice)) {
				echo implode('', static::$_notice);
			}
		});
	}

	public function initFront()
	{
		$media = static::settings('media', []);

		if (!empty($media['front'])) {
			Media::add($this, $media['front'], 'front');
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
				$_names[] = static::$key . '/' . $name;
			}
			$template = locate_template($_names);
		}

		if (!$template) {
			foreach ((array)$names as $name) {
				$file = static::dir('template') . '/' . $name;

				if (is_file($file)) {
					$template = $file;
					break;
				}
			}
		}

		if (!$template) {
			foreach ((array)$names as $name) {
				$file = WP_PLUGIN_START_DIR . '/templates/' . $name;

				if (is_file($file)) {
					$template = $file;
					break;
				}
			}
		}

		return $template;
	}

	/**
	 * page/admin/%name%/[controller|grid.view|form.view]
	 *
	 * @param string|array $components
	 * @param bool $find_in_theme
	 * @param bool $find_default
	 * @return bool|string
	 */
	static function component($components, $find_in_theme = true, $find_default = true)
	{

		$components = (array)$components;
		$components_template = [];
		if ($find_in_theme) {

			foreach ($components as $component) {
				$components_template = static::$key . '/' . $component . '.php';
			}
			
			$template = locate_template($components_template);
			if ($template) {
				return $template;
			}
		}

//		$template = static::dir('component') . '/' . $components;
		$components_plugin = [];
		$components_defaults = [];
		foreach ($components as $component) {
			$components_plugin[] = static::dir('component') . '/' . $component . '.php';

			if (static::class !== '\\WPPluginStart\\Plugin') {
				$components_plugin[] = Plugin::dir('component') . '/' . $component . '.php';
			}
			
			if (!$find_default) {
			    continue;
			}

			$component_default = explode('/', str_replace('\\', '/', $component), 4);
			$component_default[2] = 'default';
			$component_default = implode('/', $component_default);

			$components_defaults[] = static::dir('component') . '/' . $component_default . '.php';
			if (static::class !== '\\WPPluginStart\\Plugin') {
				$components_defaults[] = Plugin::dir('component') . '/' . $component_default . '.php';
			}
		}
		
		echo '<pre>' . __FILE__ . '(' . __LINE__ . ')';//zzz
		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($components_template, true), 3, 'UTF-8');
		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($components_plugin, true), 3, 'UTF-8');
		echo PHP_EOL . '  = ' . htmlspecialchars(var_export($components_defaults, true), 3, 'UTF-8');
		echo '</pre>';
		
		
		foreach ($components_plugin as $template) {
			if (is_file($template)) {
			    return $template;
			}
		}

		foreach ($components_defaults as $template) {
			if (is_file($template)) {
			    return $template;
			}
		}

		return false;
	}

	function action_links()
	{
		add_filter('plugin_action_links_' . static::$basename, function ($links) {
			$items = static::settings('action_links');

			if (empty($items)) {
				return $links;
			}

			foreach ((array)$items as $key => $item) {
				if (is_numeric($key)) {
					$key = static::$key . '-' . $key;
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


}


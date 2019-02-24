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
//	static $template_url = '';
//	static $template_dir = '';
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
				if (property_exists(__CLASS__, $key)) {
					static::$$key = $value;
				}
			}
		}

		static::$template_dir = static::$dir . '/' . static::$template_folder;
		static::$template_url = static::$url . '/' . static::$template_folder;
		static::$media_dir = static::$dir . '/' . static::$media_folder;
		static::$media_url = static::$url . '/' . static::$media_folder;

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
				$file = static::$template_dir . '/' . $name;

				if (is_file($file)) {
					$template = $file;
					break;
				}
			}
		}

		if (!$template) {
			foreach ((array)$names as $name) {
				$file = __DIR__ . '/../templates/' . $name;

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


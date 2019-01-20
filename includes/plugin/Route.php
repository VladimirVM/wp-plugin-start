<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin;

class Route
{

	/**
	 * @var array [[
	 * 'url' => '/var_1/(.*)', // regexp
	 * 'query' => 'index.php?var_1=$1', // arguments
	 * 'priority' => 'top', // top || bottom
	 * 'vars' => [],
	 * 'check' => function(){}, // callback check is current page
	 * 'controller' => 'file-name',
	 * ]]
	 */
	var $items = [];
	var $default_priority = 'top';
	private $plugin_dir = '';
	var $plugin_controller_dir = '/controllers';
	var $theme_template_dir = '';

	function __construct($items = null)
	{
		$this->plugin_dir = Settings::$plugin_dir;
		$this->addItems($items);
		add_action('init', [$this, 'add_rewrite_rule'], 10, 0);
		add_filter('query_vars', [$this, 'query_vars']);
		add_action('template_include', [$this, 'controller_include'], 10, 1);
	}

	function add_rewrite_rule()
	{
		if (!empty($this->items) and is_array($this->items)) {
			foreach ($this->items as $item) {
				$priority = isset($item['priority']) ? $item['priority'] : $this->default_priority;
				add_rewrite_rule($item['url'], $item['query'], $priority);
			}
			flush_rewrite_rules();
		}
	}

	function controller_include($template)
	{
		$controller = false;
		if (!empty($this->items) and is_array($this->items)) {
			foreach ($this->items as $item) {
				if (empty($item['check']) || empty($item['controller'])) {
					continue;
				}
				if (is_callable($item['check'])) {
					if (call_user_func($item['check'], $item)) {
						$controller = $this->getController($item['controller']);
					}
				} else {
					$controller = $this->getController($item['controller']);
				}


				if ($controller) {
					return $controller;
				}
			}
		}

		return $template;
	}

	function query_vars($vars)
	{
		if (!empty($this->items) and is_array($this->items)) {
			foreach ($this->items as $item) {
				if (!empty($item['vars'])) {
					$vars = array_merge($vars, (array)$item['vars']);
				}
			}
		}
		return $vars;
	}

	function addItems($items = null)
	{
		if (!empty($items)) {
			$this->items = $items + $this->items;
		}
	}

	function getController($file_name)
	{
		$controller = $this->plugin_dir . $this->plugin_controller_dir . '/' . $file_name;

		if (is_file($controller)) {
			return $controller;
		}
		return false;
	}

	static function generateItem($route)
	{
		$slug = null;
		if (is_string($route)) {
			$slug = $route;
			$route = [];
		} else {
			$route = (array)$route;
			if (!empty($route['slug'])) {
				$slug = $route['slug'];
			} elseif (!empty($route['url'])) {
				$slug = $route['url'];
			}
		}

		if (empty($slug)) {
			return false;
		}

		if (empty($route['slug'])) {
			$route['slug'] = $slug;
		}

		if (empty($route['url'])) {
			$route['url'] = '^' . $slug . '$';
		}

		if (empty($route['query'])) {
			$route['query'] = 'index.php?' . $slug . '=$matches[1]';
		}

		if (empty($route['vars'])) {
			$route['vars'] = [$slug];
		}

		if (empty($route['check'])) {
			$route['check'] = function ($item) {
				return (get_query_var($item['slug'], null) !== null);
			};
		}

		if (empty($route['controller'])) {
			$route['controller'] = $slug . '.php';
		}

//		$route = [
//			'url' => '^route-url(.*)', // regexp
//			'query' => 'index.php?route-url=$matches[1]', // arguments
//			'priority' => 'top', // top || bottom
//			'vars' => ['route-url'],
//			'check' => function () {}, // callback check is current page
//			'controller' => 'route-url.php',
//		];


		return $route;
	}


}
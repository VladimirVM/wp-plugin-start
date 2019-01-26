<?php

namespace WPPluginStart\Plugin;

class Media
{


	static function register($items)
	{

		$ver = Settings::$version;

		if (!empty($items['css'])) {
			$css_url = Settings::$plugin_media_url . '/css/';
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
			$js_url = Settings::$plugin_media_url . '/js/';
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
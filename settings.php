<?php
/**
 * Plugin Settings
 */

use \WPPluginStart\Plugin\Settings;
use \WPPluginStart\Plugin\Media;
use \WPPluginStart\Plugin\Admin\Page as AdminPage;


$settings = [
	'tables' => [
		/*
		'table_key' => [
			'name' => 'table_name',
			'fields' => '`id` int(11) NOT NULL AUTO_INCREMENT,
						`title` varchar(255) NOT NULL,
						`description` text,
						 PRIMARY KEY (id)'
		],
		*/
	],
	'options' => [
//        'plugin_option_name' => 'plugin_option_value',
	],
	'routers' => [
//        'front_end_routs' => WPPluginStart\Plugin\Route::generateItem('front_end_routs_path'),
	],
	'action_links' => [
		'Some html Data',
		['link' => '#link_to', 'title' => 'Settings', 'target' => true]
	],
	'media' => [
		'js' => [
			'admin.js' => 'admin.js',
			['file' => 'front.js', 'key' => (Settings::$plugin_key . '-front-js'), 'deps' => ['jquery'], 'ver' => 1, 'footer' => true]
		],
		'css' => [
			'admin.css' => 'admin.css',
			['file' => 'front.css', 'key' => Media::key('front.css'), 'deps' => ['jquery'], 'ver' => 1, 'media' => 'all']
		],
		'admin' => [
			'js' => [Media::key('admin.js')],
			'css' => [Media::key('admin.css')],
		],
		'front' => [],
	],
	'pages' => [
		AdminPage::generateItem([
			'menu' => 'Plugin\Admin\Page',
			'slug' => 'plugin_settings',
			'blocks' => [
				'settings_form'
			],
			'css' => [],
			'js' => [],
		]),
		AdminPage::generateItem('Plugin\Admin\Page 2'),
		AdminPage::generateItem('Plugin\Admin\Page 2 sub', 'Plugin\Admin\Page 2'),
		AdminPage::generateItem('Plugin\Admin\Page tools.php sub', 'tools.php'),
	],
	'blocks' => [
		'settings_form' => [
			'type' => 'form',
			'form' => [
				[
					'field' => ['type' => 'text', 'name' => 'field_name'],
					'label' => 'Title'
				]
			]
		]
	],
];

return $settings;

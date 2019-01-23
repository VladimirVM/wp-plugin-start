<?php
/**
 * Plugin Settings
 * Create: Vladimir
 */

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
		'admin' => [
			'js' => [],
			'css' => [],
		],
		'front' => [
			'js' => [],
			'css' => [],
		],
	],
	'pages' => [
		WPPluginStart\Plugin\Admin\Page::generateItem([
			'menu' => 'Plugin\Admin\Page',
			'slug' => 'plugin_settings',
			'blocks' => [
				'settings_form'
			],
		]),
		WPPluginStart\Plugin\Admin\Page::generateItem('Plugin\Admin\Page 2'),
		WPPluginStart\Plugin\Admin\Page::generateItem('Plugin\Admin\Page 2 sub', 'Plugin\Admin\Page 2'),
		WPPluginStart\Plugin\Admin\Page::generateItem('Plugin\Admin\Page tools.php sub', 'tools.php'),
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

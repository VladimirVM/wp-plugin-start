<?php

/**
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
	'pages' => [
		WPPluginStart\Plugin\AdminPage::generateItem([
			'menu' => 'Plugin Settings',
			'key' => 'plugin_settings',
			'blocks' => [
				'settings_form'
			],
		]),
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

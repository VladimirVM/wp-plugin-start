<?php
/**
 * Plugin Settings
 */

use \WPPluginStart\Plugin\Settings;
use \WPPluginStart\Plugin\Media;
use \WPPluginStart\Plugin\Admin\Page as AdminPage;


$settings = [
//	'some_data' => __DIR__,
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
//		'Some html Data',
//		['link' => '#link_to', 'title' => 'Settings', 'target' => true]
	],
	'media' => [
//		'js' => [
//			'admin.js' => 'admin.js',
//			['file' => 'front.js', 'key' => (\WPPluginStart\Plugin::$key . '-front-js'), 'deps' => ['jquery'], 'ver' => 1, 'footer' => true]
//		],
////		'css' => [
////			'admin.css' => 'admin.css',
////			['file' => 'front.css', 'key' => Media::key('front.css'), 'deps' => ['jquery'], 'ver' => 1, 'media' => 'all']
////		],
//		'admin' => [
//			'css' => [],
//			'js' => [],
//		],
//		'front' => [],
	],
	'pages' => [
//		AdminPage::generateItem([
//			'menu' => 'Plugin\Admin\Page',
//			'page' => 'Title page',
//			'slug' => 'plugin_settings',
//			'blocks' => [
//				'main_option'
//			],
//			'js' => [Media::key('admin.js')],
//			'css' => [Media::key('admin.css')],
//		]),
//		AdminPage::generateItem('Plugin\Admin\Page 2'),
//		AdminPage::generateItem('Plugin\Admin\Page 2 sub', 'Plugin\Admin\Page 2'),
//		AdminPage::generateItem('Plugin\Admin\Page tools.php sub', 'tools.php'),
	],
	'blocks' => [
//		'main_option' => [
//			'type' => 'option',
//			'title' => __('Plugin settings'),
//			'fields' => [
//				[
//					'type' => 'text',
//					'name' => 'field_name',
//					'label' => 'Title',
//					'description' => 'Some information about it field',
//				],
//				[
//					'tag' => 'textarea',
//					'name' => 'field_name_2',
//					'label' => 'Description'
//				],
//				[
//					'type' => 'checkbox',
//					'name' => 'field_name_checkbox',
//					'label' => 'Single checkbox',
//					'attr' => [
//						'value' => 'checkbox',
//					]
//				],
//				[
//					'type' => 'checkbox',
//					'name' => 'field_name_3',
//					'label' => 'Check Me',
//					'items' => [
//						'key_1' => 'Value 1',
//						'key_2' => [
//							'value' => 'value_2',
//							'label' => 'Label 2',
//						],
//						'key_3' => 'value_3:Item : 2',
//					],
//
//				],
//				[
//					'tag' => 'select',
//					'name' => 'field_name_select',
//					'label' => 'Select Me',
//					'items' => [
//						'' => 'Choose Value',
//						'key_1' => 'Select 1',
//						'key_2' => 'Select 2',
//						'key_3' => 'Select 3',
//					],
//
//				],
//				[
//					'tag' => 'select',
//					'name' => 'field_name_select_multiple',
//					'label' => 'Select Me',
//					'items' => [
//						'' => 'Choose Value',
//						'key_1' => 'Select 1',
//						'key_2' => 'Select 2',
//						'key_3' => 'Select 3',
//					],
//					'attr' => ['multiple' => true]
//				],
//			],
//		]
	],
];

return $settings;

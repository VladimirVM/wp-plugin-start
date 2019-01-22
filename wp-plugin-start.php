<?php
/**
 * Plugin Name: WP Plugin Start
 * Author: Vladimir
 * Description: API for create new plugin
 * Version: 1.0
 */

include 'includes/autoloader.php';

WPPluginStart\autoloader([
//    'ClassName' => __DIR__ . '/path/to/file'
]);

new WPPluginStart\Plugin(__FILE__);




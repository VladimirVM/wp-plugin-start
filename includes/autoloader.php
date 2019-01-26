<?php
namespace WPPluginStart;

/**
 * @param array $load = [
 * 	'Class_Name' => 'path'
 * ]
 * @throws \Exception
 */
function autoloader ($load = [])
{
	try {
		spl_autoload_register(function ($class_name) use ($load) {
			
			if (!empty($load) and isset($load[$class_name])) {
				include_once $load[$class_name];
				return;
			}
			
			$info = explode('\\', $class_name);
			
			if ($info[0] === 'WPPluginStart') {
				array_shift($info);
				$file = array_pop($info);
				include_once __DIR__ . '/' . strtolower(implode('/', $info)) . '/' . $file . '.php';
				
			}

		});

	} catch (\Exception $e) {

	}
}


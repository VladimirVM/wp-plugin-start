<?php

namespace WPPluginStart\Plugin\DB;
use WPPluginStart\Plugin\Settings;

class Table
{

	public static function removeAll()
	{
		$tables = Settings::option('tables');
		foreach ($tables as $table => $settings) {
			self::remove($table);
		}
		return true;
	}

	public static function remove($table_name)
	{
		global $wpdb;
		$sql = 'DROP TABLE IF EXISTS `' . $table_name . '` ';
		$wpdb->query($sql);
	}

	public static function createAll()
	{
		global $wpdb;

		$charset_collate = '';
		if (!empty($wpdb->charset)) {
			$charset_collate = ' DEFAULT CHARACTER SET ' . $wpdb->charset;
		}
		if (!empty($wpdb->collate)) {
			$charset_collate .= ' COLLATE ' . $wpdb->collate;
		}

		$out = [];

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$tables = Settings::get('tables');
		foreach ($tables as $key => $value) {

			$out[] = self::create($value, $charset_collate);
		}

		return $out;
	}

	public static function create($table, $charset_collate = '')
	{

		$sql = 'CREATE TABLE `' . $table['title'] . ' ` (
                    ' . $table['fields'] . '
                  ) ' . $charset_collate . ';';

		return dbDelta($sql);
	}

}

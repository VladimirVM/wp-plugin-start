<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart;


class Helper
{
	static function urlQuery($query_add = [], $query_remove = [], $url = null)
	{
		$url_parts = [];
		$query = [];
		$query_url_parts = [];

		if ($url !== null) {
			if (is_string($url)) {
				$url_parts = explode('?', $url, 2);
				if (!empty($url_parts[1])) {
					$query_url_parts = explode('#', $url_parts[1], 2);

					parse_str($query_url_parts[0], $query);
				}
			} elseif (is_array($url)) {
				$query = $url;
			}
		}

		if (!empty($query)) {
			$query = array_diff_key($query, $query_remove);
		}

		if (!empty($query)) {
			$query = array_merge($query, $query_add);
		} else {
			$query = $query_add;
		}

		$out = ($url_parts[0] ?? '');

		if (!empty($query)) {
			$out .= '?' . http_build_query($query);
		}

		if (!empty($query_url_parts[1])) {
			$out .= '#' . $query_url_parts[1];
		}

		return $out;
	}
	
	static function pageRefresh ($exit = true)
	{
		if (headers_sent()) {
		    echo '<script>window.location.reload(true);</script>';
		} else {
			header("Refresh:0");
		}
		if ($exit) {
		    exit;
		}
	}
	

}
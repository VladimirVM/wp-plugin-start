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

	static function crateMedia($name, $local = false, $post = [], $allow_ext = ['jpg', 'png', 'gif', 'jpeg'])
	{

		$out = null;

		$wordpress_upload_dir = wp_upload_dir();
		$i = 0; // number of tries when the file with the same name is already exists

		if ($local === true) {
			$file = [
				'path' => $name,
				'tmp_name' => null,
				'name' => basename($name),
				'size' => filesize($name),
				'error' => 0,
			];
		} else {
			$file = $_FILES[$name];
		}

		if (empty($file)) {
			return ('File is not selected.');
		}
		if ($file['error']) {
			return ($file['error']);
		}
		if ($file['size'] > wp_max_upload_size()) {
			return ('It is too large than expected.');
		}

		$new_file_path = $wordpress_upload_dir['path'] . '/' . $file['name'];
		$new_file_mime = mime_content_type($file['tmp_name'] ?? $file['path']);

		$info = pathinfo($new_file_path);

		if (!(is_string($allow_ext) && $allow_ext === 'all')) {
			$allow_ext = array_flip((array)$allow_ext);
			if (!isset($allow_ext[$info['extension']])) {
				return 'Doesn\'t allow this extension of uploads.';
			}
		}

		if (!in_array($new_file_mime, get_allowed_mime_types())) {
			return ('WordPress doesn\'t allow this type of uploads.');
		}
		while (is_file($new_file_path)) {
			$i++;
			$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $file['name'];
		}

		if ($local === true) {
			$upload = copy($file['path'], $new_file_path);
		} else {
			$upload = move_uploaded_file($file['tmp_name'], $new_file_path);
		}

		if ($upload) {


			$post = $post + [
					'guid' => $new_file_path,
					'post_mime_type' => $new_file_mime,
					'post_status' => 'inherit',
					'post_title' => '{name}',
					'post_content' => '',
				];

			$post['post_title'] = str_replace(
				['{name}', '{ext}'],
				[$info['filename'], $info['extension']],
				$post['post_title']
			);
			$post['post_content'] = str_replace(
				['{name}', '{ext}'],
				[$info['filename'], $info['extension']],
				$post['post_content']
			);

			$out = wp_insert_attachment($post, $new_file_path);

			// wp_generate_attachment_metadata() won't work if you do not include this file
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// Generate and save the attachment metas into the database
			wp_update_attachment_metadata($out, wp_generate_attachment_metadata($out, $new_file_path));

			// Show the uploaded file in browser
//			wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );

		}

		return $out;
	}


}
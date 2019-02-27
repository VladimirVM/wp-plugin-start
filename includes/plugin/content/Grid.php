<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin\Content;

use \WPPluginStart\Plugin\Admin\Field;

class Grid
{

	private $_data = [];
	private $_structure = [];
	private $row_i = 0;

	function __construct($structure, $data = [])
	{
		$this->_structure = $structure;
		$this->_data = $data;

	}

	function head($attr = [])
	{
		$out = '<tr>';

		foreach ($this->_structure as $i => $item) {
			$class = 'head-col-' . $i;
			if (!empty($item['key']) && is_string($item['key'])) {
				$class .= ' head-col-key-' . ($item['key']);
			} elseif (!empty($item['col']) && is_string($item['col'])) {
				$class .= ' head-col-name-' . ($item['col']);
			}

			$th = new Field('th', ['class' => $class], $item['title'] ?? '');
			$out .= $th->render();
		}

		$out .= '</tr>';

		return $out;
	}


	function body()
	{
		$out = '';

		foreach ($this->_data as $row_i => $data) {
//			$out = '<tr>';
			$row = '';
			foreach ($this->_structure as $col_i => $item) {
				if (empty($item['col'])) {
					continue;
				}
				$attr = [];
				$content = '';

				$class = 'body-col-' . $col_i;
				if (!empty($item['key']) && is_string($item['key'])) {
					$class .= ' body-col-key-' . ($item['key']);
				} elseif (!empty($item['col']) && is_string($item['col'])) {
					$class .= ' body-col-name-' . ($item['col']);
				}
				$attr['class'] = $class;


				if (!empty($item['col'])) {
					if (is_callable($item['col'])) {
						$content = call_user_func($item['col'], $data, $item, $this, $row_i, $col_i);
					} elseif (is_scalar($item['col'])) {
						if (isset($data[$item['col']])) {
						    $content = $data[$item['col']];
						} else {
							$content = $item['col'];
						}
						
//						$content = ' ' . $content;
					}
				}

				$row .= (new Field('td', $attr, $content))->render();
				
			}
			$out .= (new Field('tr', ['class' => 'row-' . $row_i], $row))->render();
		}

		
		return $out;
	}


}
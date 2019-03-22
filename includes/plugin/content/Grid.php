<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin\Content;

use WPPluginStart\Helper;
use \WPPluginStart\Plugin\Content\Field;

class Grid
{

	private $data = [];
	private $structure = [];

	function __construct($structure, $data = [])
	{
		$this->structure = $structure;
		$this->data = $data;

		if (empty($this->structure['cols'])) {
			$this->structure['cols'] = [];
		}
		if (empty($this->structure['filters'])) {
			$this->structure['filters'] = [];
		}

	}

	function head($attr = [], $filter = true)
	{
		$out = '';

		foreach ($this->structure['cols'] as $i => $item) {
			$class = 'head-col-' . $i;
			if (!empty($item['key']) && is_string($item['key'])) {
				$class .= ' head-col-key-' . ($item['key']);
			} elseif (!empty($item['col']) && is_string($item['col'])) {
				$class .= ' head-col-name-' . ($item['col']);
			}

			$th = new Field('th', ['class' => $class], $item['title'] ?? '');
			$out .= $th->render();
		}

		$out = (new Field('tr', $attr, $out))->render();

		return $out;
	}

	function filters($attr = [])
	{
		$out = '<form method="get">';
		
		if (!empty($_GET)) {
			$data = $_GET;
			unset($data['filter']);
			foreach ($data as $name => $value) {
				$out .= (new Field('input', ['type' => 'hidden', 'name' => $name, 'value' => $value]))->render();
			}
		}
		
		$out .= '<div style="clear: both">';

		foreach ($this->structure['filters'] as $item) {

			$col = '<label class="grid-filter alignleft">';

			if (!empty($item['label'])) {
				$col .= '<div class="label">' . $item['label'] . '</div>';
			}

			if (!empty($item['name'])) {
				$item['name'] = (array)$item['name'];
				array_unshift($item['name'], 'filter');
			}

			$col .= '<div class="grid-filter-item">' . Field::build($item, $_GET ?? [], []) . '</div>';

			$col .= '</label>';


			$out .= $col;

		}
		$out .= '</div>';

		$out .= '<p class="grid-filter-action" style="clear: both; text-align: right">';
		$out .= (new Field('button', ['type' => 'submit'], 'Filtered'))->render();
		$out .= '</p>';

		$out .= '</form>';

		return $out;
	}


	function body()
	{
		$out = '';
		foreach ($this->data->toArray()['data'] as $row_i => $data) {
			$row = '';
			foreach ($this->structure['cols'] as $col_i => $item) {
				if (empty($item['col'])) {
					continue;
				}
				$attr = $item['attr'] ?? [];
				$content = '';

				if (empty($attr['class'])) {
					$attr['class'] = [];
				}
				$attr['class'] = (array)$attr['class'];

				$attr['class'][] = 'body-col-' . $col_i;
				if (!empty($item['key']) && is_string($item['key'])) {
					$attr['class'][] = ' body-col-key-' . ($item['key']);
				} elseif (!empty($item['col']) && is_string($item['col'])) {
					$attr['class'][] = ' body-col-name-' . ($item['col']);
				}

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

	function paginate()
	{
		$name = $this->data->getOptions()['pageName'];
		$page_id = $this->data->currentPage();
		for ($i = 1, $count = $this->data->lastPage() + 1; $i < $count; $i++) {
			$class = 'button' . ($i === $page_id ? ' disabled' : '');
			?>
			<a href="<?php echo \WPPluginStart\Helper::urlQuery([$name => $i], [], $_GET ?? []); ?>"
			   class="<?php echo $class; ?>"><?php echo $i; ?></a>
			<?php
		}
	}


}
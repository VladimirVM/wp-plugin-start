<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin\Content;

use \WPPluginStart\Plugin\Admin\Field;

class Grid
{

	private $data = [];
	private $structure = [];

	function __construct($structure, $data = [])
	{
		$this->structure = $structure;
		$this->data = $data;

	}

	function head($attr = [])
	{
		$out = '<tr>';

		foreach ($this->structure as $i => $item) {
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
		foreach ($this->data->toArray()['data'] as $row_i => $data) {
//			$out = '<tr>';
			$row = '';
			foreach ($this->structure as $col_i => $item) {
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
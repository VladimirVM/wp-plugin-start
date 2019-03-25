<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\Plugin\Content;

use WPPluginStart\Helper;
use \WPPluginStart\Plugin\Content\Field;

class Grid
{

	private $data = null;
	private $query = [];
	private $structure = [];
	private $page_name_key = 'paged';
	private $items_per_page = 10;
	private $page_id = 1;

	function __construct($structure, $query, $items_per_page = null, $page_name_key = null, $page_id = null)
	{
		$this->structure = $structure;

		if ($page_name_key !== null) {
			$this->page_name_key = $page_name_key;
		}
		if ($items_per_page !== null) {
			$this->items_per_page = $items_per_page;
		}
		if ($page_id !== null) {
			$this->page_id = $page_id;
		}

		if (!empty($structure['filters'])) {
			foreach ($structure['filters'] as $filter) {
				if (isset($filter['filtered']) && $filter['filtered']) {
					$filter['filtered']($query, $_GET ?? []);
				}
			}
		}

		$this->query = $query;

		$this->page_id = filter_input(INPUT_GET, $this->page_name_key, FILTER_SANITIZE_NUMBER_INT) ?? 1;

//		$this->data = $data->paginate(10, ['*'], $this->page_name_key, $page_id);

		if (empty($this->structure['cols'])) {
			$this->structure['cols'] = [];
		}
		if (empty($this->structure['filters'])) {
			$this->structure['filters'] = [];
		}

	}

	function getData()
	{
		if ($this->data) {
			return $this->data;
		}

		$this->data = $this->query->paginate($this->items_per_page, ['*'], $this->page_name_key, $this->page_id);

		return $this->data;
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

		$out .= '<div class="tablenav">';
		$out .= '<div class="alignleft actions">';

		foreach ($this->structure['filters'] as $item) {

			$item['attr'] = $item['attr'] ?? [];
			$col = '<label class="grid-filter">';

			if (!empty($item['label'])) {
				if (!isset($item['attr']['placeholder'])) {
					$item['attr']['placeholder'] = $item['label'];
				}
			}

			if (!empty($item['name'])) {
				$item['name'] = (array)$item['name'];
				array_unshift($item['name'], 'filter');
			}


			$col .= Field::build($item, $_GET ?? [], []);

			$col .= '</label>';


			$out .= $col;

		}

		$out .= (new Field('button', ['type' => 'submit', 'class' => 'button'], 'Filter'))->render();

		$out .= '</div>';
		$out .= '</div>';

		$out .= '</form>';

		return $out;
	}


	function body()
	{
		$out = '';


		foreach ($this->getData()->toArray()['data'] as $row_i => $data) {
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
		$data = $this->getData();

		$name = $data->getOptions()['pageName'];
		$page_id = $data->currentPage();
		for ($i = 1, $count = $data->lastPage() + 1; $i < $count; $i++) {
			$class = 'button' . ($i === $page_id ? ' disabled' : '');
			?>
			<a href="<?php echo \WPPluginStart\Helper::urlQuery([$name => $i], [], $_GET ?? []); ?>"
			   class="<?php echo $class; ?>"><?php echo $i; ?></a>
			<?php
		}
	}


	/**
	 * @return string|null
	 */
	public function getPageNameKey(): ?string
	{
		return $this->page_name_key;
	}

	/**
	 * @param string|null $page_name_key
	 */
	public function setPageNameKey(?string $page_name_key): void
	{
		$this->page_name_key = $page_name_key;
	}

	/**
	 * @return int|null
	 */
	public function getItemsPerPage(): ?int
	{
		return $this->items_per_page;
	}

	/**
	 * @param int|null $items_per_page
	 */
	public function setItemsPerPage(?int $items_per_page): void
	{
		$this->items_per_page = $items_per_page;
	}

	/**
	 * @return int|mixed
	 */
	public function getPageId()
	{
		return $this->page_id;
	}

	/**
	 * @param int|mixed $page_id
	 */
	public function setPageId($page_id): void
	{
		$this->page_id = $page_id;
	}
}
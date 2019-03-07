<?php
/**
 * Create: Vladimir
 */

namespace WPPluginStart\plugin\helpers;

class ComponentController
{

	public $action = 'list';
	public $key = '';
	/**
	 * @var \WPPluginStart\Plugin\Admin\Page
	 */
	public $page;

	/**
	 *  constructor.
	 * @param $page \WPPluginStart\Plugin\Admin\Page
	 */
	public function __construct($page, $key)
	{
		$this->page = $page;
		$this->key = $key;

		$this->annaliseRequest();
		$this->addView();
		$this->control();
	}

	public function annaliseRequest()
	{
		if (!empty($_GET['crud-action'])) {
			$this->action = basename($_GET['crud-action'], '.php');
		}

	}

	public function addView()
	{
		array_unshift($this->page->views, 'admin/page/' . $this->key . '/' . $this->action . '.view');
	}

	public function control()
	{
		/*
		 * call class method prepare%Action%()
		 */

		$method = 'prepare' . ucfirst($this->action);
		if (method_exists($this, $method)) {
			call_user_func([$this, $method]);
		}

	}

}
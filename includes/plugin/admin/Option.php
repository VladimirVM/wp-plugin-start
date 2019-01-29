<?php

namespace WPPluginStart\Plugin\Admin;


use WPPluginStart\Plugin\Settings;

class Option
{

	static $page_slug;
	static $page_blocks;
	static $field_type = [
		'text' => [__CLASS__, 'fieldTextRender']
	];

	public $key = '';
	public $title = '';
	public $args = [];
	public $fields = [];
	public $description = '';


	function __construct($option_key, $settings = [])
	{
		$this->key = $option_key;

		foreach ($settings as $key => $value) {
			$this->{$key} = $value;
		}

		$this->prepare();
		$this->register();
	}

	function prepare()
	{

	}

	function register()
	{

		register_setting(self::$page_slug, Settings::$plugin_key, $this->args);

		$section_key = $this->key . '_section';

		add_settings_section(
			$section_key,
			$this->title,
			[$this, 'sectionRented'],
			self::$page_slug
		);
		// @bug ERROR: options page not found.

		if (!empty($this->fields)) {
			foreach ($this->fields as $field) {
				// @todo check all
				$id = Settings::$plugin_key . '_' . $field['name'];

				add_settings_field(
					$id,
					$field['label'],
					self::$field_type[$field['type']],
					self::$page_slug,
					$section_key,
					[
						'label_for' => $id,
						'field' => $field,
						'id' => $id,
						'section' => $section_key, 'this' => $this
					]
				);

//				register_setting(self::$page_slug, $id);
			}
		}

	}

	static function fieldTextRender($data)
	{
		$options = get_option(Settings::$plugin_key);
		?>
		<input type='text' name='<?php echo Settings::$plugin_key; ?>[<?php echo $data['field']['name']; ?>]'
		       value='<?php echo isset($options[$data['field']['name']])??$options[$data['field']['name']]; ?>'>
		<?php

	}

	function sectionRented()
	{

		echo $this->description;

	}

	function render()
	{
		settings_fields(self::$page_slug);
		do_settings_sections(self::$page_slug);
		submit_button();
	}

	static function renderOnPage()
	{
		settings_fields(self::$page_slug);
		do_settings_sections(self::$page_slug);
		submit_button();
	}

	static function init($slug = '', $blocks = [])
	{
		self::$page_slug = $slug;
		self::$page_blocks = array_combine($blocks, $blocks);
		add_action('admin_init', [__CLASS__, 'load']);
	}

	static function load()
	{
		$blocks = Settings::get('blocks', []);


		$blocks = array_filter($blocks, function ($value, $key) {
			return (isset($value['type']) && $value['type'] === 'option' && self::$page_blocks[$key]);
		}, ARRAY_FILTER_USE_BOTH);


		foreach ($blocks as $key => $block) {
			new self($key, $block);
		}
	}

}
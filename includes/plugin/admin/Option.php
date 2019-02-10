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
	public $name = null;
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
		if ($this->name === null) {
		    $this->name = Settings::$plugin_key . '_' . $this->key;
		}
	}

	function register()
	{
		if ($this->name) {
			register_setting(self::$page_slug, $this->name, $this->args);
		}

		$section_key = $this->key . '_section';

		add_settings_section(
			$section_key,
			$this->title,
			[$this, 'sectionRented'],
			self::$page_slug
		);

		if (!empty($this->fields)) {
			foreach ($this->fields as $field) {

				$id = Settings::$plugin_key . '_' . $field['name'];

				Field::init(self::$page_slug, $section_key, [
					'section' => $this,
					'field' => $field,
					'id' => $id,
				]);
			}
		}

	}

	static function fieldTextRender($data)
	{
		$section_name = $data['section']->name;
		$name = '';
		
		if ($section_name) {
			$options = get_option($section_name);
			$value = '';
			if (isset($options[$data['field']['name']])) {
				$value = $options[$data['field']['name']];
			}
			$name = $section_name . '[' . $data['field']['name'] . ']';
		} else {
			$name = $data['field']['name'];
			$value = get_option($name, null);
		}

		?>
		<input type='text' name='<?php echo esc_attr($name); ?>'
		       value='<?php echo esc_attr($value); ?>'>
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
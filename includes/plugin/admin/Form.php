<?php
namespace WPPluginStart\Plugin\Admin;


class Form
{


	function __construct()
	{
//		add_action('admin_menu', [$this, 'wp_plugin_start_prefix_add_admin_menu']);
//		add_action('admin_init', [$this, 'wp_plugin_start_prefix_settings_init']);
	}

	function wp_plugin_start_prefix_add_admin_menu()
	{

		add_submenu_page(
			'tools.php',
			'plugin_theme_name',
			'plugin_theme_name',
			'manage_options',
			'plugin_theme_name',
			[$this, 'wp_plugin_start_prefix_options_page']
		);

	}

	function wp_plugin_start_prefix_settings_init()
	{

		register_setting('pluginPage', 'wp_plugin_start_prefix_settings');

		add_settings_section(
			'wp_plugin_start_prefix_pluginPage_section',
			__('Your section description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_settings_section_callback'],
			'pluginPage'
		);

		add_settings_field(
			'wp_plugin_start_prefix_text_field_0',
			__('Settings field description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_text_field_0_render'],
			'pluginPage',
			'wp_plugin_start_prefix_pluginPage_section'
		);

		add_settings_field(
			'wp_plugin_start_prefix_checkbox_field_1',
			__('Settings field description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_checkbox_field_1_render'],
			'pluginPage',
			'wp_plugin_start_prefix_pluginPage_section'
		);

		add_settings_field(
			'wp_plugin_start_prefix_radio_field_2',
			__('Settings field description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_radio_field_2_render'],
			'pluginPage',
			'wp_plugin_start_prefix_pluginPage_section'
		);

		add_settings_field(
			'wp_plugin_start_prefix_textarea_field_3',
			__('Settings field description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_textarea_field_3_render'],
			'pluginPage',
			'wp_plugin_start_prefix_pluginPage_section'
		);

		add_settings_field(
			'wp_plugin_start_prefix_select_field_4',
			__('Settings field description', 'wp_plugin_satrt_text'),
			[$this, 'wp_plugin_start_prefix_select_field_4_render'],
			'pluginPage',
			'wp_plugin_start_prefix_pluginPage_section'
		);


	}

	function wp_plugin_start_prefix_text_field_0_render()
	{

		$options = get_option('wp_plugin_start_prefix_settings');
		?>
		<input type='text' name='wp_plugin_start_prefix_settings[wp_plugin_start_prefix_text_field_0]'
		       value='<?php echo $options['wp_plugin_start_prefix_text_field_0']; ?>'>
		<?php

	}

	function wp_plugin_start_prefix_checkbox_field_1_render()
	{

		$options = get_option('wp_plugin_start_prefix_settings');
		?>
		<input type='checkbox'
		       name='wp_plugin_start_prefix_settings[wp_plugin_start_prefix_checkbox_field_1]' <?php checked($options['wp_plugin_start_prefix_checkbox_field_1'], 1); ?>
		       value='1'>
		<?php

	}

	function wp_plugin_start_prefix_radio_field_2_render()
	{

		$options = get_option('wp_plugin_start_prefix_settings');
		?>
		<input type='radio'
		       name='wp_plugin_start_prefix_settings[wp_plugin_start_prefix_radio_field_2]' <?php checked($options['wp_plugin_start_prefix_radio_field_2'], 1); ?>
		       value='1'>
		<?php

	}

	function wp_plugin_start_prefix_textarea_field_3_render()
	{

		$options = get_option('wp_plugin_start_prefix_settings');
		?>
		<textarea cols='40' rows='5' name='wp_plugin_start_prefix_settings[wp_plugin_start_prefix_textarea_field_3]'> 
		<?php echo $options['wp_plugin_start_prefix_textarea_field_3']; ?>
 	</textarea>
		<?php

	}

	function wp_plugin_start_prefix_select_field_4_render()
	{

		$options = get_option('wp_plugin_start_prefix_settings');
		?>
		<select name='wp_plugin_start_prefix_settings[wp_plugin_start_prefix_select_field_4]'>
			<option value='1' <?php selected($options['wp_plugin_start_prefix_select_field_4'], 1); ?>>Option 1</option>
			<option value='2' <?php selected($options['wp_plugin_start_prefix_select_field_4'], 2); ?>>Option 2</option>
		</select>

		<?php

	}

	function wp_plugin_start_prefix_settings_section_callback()
	{

		echo __('This section description', 'wp_plugin_satrt_text');

	}

	function wp_plugin_start_prefix_options_page()
	{

		?>
		<form action='options.php' method='post'>

			<h2>plugin_theme_name</h2>

			<?php
			settings_fields('pluginPage');
			do_settings_sections('pluginPage');
			submit_button();
			?>

		</form>
		<?php

	}


}
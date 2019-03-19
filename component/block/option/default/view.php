<?php
/**
 * @var $this \WPPluginStart\Plugin\Admin\Option
 */

?>
<form action="options.php?wp-plugin-start[option-uid]=<?php echo $this->uid; ?>" method="post">
	<?php
	settings_fields($this->uid);
	do_settings_sections($this->uid);
	submit_button();
	?>
</form>
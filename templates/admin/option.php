<?php
/**
 * @var $this \WPPluginStart\Plugin\Admin\Option
 */

?>
<form action="options.php" method="post">
<?php
settings_fields($this->uid);
do_settings_sections($this->uid);
submit_button();
?>
</form>
<?php
/**
 * Create: Vladimir
 *
 * @var array $attr wrapper
 * @var array $data - field addition information
 *
 */

use \WPPluginStart\Plugin\Content\Field;

?>
<div <?php echo Field::attr($attr); ?>>
	<div>
		<div class="field-wrap"><input type="text" class="js-auto-complete-field-text"/></div>
		<ul class="js-list-items">
			<?php echo $list_items; ?>
		</ul>
	</div>
	<?php if (isset($data['field']['description'])) { ?>
		<p class="description"><?php echo $data['field']['description']; ?></p>
	<?php } ?>
</div>

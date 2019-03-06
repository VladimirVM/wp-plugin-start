<?php
/**
 * $this \WPPluginStart\Plugin\Admin\Page
 */
?>
<div class="wrap">
	<?php if ($title = get_admin_page_title()) { ?>
		<h1><?php echo esc_html($title); ?></h1>
	<?php } ?>
	<div class="admin-page-content">
		<?php $this->content(); ?>
	</div>
</div>
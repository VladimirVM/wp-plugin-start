<div class="wrap">
	<?php if ($title = get_admin_page_title()) { ?>
		<h1><?php echo esc_html($title); ?></h1>
	<?php } ?>
	<div class="admin-page-content">
		<?php \WPPluginStart\Plugin\Admin\Page::content(); ?>
	</div>
</div>
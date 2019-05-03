<div class="wrap cortex-admin-blocks-page">

	<h1>
		<?php _e('Blocks', 'cortex') ?>
		<a href="<?php echo admin_url('post-new.php?post_type=acf-field-group&mode=cortex-block') ?>" class="page-title-action cortex-create-block-link"><?php _e('Add New', 'cortex') ?></a>
	</h1>

	<form method="get">
		<?php $list->display() ?>
		<input type="hidden" name="page" value="cortex_admin_blocks_page">
	</form>

</div>
<div class="wrap cortex-admin-blocks-page">

	<h1>
		<?php _e('Blocks', 'cortex') ?>
		<a href="<?php echo admin_url('post-new.php?post_type=acf-field-group&mode=cortex-block') ?>" class="page-title-action cortex-create-block-link"><?php _e('Add New', 'cortex') ?></a>
	</h1>

	<form method="get">
		<?php $list->display() ?>
		<input type="hidden" name="page" value="cortex_admin_blocks_page">
	</form>

	<div class="cortex-modal cortex-create-block-modal">
		<div class="cortex-modal-content">
			<div class="cortex-modal-content-head">
				<div class="cortex-modal-content-head-title"><?php _e('Create Block', 'cortex') ?></div>
				<div class="cortex-modal-content-head-close">
					<button type="button" class="cortex-modal-close">
						<span class="cortex-modal-close-icon">
							<span class="screen-reader-text"><?php _e('Close', 'cortex') ?></span>
						</span>
					</button>
				</div>
			</div>
			<div class="cortex-modal-content-body">
				<iframe></iframe>
			</div>
		</div>
	</div>

	<div class="cortex-modal cortex-update-block-modal">
		<div class="cortex-modal-content">
			<div class="cortex-modal-content-head">
				<div class="cortex-modal-content-head-title"><?php _e('Edit Block', 'cortex') ?></div>
				<div class="cortex-modal-content-head-close">
					<button type="button" class="cortex-modal-close">
						<span class="cortex-modal-close-icon">
							<span class="screen-reader-text"><?php _e('Close', 'cortex') ?></span>
						</span>
					</button>
				</div>
			</div>
			<div class="cortex-modal-content-body">
				<iframe></iframe>
			</div>
		</div>
	</div>

</div>
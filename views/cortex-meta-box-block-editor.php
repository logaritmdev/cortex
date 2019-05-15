
<div
	id="cortex-block-editor"
	class="cortex-text-editor cortex-block-editor"
	data-file="block"
	data-id="<?php echo $block ? $block->get_type() : 0 ?>"
	data-date="<?php echo $block ? $block->get_block_file_date() : 0 ?>">
</div>

<textarea id="cortex-block" name="cortex_block" style="display:none"><?php echo $block ? $block->get_block_file_content() : '' ?></textarea>
<div
	id="cortex-style-editor"
	class="cortex-text-editor cortex-style-editor"
	data-file="style"
	data-id="<?php echo $block ? $block->get_type() : 0 ?>"
	data-date="<?php echo $block ? $block->get_style_file_date() : 0 ?>">
</div>
<textarea id="cortex-style" name="cortex_style" style="display:none"><?php echo $block ? $block->get_style_file_content() : '' ?></textarea>
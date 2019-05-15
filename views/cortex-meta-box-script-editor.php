<div
	id="cortex-script-editor"
	class="cortex-text-editor cortex-script-editor"
	data-file="script"
	data-id="<?php echo $block ? $block->get_type() : 0 ?>"
	data-date="<?php echo $block ? $block->get_script_file_date(): 0 ?>">
</div>

<textarea id="cortex-script" name="cortex_script" style="display:none"><?php echo $block ? $block->get_script_file_content() : '' ?></textarea>
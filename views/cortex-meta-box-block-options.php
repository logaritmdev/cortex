<div class="inside acf-fields -left">

	<?php if ($mode == 'create'): ?>

		<div class="acf-field acf-field-checkbox" data-name="cortex_block_template_path" data-type="select">

			<div class="acf-label">

				<label for="cortex_block_template_path"><?php _e('Location', 'cortex') ?></label>

				<p class="description">
					<?php _e('The location where the block template will be created.', 'cortex') ?>
				</p>

			</div>

			<div class="acf-input">
				<ul class="acf-checkbox-list acf-bl">
					<?php foreach ($locations as $location): ?>
						<li><input type="radio" name="cortex_block_template_path" value="<?php echo $location ?>" checked="checked"><label><?php echo $location ?></label></li>
					<?php endforeach ?>
				</ul>
			</div>

		</div>

	<?php endif ?>

	<div class="acf-field acf-field-text" data-name="cortex_block_template_name" data-type="text">

		<div class="acf-label">

			<label for="cortex_block_template_name"><?php _e('Folder', 'cortex') ?></label>

			<p class="description">
				<?php _e('The block template folder name, will default to the block name if empty.', 'cortex') ?>
			</p>

		</div>

		<div class="acf-input">
			<input type="text" id="cortex_block_template_name" name="cortex_block_template_name" value="<?php echo basename($template->get_path()) ?>">
		</div>

	</div>

	<div class="acf-field acf-field-text" data-name="cortex_block_template_name" data-type="text">

		<div class="acf-label">

			<label for="cortex_block_template_name"><?php _e('Template engine', 'cortex') ?></label>

			<p class="description">

			</p>

		</div>

		<div class="acf-input">
			<ul class="acf-checkbox-list acf-bl">
				<li><input type="radio" name="cortex_block_file_type" value="twig" <?php if ($template && $template->get_block_file_type() == 'twig'): ?> checked="checked" <?php endif ?>><label>Twig</label></li>
				<li><input type="radio" name="cortex_block_file_type" value="blade" <?php if ($template && $template->get_block_file_type() == 'blade'): ?> checked="checked" <?php endif ?>><label>Blade</label></li>
			</ul>
		</div>

	</div>

	<div class="acf-field acf-field-text" data-name="cortex_block_template_name" data-type="text">

		<div class="acf-label">

			<label for="cortex_block_template_name"><?php _e('Preprocessor engine', 'cortex') ?></label>

			<p class="description">

			</p>

		</div>

		<div class="acf-input">
			<ul class="acf-checkbox-list acf-bl">
				<li><input type="radio" name="cortex_style_file_type" value="sass" <?php if ($template && $template->get_style_file_type() == 'sass'): ?> checked="checked" <?php endif ?>><label>Sass</label></li>
				<li><input type="radio" name="cortex_style_file_type" value="less" <?php if ($template && $template->get_style_file_type() == 'less'): ?> checked="checked" <?php endif ?>><label>Less</label></li>
			</ul>
		</div>

	</div>

	<?php if ($mode == 'create'): ?>
		<input type="hidden" name="cortex_create_block" value="1" />
	<?php endif ?>

	<?php if ($mode == 'update'): ?>
		<input type="hidden" name="cortex_update_block" value="1" />
	<?php endif ?>

	<input type="hidden" name="mode" value="cortex-block" />

</div>
<fieldset>
	<table>
		<?php foreach ($values as $value): ?>
			<tr>
				<td style="padding:0px 12px 0px 0px"><label><strong><?php echo $value['name'] ?> ?></strong></label></td>
				<td style="padding:0px 12px 0px 0px"><input type="radio" name="cortex_block_status[<?php echo $value['slug'] ?>]" value="enabled" <?php if ($value['enabled']): ?> checked <?php endif ?>> Enabled</td>
				<td style="padding:0px 12px 0px 0px"><input type="radio" name="cortex_block_status[<?php echo $value['slug'] ?>]" value="disabled" <?php if ($value['enabled']): ?> checked <?php endif ?>> Disabled</td>
			</tr>
		<?php endforeach ?>
	</table>
</fieldset>
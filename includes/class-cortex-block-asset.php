<?php

/**
 * Enqueues each Cortex block's own styles and scripts for the current post.
 * @class CortexBlockAsset
 * @since 2.0.0
 */
class CortexBlockAsset {

	/**
	 * Enqueues the styles and scripts of every Cortex block used in the
	 * current post's content.
	 * @method enqueue
	 * @since 2.0.0
	 */
	public static function enqueue() {

		global $post;

		if ($post == null) {
			return;
		}

		$blocks = parse_blocks($post->post_content);

		if ($blocks) {
			self::process($blocks);
		}
	}

	/**
	 * Recursively walks a list of parsed blocks and enqueues the assets of
	 * every Cortex block found.
	 * @method process
	 * @since 2.0.0
	 * @hidden
	 */
	private static function process($blocks) {

		global $post;

		if ($blocks == null) {
			return;
		}

		foreach ($blocks as $block) {

			$name = isset($block['attrs']['name']) ? $block['attrs']['name'] : null;

			if ($name) {

				if ($cortex_block = Cortex::get_block(str_replace('acf/', '', $name))) {

					$cortex_block->enqueue_styles();
					$cortex_block->enqueue_scripts();

					if (is_admin()) {

						/**
						 * Forces the block to render so any child blocks it displays
						 * get a chance to enqueue their own styles/scripts too.
						 */

						$id = 'block_' . $block['attrs']['id'];
						acf_setup_meta($block['attrs']['data'], $id, true);
						$data = get_fields($id);
						acf_reset_meta($id);

						ob_start();
						$cortex_block->display($block['attrs']['id'], isset($post->ID) ? $post->ID : 0, $data);
						ob_end_clean();
					}

					continue;
				}
			}

			$inner = isset($block['innerBlocks']) ? $block['innerBlocks'] : null;

			if ($inner) {
				self::process($inner);
			}
		}
	}
}

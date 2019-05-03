<?php

/**
 * The meta box that shows extra data when editing a block template.
 * @class CortexMetaBoxBlockOptions
 * @since 2.0.0
 */
class CortexMetaBoxBlockOptions extends CortexMetaBox {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 2.0.0
	 */
	protected function template() {
		return 'cortex-meta-box-block-options.php';
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 2.0.0
	 */
	protected function load($context) {
		$context['locations'] = Cortex::get_relative_block_locations();
		$context['template'] = $this->get_block();
		return $context;
	}


	/**
	 * @method get_block
	 * @since 2.0.0
	 * @hidden
	 */
	private function get_block() {
		global $post;
		return Cortex::get_block(get_post_meta($post->ID, '_cortex_block_type', true));
	}
}

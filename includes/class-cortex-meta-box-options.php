<?php

/**
 * The meta box that shows extra data when editing a block template.
 * @class CortexMetaBoxOptions
 * @since 0.1.0
 */
class CortexMetaBoxOptions extends CortexMetaBox {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 0.1.0
	 */
	protected function template() {
		return 'cortex-meta-box-options.twig';
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 0.1.0
	 */
	protected function load($context) {
		$context['locations'] = Cortex::get_relative_block_locations();
		$context['template'] = $this->get_block_template();
		return $context;
	}


	/**
	 * @method get_block_template
	 * @since 0.1.0
	 * @hidden
	 */
	private function get_block_template() {
		global $post;
		return Cortex::get_block_template(get_post_meta($post->ID, '_cortex_block_guid', true));
	}
}

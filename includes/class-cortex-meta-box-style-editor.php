<?php

/**
 * The meta box that shows a style editor.
 * @class CortexMetaBoxStyleEditor
 * @since 2.0.0
 */
class CortexMetaBoxStyleEditor extends CortexMetaBox {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 2.0.0
	 */
	protected function template() {
		return 'cortex-meta-box-style-editor.php';
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 2.0.0
	 */
	protected function load($context) {
		$context['template'] = $this->get_block_template();
		return $context;
	}

	/**
	 * @method get_block_template
	 * @since 2.0.0
	 * @hidden
	 */
	private function get_block_template() {
		global $post;
		return Cortex::get_block_template(get_post_meta($post->ID, '_cortex_block_type', true));
	}
}

<?php

/**
 * The meta box that shows a block editor.
 * @class CortexMetaBoxBlockEditor
 * @since 2.0.0
 */
class CortexMetaBoxBlockEditor extends CortexMetaBox {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 2.0.0
	 */
	protected function template() {
		return 'cortex-meta-box-block-editor.php';
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 2.0.0
	 */
	protected function load($context) {
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
		return Cortex::get_block_by_id($post->ID);
	}
}

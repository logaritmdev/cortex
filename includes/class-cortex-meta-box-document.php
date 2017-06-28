<?php

/**
 * The meta box that displays and manages the block for a specified document.
 * @class CortexMetaBoxDocument
 * @since 0.1.0
 */
class CortexMetaBoxDocument extends CortexMetaBox {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 0.1.0
	 */
	protected function template() {
		return 'cortex-meta-box-document.twig';
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 0.1.0
	 */
	protected function load($context) {

		global $post;

		$document = $post->ID;

		$context['groups'] = Cortex::get_block_groups();
		$context['blocks'] = Cortex::get_blocks($document);
		$context['templates'] = Cortex::get_block_templates();
		$context['document'] = $document;

		return $context;
	}
}

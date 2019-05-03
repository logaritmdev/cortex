<?php

/**
 * The base class for renderer.
 * @class CortexBlockRenderer
 * @since 2.0.0
 */
abstract class CortexBlockRenderer {

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * The block being rendered.
	 * @property block
	 * @since 2.0.0
	 */
	protected $block = null;

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the renderer
	 * @constructor
	 * @since 2.0.0
	 */
	public function __construct(CortexBlock $block) {
		$this->block = $block;
	}

	/**
	 * Renders the block.
	 * @method render
	 * @since 2.0.0
	 */
	abstract public function render(array $vars = array());
}
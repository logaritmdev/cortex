<?php

/**
 * Renders a twig template.
 * @class CortexBlockTwigRenderer
 * @since 2.0.0
 */
class CortexBlockTwigRenderer extends CortexBlockRenderer {

	/**
	 * @inherited
	 * @method render
	 * @since 2.0.0
	 */
	public function render(array $vars = array()) {

		if (Timber::$locations == null) {
			Timber::$locations = array();
		}

		/*
		 * Forces the first template location to be the
		 * template we want to use. We'll remove it after.
		 */

		array_unshift(Timber::$locations, $this->block->get_type()->get_path());

		/*
		 * Creates a reference of the block and renderer for cases that
		 * I might not have thought of yet.
		 */

		$data = Timber::get_context();
		$data['block'] = $this->block;

		if ($vars) {
			$data = array_merge($data, $vars);
		}

		Timber::render('block.twig', $data);

		/*
		 * Removes the block path from the timber locations so it
		 * wont interfere anymore.
		 */

		array_shift(Timber::$locations);
	}
}
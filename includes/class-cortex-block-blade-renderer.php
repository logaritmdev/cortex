<?php

use Roots\Sage\Container;

/**
 * Renders a twig template.
 * @class CortexBlockBladeRenderer
 * @since 2.0.0
 */
class CortexBlockBladeRenderer extends CortexBlockRenderer {

	private $blade = null;

	/**
	 * @inherited
	 * @method render
	 * @since 2.0.0
	 */
	public function render(array $vars = array()) {

		if ($this->blade === null) {

			$container = Container::getInstance();

			$this->blade = $container->bound('blade')
				? $container->makeWith('blade')
				: $container->makeWith('sage.blade');
		}

		$path = $this->block->get_type()->get_path();

		echo $this->blade->render("$path/block.blade.php", $vars);
	}
}
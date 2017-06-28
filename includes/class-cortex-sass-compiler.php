<?php

require_once __DIR__ . "/../vendor/scssphp/scss.inc.php";

use Leafo\ScssPhp\Compiler;

class CortexSassCompiler {

	/**
	 * Compiles Sass file into css.
	 * @method compile
	 * @since 0.1.0
	 */
	public static function compile($code, $includes = array()) {

		$paths = get_option('cortex_style_include_path');

		if ($paths) {

			$paths = explode("\n", $paths);

			foreach ($paths as $i => $path) {
				$paths[$i] = sprintf('%s/%s', get_template_directory(), trim($path));
			}

		} else {
			$paths = array();
		}

		$compiler = new Compiler();
		$compiler->setImportPaths($paths);
		return $compiler->compile($code);
	}
}
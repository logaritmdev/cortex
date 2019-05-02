<?php

require_once __DIR__ . "/../vendor/lessphp/lessc.inc.php";

class CortexLessCompiler {

	/**
	 * Compiles Less file into css.
	 * @method compile
	 * @since 0.1.0
	 */
	public static function compile($code, $includes = array()) {

		$paths = get_option('cortex_style_include_path');

		if ($paths) {

			$paths = explode("\n", $includes);

			foreach ($paths as $i => $path) {
				$paths[$i] = sprintf('%s/%s', get_template_directory(), trim($path));
			}

		} else {
			$paths = array();
		}

		$compiler = new lessc();
		$compiler->setImportDir($paths);
		return $compiler->compile($code);
	}
}
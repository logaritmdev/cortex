<?php

/**
 * Define the internationalization functionality.
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @class Cortex_i18n
 * @since 0.1.0
 */
class Cortex_i18n {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Load the plugin text domain for translation.
	 * @method load_plugin_textdomain
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('cortex', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
	}
}

<?php

/**
 * The public-facing functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @since 0.1.0
 */
class Cortex_Public {

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * Hack to detect whether timber is resizing an image.
	 * @since 0.1.0
	 * @hidden
	 */
	public static $resizing_image = false;

	/**
	 * The main plugin.
	 * @property plugin
	 * @since 0.1.0
	 */
	private $plugin;

	/**
	 * The name of this plugin.
	 * @property plugin_name
	 * @since 0.1.0
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 * @property plugin_version
	 * @since 0.1.0
	 */
	private $plugin_version;

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initialize the class and set its properties.
	 * @constructor
	 * @since 0.1.0
	 */
	public function __construct($plugin, $plugin_name, $plugin_version) {
		$this->plugin = $plugin;
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;
	}

	/**
	 * Configures Timber's locations.
	 * @method configure_timber
	 * @since 0.1.0
	 */
	public function configure_timber() {
		Timber::$locations = array();
	}

	/**
	 * Returns the home url that is stripped from WPML lang
	 * @method render
	 * @since 0.1.0
	 */
	public function home_url($url) {

		if (defined('ICL_LANGUAGE_CODE') && self::$resizing_image) {

			/*
				This is a huge and hopefully temporary hack. TimberLibrary has
				some issues resizing images when the image url contains the
				site language identifier. To fix this, in that exact moment,
				we simply remove the language code from the URL.
			*/

			$url = preg_replace('#/' . ICL_LANGUAGE_CODE . '$#', '', $url);
		}

		return $url;
	}
}

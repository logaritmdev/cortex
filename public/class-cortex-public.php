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
	 * Indicates whether blocks are rendering.
	 * @property rendering
	 * @since 0.1.0
	 */
	private static $rendering = false;

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
	 * Register the stylesheets for the public-facing side of the site.
	 * @method enqueue_styles
	 * @since 0.1.0
	 */
	public function enqueue_styles() {

		global $post;

		if ($post) foreach (Cortex::get_blocks($post->ID) as $block) {

			if ($block->get_template()->is_active()) {
				$block->get_template()->enqueue_styles();
				$block->enqueue_styles();
			}

		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @method enqueue_scripts
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		global $post;

		if ($post) foreach (Cortex::get_blocks($post->ID) as $block) {

			if ($block->get_template()->is_active()) {
				$block->get_template()->enqueue_scripts();
				$block->enqueue_scripts();
			}

		}
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
	 * Displays the blocks.
	 * @method render
	 * @since 0.1.0
	 */
	public function render($content) {

		global $post;

		if (self::$rendering) {
			return $content;
		}

		self::$rendering = true;

		$blocks = Cortex::get_blocks($post->ID);

		if (count($blocks) > 0) {

			ob_start();

			foreach (Cortex::get_blocks($post->ID) as $block) {

				if ($block->get_parent_layout() ||
					$block->get_parent_region() ||
					$block->get_template()->is_active() === false) {
					continue;
				}

				$block->display();
			}

			$content = ob_get_contents();

			ob_end_clean();
		}

		self::$rendering = false;

		return $content;
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

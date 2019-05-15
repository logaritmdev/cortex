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
	 * Enqueues the blocks styles and scripts at the top of the page.
	 * @method enqueue_block_assets
	 * @since 2.0.0
	 */
	public function enqueue_block_assets() {

		global $post;

		if ($post == null) {
			return;
		}

		$blocks = parse_blocks($post->post_content);

		if ($blocks) {
			$this->process_block_assets($blocks);
		}
	}

	/**
	 * Enqueues the blocks styles and scripts at the top of the page.
	 * @method enqueue_block_assets
	 * @since 2.0.0
	 */
	private function process_block_assets($blocks) {

		if ($blocks == null) {
			return;
		}

		foreach ($blocks as $block) {

			$name = isset($block['attrs']['name']) ? $block['attrs']['name'] : null;

			if ($name) {
				if ($block = Cortex::get_block(str_replace('acf/', '', $name))) {
					$block->enqueue_styles();
					$block->enqueue_scripts();
					continue;
				}
			}

			$inner = isset($block['innerBlocks']) ? $block['innerBlocks'] : null;

			if ($inner) {
				$this->process_block_assets($inner);
			}
		}
	}
}

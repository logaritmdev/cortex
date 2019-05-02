<?php

class Cortex_Settings {

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
	 * Register a setting.
	 * @method register
	 * @since 0.1.0
	 */
	public function register($name) {
		register_setting('cortex', $name);
	}

	/**
	 * Registers a setting group.
	 * @method register
	 * @since 0.1.0
	 */
	public function register_group($name, $title) {
		add_settings_section($name, $title, array($this, 'group_' . $name), 'cortex');
	}

	/**
	 * Registers a setting field.
	 * @method register_field
	 * @since 0.1.0
	 */
	public function register_field($name, $title, $group) {
		add_settings_field($name, $title, array($this, 'field_' . $name), 'cortex', $group);
	}

	/**
	 * Register the plugin settings.
	 * @method setup
	 * @since 0.1.0
	 */
	public function setup() {
		$this->register('cortex_block_status');
		$this->register('cortex_style_include_path');
		$this->register('cortex_enqueue_styles_admin');
		$this->register('cortex_enqueue_scripts_admin');
		$this->register_group('block_status', __('Blocks', 'cortex'));
		$this->register_field('block_status', __('Blocks', 'cortex'), 'block_status');
		$this->register_group('style', __('Styles Include Path', 'cortex'));
		$this->register_field('style_include_path', __('Styles Include Path', 'cortex'), 'style');
		$this->register_field('enqueue_styles_admin', __('Enqueue all blocks styles on admin page', 'cortex'), 'style');
		$this->register_field('enqueue_scripts_admin', __('Enqueue all block scripts on admin page', 'cortex'), 'style');
	}

	/**
	 * @method group_block_status
	 * @since 0.1.0
	 * @hidden
	 */
	public function group_block_status($args) {
		Cortex::render_template('settings/cortex-block-status-group.php');
	}

	/**
	 * @method field_block_status
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_block_status($args) {

		$option = get_option('cortex_block_status');

		$values = array();

		foreach (Cortex::get_block_templates() as $block_template) {

			$name = $block_template->get_name();
			$slug = $block_template->get_type();

			$enabled = $option === false || !(isset($option[$slug])) || $option[$slug] == 'enabled';

			$values[] = array(
				'name' => $name,
				'slug' => $slug,
				'enabled' => $enabled
			);
		}

		Cortex::render_template('settings/cortex-block-status-field.php', array('values' => $values));
	}

	/**
	 * @method group_styles
	 * @since 0.1.0
	 * @hidden
	 */
	public function group_style($args) {
		echo __('Defines options related to block scripts and styles files.', 'cortex');
	}

	/**
	 * @method field_style_include_path
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_style_include_path($args) {
		?>
		<fieldset>
			<textarea cols="60" rows="5" name="cortex_style_include_path"><?php echo get_option('cortex_style_include_path') ?></textarea>
		</fieldset>
		<?php
	}

	/**
	 * @method field_enqueue_styles_admin
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_enqueue_styles_admin($args) {
		?>
		<fieldset>
			<input type="checkbox" name="cortex_enqueue_styles_admin" value="true" <?php echo get_option('cortex_enqueue_styles_admin') == 'true' ? 'checked' : '' ?> />
		</fieldset>
		<?php
	}

	/**
	 * @method field_enqueue_scripts_admin
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_enqueue_scripts_admin($args) {
		?>
		<fieldset>
			<input type="checkbox" name="cortex_enqueue_scripts_admin" value="true" <?php echo get_option('cortex_enqueue_scripts_admin') == 'true' ? 'checked' : '' ?> />
		</fieldset>
		<?php
	}
}
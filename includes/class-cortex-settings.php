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
		$this->register('cortex_environment');
		$this->register('cortex_enqueue_style_admin');
		$this->register('cortex_enqueue_script_admin');

		$this->register_group('block_status', __('Blocks', 'cortex'));
		$this->register_field('block_status', __('Blocks', 'cortex'), 'block_status');
		$this->register_group('style', __('Styles Include Path', 'cortex'));
		$this->register_field('style_include_path', __('Styles Include Path', 'cortex'), 'style');
		$this->register_field('environment', __('Environment', 'cortex'), 'style');
		$this->register_group('settings', __('Settings', 'cortex'));
		$this->register_field('enqueue_style_admin', __('Enqueue block style on admin page', 'cortex'), 'settings');
		$this->register_field('enqueue_script_admin', __('Enqueue block script on admin page', 'cortex'), 'settings');
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

		if ($option === '' ||
			$option === null) {
			$option = array();
		}

		$values = array();

		foreach (Cortex::get_blocks() as $block) {

			$name = $block->get_name();
			$slug = $block->get_type();

			$enabled = $option === false || (isset($option[$slug]) === false) || $option[$slug] === 'enabled';

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
	 * @method group_settings
	 * @since 2.0.0
	 * @hidden
	 */
	public function group_settings($args) {
		echo __('General settings.', 'cortex');
	}

	/**
	 * @method field_style_include_path
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_style_include_path($args) {
		?>
		<fieldset>
			<textarea cols="60" rows="5" name="cortex_style_include_path"><?php echo esc_attr(get_option('cortex_style_include_path')) ?></textarea>
		</fieldset>
		<?php
	}

	/**
	 * @method field_environment
	 * @since 2.0.0
	 * @hidden
	 */
	public function field_environment($args) {
		?>
		<fieldset>
			<table>
				<tr>
					<td style="padding:0px 12px 0px 0px"><input type="radio" name="cortex_environment" value="dev" <?php echo get_option('cortex_environment') === 'dev' ? 'checked' : '' ?> /> Development</td>
					<td style="padding:0px 12px 0px 0px"><input type="radio" name="cortex_environment" value="prod" <?php echo get_option('cortex_environment') === 'prod' ? 'checked' : '' ?> /> Production</td>
				</tr>
			</table>
		</fieldset>
		<?php
	}

	/**
	 * @method field_enqueue_style_admin
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_enqueue_style_admin($args) {
		?>
		<fieldset>
			<input type="checkbox" name="cortex_enqueue_style_admin" value="true" <?php echo get_option('cortex_enqueue_style_admin') ? 'checked' : '' ?> />
		</fieldset>
		<?php
	}

	/**
	 * @method field_enqueue_script_admin
	 * @since 0.1.0
	 * @hidden
	 */
	public function field_enqueue_script_admin($args) {
		?>
		<fieldset>
			<input type="checkbox" name="cortex_enqueue_script_admin" value="true" <?php echo get_option('cortex_enqueue_script_admin') ? 'checked' : '' ?> />
		</fieldset>
		<?php
	}
}
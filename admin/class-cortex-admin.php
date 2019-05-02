<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-block-options.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-block-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-style-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-script-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-sass-compiler.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-less-compiler.php';

/**
 * The plugin's admin functionality.
 * @class Cortex_Admin
 * @since 0.1.0
 */
class Cortex_Admin {

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
	 * Register the stylesheets for the admin area.
	 * @method enqueue_styles
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'styles/main.css', array(), $this->plugin_version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 * @method enqueue_scripts
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script('ace', plugin_dir_url(__FILE__) . 'ace/ace.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_css', plugin_dir_url(__FILE__) . 'ace/mode-css.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_less', plugin_dir_url(__FILE__) . 'ace/mode-less.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_sass', plugin_dir_url(__FILE__) . 'ace/mode-sass.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_twig', plugin_dir_url(__FILE__) . 'ace/mode-twig.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_html', plugin_dir_url(__FILE__) . 'ace/mode-html.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_mode_javascript', plugin_dir_url(__FILE__) . 'ace/mode-javascript.js', array(), '1.2.6', false);
		wp_enqueue_script('ace_theme_tomorrow_night', plugin_dir_url(__FILE__) . 'ace/theme-tomorrow_night.js', array(), '1.2.6', false);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'scripts/main.js', array('jquery'), $this->plugin_version, false);

		wp_localize_script($this->plugin_name, 'CORTEX', array(

			'site_url'  => site_url(),
			'home_url'  => home_url(),
			'admin_url' => admin_url(),

		));
	}

	/**
	 * Validates all dependencies.
	 * @method validate_dependencies
	 * @since 0.1.0
	 */
	public function validate_dependencies() {

		$dependencies = array(

			array(
				'name' => 'Advanced Custom Fields',
				'slug' => 'advanced-custom-fields-pro/acf.php',
				'version' => '5.8.0-RC2'
			),

			array(
				'name' => 'Timber Library',
				'slug' => 'timber-library/timber.php',
				'version' => '1.3.0'
			)

		);

		foreach ($dependencies as $dependency) {

			$name = $dependency['name'];
			$slug = $dependency['slug'];
			$version = $dependency['version'];

			if ($this->has_version($slug, $version) && is_plugin_active($slug)) {
				continue;
			}

			$this->add_error(sprintf('Cortex require plugin %s with version %s or higher', $name, $version));
		}
	}

	/**
	 * Configures Timber.
	 * @method configure_timber
	 * @since 0.1.0
	 */
	public function configure_timber() {
		if (class_exists('Timber')) Timber::$locations = array(__DIR__ . '/../views');
	}

	/**
	 * Configures the menu.
	 * @method configure_menu
	 * @since 0.1.0
	 */
	public function configure_menu() {

		add_options_page(
			__('Cortex', 'cortex'),
			__('Cortex', 'cortex'),
			'manage_options',
			'cortex_settings_page',
			array($this, 'admin_settings_page')
		);

		add_menu_page(
			__('Blocks', 'cortex'),
			__('Blocks', 'cortex'),
			'manage_options',
			'cortex_blocks_page',
			array($this, 'admin_blocks_page'),
			'dashicons-editor-kitchensink',
			'80.025'
		);
	}

	/**
	 * Configures ui elements.
	 * @method configure_ui
	 * @since 0.1.0
	 */
	public function configure_ui() {

		if ($this->is_create_block_page() ||
			$this->is_update_block_page()) {
			remove_meta_box('acf-field-group-locations', 'acf-field-group', 'normal');
			remove_meta_box('acf-field-group-options', 'acf-field-group', 'normal');
		}

		remove_meta_box('icl_div_config', 'cortex-block', 'normal');
	}

	/**
	 * Configures body classes.
	 * @method configure_body_classes
	 * @since 0.1.0
	 */
	public function configure_body_classes($classes) {
		if ($this->is_create_block_page()) $classes = $classes . 'cortex-create-block-page';
		if ($this->is_update_block_page()) $classes = $classes . 'cortex-update-block-page';
		return $classes;
	}

	/**
	 * Configures the meta boxes.
	 * @method configure_meta_box
	 * @since 0.1.0
	 */
	public function configure_meta_box() {

		$create_block_page = $this->is_create_block_page();
		$update_block_page = $this->is_update_block_page();

		if ($create_block_page) {
			new CortexMetaBoxBlockOptions(__('Options', 'cortex'), 'cortex_create_block', 'acf-field-group', array('mode' => 'create'), 'normal', 'default');
		}

		if ($update_block_page) {
			new CortexMetaBoxBlockOptions(__('Options', 'cortex'), 'cortex_update_block', 'acf-field-group', array('mode' => 'update'), 'normal', 'default');
		}

		if ($create_block_page || $update_block_page) {
			new CortexMetaBoxBlockEditor(__('Block', 'cortex'), 'cortex_block_editor', 'acf-field-group', array(), 'normal', 'default');
			new CortexMetaBoxStyleEditor(__('Style', 'cortex'), 'cortex_style_editor', 'acf-field-group', array(), 'normal', 'default');
			new CortexMetaBoxScriptEditor(__('Script', 'cortex'), 'cortex_script_editor', 'acf-field-group', array(), 'normal', 'default');
		}
	}

	/**
	 * Renders the admin blocks page.
	 * @method admin_blocks_page
	 * @since 0.1.0
	 */
	public function admin_blocks_page() {

		if (isset($_GET['settings-updated'])) {
			add_settings_error('cortex_messages', 'cortex_message', __('Settings Saved', 'cortex'), 'updated');
 		}

		$list = new CortexBlockTemplateList();
		$list->prepare_items();

		Cortex::render_template('cortex-admin-blocks-page.php', array('list' => $list));
	}

	/**
	 * Renders the admin settings page.
	 * @method admin_settings_page
	 * @since 0.1.0
	 */
	public function admin_settings_page() {
		Cortex::render_template('cortex-admin-settings-page.php');
	}

	/**
	 * @method render_block
	 * @since 0.1.0
	 */
	public function render_block() {

		$id   = $_REQUEST['id'];
		$post = $_REQUEST['post'];

		$post = get_post($post);

		if ($post == null) {
			return;
		}

		$target = null;
		$blocks = parse_blocks($post->post_content);

		if ($blocks) {

			foreach ($blocks as $block) {

				if (isset($block['attrs']['id']) == false ||
					isset($block['attrs']['name']) == false) {
					continue;
				}

				if ($block['attrs']['id'] == $id) {
					$target = acf_get_block_type($block['attrs']['name']);
					break;
				}
			}
		}

		if ($target) {

			?>

				<!DOCTYPE HTML>
				<html <?php language_attributes()?>>
				<head>
					<meta charset="utf-8">
					<meta http-equiv="x-ua-compatible" content="ie=edge">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<?php wp_head() ?>
				</head>
				<body>
					<?php acf_render_block($target, '', false, $post->ID); ?>
					<?php wp_footer() ?>
				</body>
				</html>
			<?php
		}

		exit;
	}

	/**
	 * Returns the modification date of a specific block template file.
	 * @method get_block_template_file_date
	 * @since 0.1.0
	 */
	public function get_block_template_file_date() {

		$id = $_POST['id'];
		$file = $_POST['file'];

		$block = Cortex::get_block_template($id);

		if ($block == null) {
			exit;
		}

		switch ($file) {

			case 'block':
				echo $block->get_block_file_date();
				exit;

			case 'style':
				echo $block->get_style_file_date();
				exit;

			case 'script':
				echo $block->get_script_file_date();
				exit;
		}
	}

	/**
	 * Returns the content of a specific block template file.
	 * @method get_block_template_file_data
	 * @since 0.1.0
	 */
	public function get_block_template_file_data() {

		$file = $_POST['file'];
		$id = $_POST['id'];

		$block = Cortex::get_block_template($id);

		if ($block == null) {
			exit;
		}

		switch ($file) {

			case 'block':
				echo $block->get_block_file_content();
				exit;

			case 'style':
				echo $block->get_style_file_content();
				exit;

			case 'script':
				echo $block->get_script_file_content();
				exit;
		}
	}

	/**
	 * Synchronizes the block templates with the database.
	 * @method synchronize
	 * @since 0.1.0
	 */
	public function synchronize() {

		global $pagenow;

		if (class_exists('acf') === false || ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'acf-field-group')) {
			return;
		}

		$groups = array();

		foreach (acf_get_field_groups() as $field_group) {

			$type = get_post_meta($field_group['ID'], '_cortex_block_type', true);
			$date = get_post_meta($field_group['ID'], '_cortex_block_date', true);

			if ($type == '') {
				continue;
			}

			$date = (int) $date;

			$groups[$type] = array(
				'fields' => $field_group,
				'date' => $date,
				'type' => $type,
			);

			if (Cortex::has_block_template($type) == false) {
				acf_delete_field_group($field_group['ID']);
			}
		}

		foreach (Cortex::get_block_templates() as $type => $template) {

			$template_fields = $template->get_fields();

			if (empty($template_fields) === true) {
				continue;
			}

			$sync = false;
			$data = isset($groups[$type]) ? $groups[$type] : null;
			$date = $template->get_date();

			if ($sync == false) $sync = $data == null;
			if ($sync == false) $sync = $date > $data['date'];

			if ($sync) {

				$id = $data ? $data['fields']['ID'] : 0;

				acf_update_setting('json', false);

				$field_group = acf_get_local_field_group($template_fields['key']);
				$field_group['ID'] = $id;
				$field_group['key'] = $template_fields['key'];
				$field_group['title'] = $template_fields['title'];
				$field_group['fields'] = $template_fields['fields'];

				acf_reset_fields($field_group['fields']);

				$field_group = acf_import_field_group($field_group);
				update_post_meta($field_group['ID'], '_cortex_block_type', $type);
				update_post_meta($field_group['ID'], '_cortex_block_date', $date);

				acf_update_setting('json', true);
			}
		}
	}

	/**
	 * Called when a post is saved.
	 * @method save_post
	 * @since 0.1.0
	 */
	public function save_post($id) {

		if (wp_is_post_revision($id) || wp_is_post_autosave($id)) {
			return;
		}

		remove_action('save_post', array($this, 'save_post'));

		switch (get_post_type($id)) {

			case 'acf-field-group':
				$this->save_post_acf_field_group($id);
				break;
		}

		add_action('save_post', array($this, 'save_post'));
	}

	/**
	 * Updates the fields json file of a block when its structure changes.
	 * @method save_post_acf_field_group
	 * @since 0.1.0
	 */
	public function save_post_acf_field_group($id) {

		global $pagenow;

		if ($pagenow === 'post.php') {

			$create_block = isset($_POST['cortex_create_block']);
			$update_block = isset($_POST['cortex_update_block']);

			if ($create_block ||
				$update_block) {

				acf_update_setting('json', false);

				$field_group = $this->get_field_group($id);

				if (empty($field_group['title'])) {
					return;
				}

				$template = null;
				$path = isset($_POST['cortex_block_template_path']) ? trim($_POST['cortex_block_template_path']) : null;
				$name = isset($_POST['cortex_block_template_name']) ? trim($_POST['cortex_block_template_name']) : null;

				if ($name == '' ||
					$name == null) {
					$name = $field_group['title'];
				}

				if ($create_block) {

					if ($path == null) {
						return;
					}

					$template = Cortex::create_block_template_folder($path, $field_group['title'], $name, $field_group);
					update_post_meta($id, '_cortex_block_type', $template->get_type());
					update_post_meta($id, '_cortex_block_date', $template->get_date());

					$_POST['cortex_block'] = !empty($_POST['cortex_block']) ? $_POST['cortex_block'] : Cortex::render_template('cortex-empty-block-template.php', array(), true);

				} else {

					$template = Cortex::get_block_template(get_post_meta($id, '_cortex_block_type', true));
					$basename = Cortex::get_block_template(get_post_meta($id, '_cortex_block_type', true))->get_type();

					if ($basename != $name) {
						$template = Cortex::rename_block_template_folder($template, $name);
						update_post_meta($id, '_cortex_block_type', $template->get_type());
						update_post_meta($id, '_cortex_block_date', $template->get_date());
					}
				}

				$title = $_POST['post_title'];

				if ($field_group['title'] != $title) {
					$field_group['title'] = $title;
					$template->update_config('name', $title);
				}

				$template->update_field_file($field_group);

				if (isset($_POST['cortex_block_file_type'])) $template->update_config('block_file_type', $_POST['cortex_block_file_type']);
				if (isset($_POST['cortex_style_file_type'])) $template->update_config('style_file_type', $_POST['cortex_style_file_type']);

				try {

					$template->update_block_file(stripslashes($_POST['cortex_block']));
					$template->update_style_file(stripslashes($_POST['cortex_style']));
					$template->update_script_file(stripslashes($_POST['cortex_script']));

				} catch (Exception $e) {
					$this->add_error($e->getMessage());
				}
			}
		}
	}

	/**
	 * @method filter_field_groups
	 * @since 2.0.0
	 * @hidden
	 */
	public function filter_field_groups($query) {

		global $pagenow;

		if ($pagenow == 'edit.php') {

			if ($query->get('post_type') == 'acf-field-group') {

				/*
				 * This will hide blocks from the list since the meta
				 * key is only applied to blocks.
				 */

				$meta_query =  array(
					array(
						'key'     => '_cortex_block_type',
						'compare' => 'NOT EXISTS'
					)
				);

				$query->set('meta_query', $meta_query);
			}
		}

		return $query;
	}

	/**
	 * Display notices stored in session.
	 * @method display_notices
	 * @since 0.1.0
	 * @hidden
	 */
	public function display_notices() {

		$default = array();

		foreach (Cortex::session_take('cortex_notices', $default) as $message) {
			echo sprintf('<div class="notice notice-warning"><p>%s</p></div>', $message);
		}

		foreach (Cortex::session_take('cortex_errors', $default) as $message) {
			echo sprintf('<div class="notice notice-error"><p>%s</p></div>', $message);
		}
	}

	//--------------------------------------------------------------------------
	// Private API
	//--------------------------------------------------------------------------

	/**
	 * @method has_been_saved
	 * @since 0.1.0
	 * @hidden
	 */
	private function has_been_saved($id) {
		static $saved = array();
		$found = in_array($id, $saved);
		$saved[] = $id;
		return $found;
	}

	/**
	 * @method is_create_block_page
	 * @since 0.1.0
	 * @hidden
	 */
	private function is_create_block_page() {
		global $pagenow;
		return $pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'acf-field-group' && isset($_GET['mode']) && $_GET['mode'] === 'cortex-block';
	}

	/**
	 * @method is_update_block_page
	 * @since 0.1.0
	 * @hidden
	 */
	private function is_update_block_page() {
		global $pagenow;
		return $pagenow === 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) === 'acf-field-group' && get_post_meta($_GET['post'], '_cortex_block_type', true);
	}

	/**
	 * Retrieve the field group data.
	 * @method get_field_group
	 * @since 0.1.0
	 */
	private function get_field_group($id) {
		$field_group = acf_get_field_group($id);
		$field_group['fields'] = acf_get_fields($field_group);
		$field_group = acf_prepare_field_group_for_export($field_group);
		return $field_group;
	}

	/**
	 * @method has_version
	 * @since 0.1.0
	 * @hidden
	 */
	private function has_version($plugin, $version) {

		$data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

		if ($data == null) {
			return false;
		}

		return version_compare($data['Version'], $version, '>=');
	}

	/**
	 * @method add_notice
	 * @since 0.1.0
	 * @hidden
	 */
	private function add_notice($message) {
		Cortex::session_add('cortex_notices', $message);
	}

	/**
	 * @method add_error
	 * @since 0.1.0
	 * @hidden
	 */
	private function add_error($message) {
		Cortex::session_add('cortex_errors', $message);
	}
}

/**
 * @function acf_reset_fields
 * @since 0.1.0
 * @hidden
 */
function acf_reset_fields(&$fields) {

	if (is_array($fields)) foreach ($fields as &$field) {

		$field['ID'] = 0;

		if (isset($field['sub_fields'])) {
			acf_reset_fields($field['sub_fields']);
		}

		if (isset($field['layouts'])) foreach ($field['layouts'] as &$layout) {
			acf_reset_fields($layout['sub_fields']);
		}
	}
}
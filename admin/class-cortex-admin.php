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

			'blocks' => array_filter(acf_get_field_groups(), function($group) {

				$block_type = acf_maybe_get($group, '@block_type');
				$block_name = acf_maybe_get($group, '@block_name');

				if ($block_type &&
					$block_name) {
					return true;
				}

				return false;
			})

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
	 * Adds global html.
	 * @method configure_footer
	 * @since 2.0.0
	 */
	public function configure_footer() {

		?>

			<div class="cortex-modal cortex-create-block-modal">
				<div class="cortex-modal-content">
					<div class="cortex-modal-content-head">
						<div class="cortex-modal-content-head-title"><?php _e('Create Block', 'cortex') ?></div>
						<div class="cortex-modal-content-head-close">
							<button type="button" class="cortex-modal-close">
								<span class="cortex-modal-close-icon">
									<span class="screen-reader-text"><?php _e('Close', 'cortex') ?></span>
								</span>
							</button>
						</div>
					</div>
					<div class="cortex-modal-content-body">
						<iframe></iframe>
					</div>
				</div>
			</div>

			<div class="cortex-modal cortex-update-block-modal">
				<div class="cortex-modal-content">
					<div class="cortex-modal-content-head">
						<div class="cortex-modal-content-head-title"><?php _e('Edit Block', 'cortex') ?></div>
						<div class="cortex-modal-content-head-close">
							<button type="button" class="cortex-modal-close">
								<span class="cortex-modal-close-icon">
									<span class="screen-reader-text"><?php _e('Close', 'cortex') ?></span>
								</span>
							</button>
						</div>
					</div>
					<div class="cortex-modal-content-body">
						<iframe></iframe>
					</div>
				</div>
			</div>

		<?php
	}

	/**
	 * Filters the block title in the field group page
	 * @method configure_meta_box
	 * @since 0.1.0
	 */
	public function filter_acf_field_group_title($title, $id) {

		global $pagenow;

		if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'acf-field-group') {
			return Cortex::has_block_by_id($id) ? ('[Block] ' . $title) : $title;
		}

		return $title;
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

		$list = new CortexBlockList();
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

		if ($post === null) {
			return;
		}

		$target = null;
		$blocks = parse_blocks($post->post_content);

		if ($blocks) {

			foreach ($blocks as $block) {

				if (isset($block['attrs']['id']) === false ||
					isset($block['attrs']['name']) === false) {
					continue;
				}

				if ($block['attrs']['id'] === $id) {
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
	 * @method get_block_file_date
	 * @since 0.1.0
	 */
	public function get_block_file_date() {

		$id   = $_POST['id'];
		$file = $_POST['file'];

		$block = Cortex::get_block($id);

		if ($block === null) {
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
	 * @method get_block_file_data
	 * @since 0.1.0
	 */
	public function get_block_file_data() {

		$id   = $_POST['id'];
		$file = $_POST['file'];

		$block = Cortex::get_block($id);

		if ($block === null) {
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

		if (defined('DOING_AJAX') ||
			defined('DOING_CRON') ||
			defined('DOING_SYNC')) {
			return;
		}

		$groups = acf_get_field_groups();

		if (empty($groups)) {
			return;
		}

		$sync = array();

		foreach ($groups as $group) {

			$id         = acf_maybe_get($group, 'ID');
			$block_name = acf_maybe_get($group, '@block_name');
			$block_type = acf_maybe_get($group, '@block_type');
			$modified   = acf_maybe_get($group, 'modified', 0);

			if ($block_name === null ||
				$block_type === null) {

				/*
				 * There are not local file definition for this block but
				 * it could have been a delete block. In this case we remove
				 * the field group
				 */

				$is_block = $this->get_acf_field_group_meta_flag($id);

				if ($is_block) {

					acf_delete_field_group($group['ID']);

					$this->add_notice("
						The block {$group['title']} has been delete because it is no longer
						present within a searchable block folder.
					");
				}

				continue;
			}

			/*
			 * The the group id is null it means this file has been loaded
			 * locally only and has not been synced.
			 */

			if ($id === null) {
				$sync[$group['key']] = $group;
			} elseif ($modified && $modified > get_post_modified_time('U', true, $group['ID'], true)) {
				$sync[$group['key']] = $group;
			}
		}

		if (empty($sync)) {
			return;
		}

		define('DOING_SYNC', true);

		foreach ($sync as $key => $val) {

			$block_type = acf_maybe_get($group, '@block_type');
			$block_name = acf_maybe_get($group, '@block_name');

			$this->add_notice("
				Block <b>$block_name</b> in folder <b>$block_type</b> has been synchronized.
			");

			/*
			 * Unset these block related properties when syncing. We want them
			 * to be loaded from local files only
			 */

			unset($sync[$key]['@block_name']);
			unset($sync[$key]['@block_type']);
			unset($sync[$key]['@block_hint']);
			unset($sync[$key]['@block_icon']);
			unset($sync[$key]['@block_group']);
			unset($sync[$key]['@block_class']);
			unset($sync[$key]['active']);
			unset($sync[$key]['hidden']);
			unset($sync[$key]['modified']);

			if (acf_have_local_fields($key)) {
				$sync[$key]['fields'] = acf_get_local_fields($key);
			}

			acf_import_field_group($sync[$key]);
		}
	}

	/**
	 * Called when a post is saved.
	 * @method save_post
	 * @since 0.1.0
	 */
	public function save_post($id) {

		if (wp_is_post_revision($id) ||
			wp_is_post_autosave($id)) {
			return;
		}

		if (defined('DOING_AJAX') ||
			defined('DOING_CRON')) {
			return;
		}

		$post_type = get_post_type($id);

		if ($post_type === 'acf-field-group') {
			$this->save_field_group($id);
		}
	}

	/**
	 * Updates the fields json file of a block when its structure changes.
	 * @method save_field_group
	 * @since 2.0.0
	 */
	public function save_field_group($id) {

		global $pagenow;

		$doing_sync   = defined('DOING_SYNC');
		$create_block = isset($_POST['cortex_create_block']);
		$update_block = isset($_POST['cortex_update_block']);

		if ($doing_sync ||
			$create_block ||
			$update_block) {

			/*
			 * This prevents ACF from creating json file because we want to
			 * keep manage the json file by ourselves.
			 */

			 acf_update_setting('json', false);
		}

		if ($doing_sync) {

			/*
			 * We need to set the meta flag when syncin a new field here
			 * because otherwise it would not be set.
			 */

			$this->set_acf_field_group_meta_flag($id);
			return;
		}

		if ($pagenow === 'post.php') {

			if ($create_block ||
				$update_block) {

				$group = $this->get_acf_field_group($id);

				if (empty($group['title'])) {
					return;
				}

				$block = null;
				$path = isset($_POST['cortex_block_path']) ? trim($_POST['cortex_block_path']) : null;
				$slug = isset($_POST['cortex_block_slug']) ? trim($_POST['cortex_block_slug']) : null;

				if ($create_block) {

					if ($path === null) {
						return;
					}

					if ($slug === '' ||
						$slug === null) {
						$slug = $group['title'];
					}

					$slug = self::generate_block_slug($slug);
					$type = self::generate_block_type($slug);

					$path = Cortex::create_block_folder(
						$path,
						$slug,
						$_POST['cortex_block_file_type'],
						$_POST['cortex_style_file_type']
					);

					$data = array(
						'name' => $group['title'],
						'icon' => '',
						'hint' => '',
						'group' => ''
					);

					$block = new CortexBlockType(
						$type,
						$path,
						$data
					);

					$block->update_config_file($data);

					$_POST['cortex_block'] = !empty($_POST['cortex_block']) ? $_POST['cortex_block'] : '<section class="block"></section>';

				} else {

					$block = Cortex::get_block_by_id($id);

					if ($block === null) {

						/*
						 * This method is called twice. The second time though
						 * if the block has been renamed, it won't be found.
						 * This is ok, but just stop here
						 */

						 return;
					}

					if ($slug) {

						$slug = self::generate_block_slug($slug);
						$type = self::generate_block_type($slug);

						if ($block->get_type() != $type) {
							self::rename_block_folder($block, $slug);
						}
					}

					$title = $_POST['post_title'];

					if ($group['title'] != $title) {
						$group['title'] = $title;
						$block->update_config('name', $title);
					}
				}

				/*
				 * Sets a meta value on the field group id to indicate that
				 * this field group is a block. This is only used to determine
				 * if a block as been removed or to filter the list.
				 */

				$this->set_acf_field_group_meta_flag($id);

				/*
				 * Add the modified date to the field group. This will be used
				 * later as a way to determine whether the block must be
				 * synced.
				 */

				$group['modified'] = get_post_modified_time('U', true, $id, true);

				$block->update_field_file($group);

				if (isset($_POST['cortex_block_file_type'])) $block->update_config('block_file_type', $_POST['cortex_block_file_type']);
				if (isset($_POST['cortex_style_file_type'])) $block->update_config('style_file_type', $_POST['cortex_style_file_type']);

				try {

					$block->update_block_file(stripslashes($_POST['cortex_block']));
					$block->update_style_file(stripslashes($_POST['cortex_style']));
					$block->update_script_file(stripslashes($_POST['cortex_script']));

				} catch (Exception $e) {

					$this->add_error($e->getMessage());

				}
			}
		}
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
	 * Creates a new block template folder at the specified location.
	 * @method rename_block_folder
	 * @since 2.0.0
	 */
	public static function rename_block_folder($block, $slug) {

		global $wpdb;

		$old_path = $block->get_path();
		$old_type = $block->get_type();

		if (file_exists($old_path) === false) {
			return $block;
		}

		$new_path = explode('/', $old_path);
		array_pop($new_path);
		$new_path = implode('/', $new_path);
		$new_path = $new_path . '/' . $slug;

		$new_type = self::generate_block_type($new_path);

		rename(
			$old_path,
			$new_path
		);

		$wpdb->query("
			UPDATE
				$wpdb->posts
			SET
				post_content = REPLACE(
					post_content,
					'<!-- wp:acf/$old_type',
					'<!-- wp:acf/$new_type'
				)
		");

		$block->set_type($new_type);
		$block->set_path($new_path);
	}

	/**
	 * @method generate_block_type
	 * @since 2.0.0
	 * @hidden
	 */
	private static function generate_block_type($path) {
		return basename($path);
	}

	/**
	 * @method generate_block_slug
	 * @since 2.0.0
	 * @hidden
	 */
	private static function generate_block_slug($name) {

		$name = trim($name);
		$name = preg_replace('/(?<!^)[A-Z]+/', ' $0', $name);
		$name = preg_replace('/\s+/', '-', $name);

		return sanitize_title($name);
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
		return $pagenow === 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) === 'acf-field-group' && Cortex::has_block_by_id($_GET['post']);
	}

	/**
	 * Retrieve the field group data.
	 * @method get_acf_ield_group
	 * @since 2.0.0
	 */
	private function get_acf_field_group($id) {
		$group = acf_get_field_group($id);
		$group['fields'] = acf_get_fields($group);
		$group = acf_prepare_field_group_for_export($group);
		return $group;
	}

	/**
	 * Returns the acf field group is block flag.
	 * @method get_acf_field_group_meta_flag
	 * @since 2.0.0
	 */
	public static function get_acf_field_group_meta_flag($id) {
		return get_post_meta($id, '_is_block', true);
	}

	/**
	 * Sets the acf field group is block flag.
	 * @method get_acf_field_group_meta_flag
	 * @since 2.0.0
	 */
	public static function set_acf_field_group_meta_flag($id) {
		update_post_meta($id, '_is_block', true);
	}

	/**
	 * @method has_version
	 * @since 0.1.0
	 * @hidden
	 */
	private function has_version($plugin, $version) {

		$data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

		if ($data === null) {
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
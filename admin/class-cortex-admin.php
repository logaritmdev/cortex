<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-document.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-options.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-block-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-style-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-script-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-meta-box-preview-editor.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-sass-compiler.php';

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

	/**
	 * Whether we're currently inserting a block.
	 * @property inserting
	 * @since 0.1.0
	 */
	private $inserting = false;

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

		$enqueue_styles = get_option('cortex_enqueue_styles_admin');
		$enqueue_scripts = get_option('cortex_enqueue_styles_admin');

		if ($enqueue_styles || $enqueue_scripts) {
			foreach (Cortex::get_block_templates() as $template) {
				if ($enqueue_styles) $template->enqueue_styles();
				if ($enqueue_scripts) $template->enqueue_scripts();
			}
		}
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

			'messages' => array(
				'remove_block' => __('This block will be removed from this document. Are you sure ?', 'cortex'),
				'create_block_template' => __('Add New Block Template', 'cortex'),
				'update_block_template' => __('Edit Block Template', 'cortex'),
			)

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
				'version' => '5.4.0'
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

		$settings_slug = 'cortex_settings_page';
		$templates_slug = 'cortex_templates_page';

		add_options_page(
			__('Cortex', 'cortex'),
			__('Cortex', 'cortex'),
			'manage_options',
			'cortex_settings_page',
			array($this, 'admin_settings_page')
		);

		add_menu_page(
			__('Cortex', 'cortex'),
			__('Cortex', 'cortex'),
			'manage_options',
			'cortex_templates_page',
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

		foreach (Cortex::get_post_types() as $post_type) {
			new CortexMetaBoxDocument(__('Blocks', 'cortex'), 'cortex_meta_box_document', $post_type);
		}

		$create_block_page = $this->is_create_block_page();
		$update_block_page = $this->is_update_block_page();

		if ($create_block_page) {
			new CortexMetaBoxOptions(__('Options', 'cortex'), 'cortex_create_block', 'acf-field-group', array('mode' => 'create'), 'normal', 'default');
		}

		if ($update_block_page) {
			new CortexMetaBoxOptions(__('Options', 'cortex'), 'cortex_update_block', 'acf-field-group', array('mode' => 'update'), 'normal', 'default');
		}

		if ($create_block_page || $update_block_page) {
			new CortexMetaBoxBlockEditor(__('Block', 'cortex'), 'cortex_block_editor', 'acf-field-group', array(), 'normal', 'default');
			new CortexMetaBoxStyleEditor(__('Styles', 'cortex'), 'cortex_style_editor', 'acf-field-group', array(), 'normal', 'default');
			new CortexMetaBoxScriptEditor(__('Scripts', 'cortex'), 'cortex_script_editor', 'acf-field-group', array(), 'normal', 'default');
			new CortexMetaBoxPreviewEditor(__('Preview', 'cortex'), 'cortex_preview_editor', 'acf-field-group', array(), 'normal', 'default');
		}
	}

	/**
	 * Renders the admin blocks page.
	 * @method admin_blocks_page
	 * @since 0.1.0
	 */
	public function admin_blocks_page() {

		if (isset($_GET['settings-updated'])) {
			add_settings_error( 'cortex_messages', 'cortex_message', __('Settings Saved', 'cortex'), 'updated');
 		}

		$list = new CortexBlockTemplateList();
		$list->prepare_items();

		Cortex::render_twig('cortex-admin-blocks-page.twig', array('list' => $list));
	}

	/**
	 * Renders the admin settings page.
	 * @method admin_settings_page
	 * @since 0.1.0
	 */
	public function admin_settings_page() {
		Cortex::render_twig('cortex-admin-settings-page.twig');
	}

	/**
	 * Inserts a block in a document.
	 * @method insert_block
	 * @since 0.1.0
	 */
	public function insert_block() {

		global $post;

		$template = $_POST['template'];
		$document = $_POST['document'];
		$parent_layout = isset($_POST['parent_layout']) ? $_POST['parent_layout'] : '';
		$parent_region = isset($_POST['parent_region']) ? $_POST['parent_region'] : '';

		$this->inserting = true;

		$id = wp_insert_post(array(
			'post_parent'  => $document,
			'post_type'    => 'cortex-block',
			'post_title'   => '',
			'post_content' => '',
			'post_status'  => 'publish',
		));

		$this->inserting = false;

		$block = Cortex::create_block(
			$id,
			$document,
			$template,
			$parent_layout,
			$parent_region
		);

		Cortex::insert_block($document, $block);

		Cortex::render_twig('cortex-block-list-item-preview.twig', array('block' => $block));
		exit;
	}

	/**
	 * Removes a block in a document.
	 * @method remove_block
	 * @since 0.1.0
	 */
	public function remove_block() {

		$id = $_POST['id'];
		$document = $_POST['document'];

		$block = Cortex::get_block($document, $id);

		if ($block == null) {
			return;
		}

		Cortex::remove_block($document, $block);
		exit;
	}

	/**
	 * Displays the preview of a block.
	 * @method preview_block
	 * @since 0.1.0
	 */
	public function preview_block() {

		$id = $_POST['id'];
		$document = $_POST['document'];

		$block = Cortex::get_block($document, $id);

		if ($block == null) {
			return;
		}

		$block->preview();
		exit;
	}

	/**
	 * @method render_block
	 * @since 0.1.0
	 */
	public function render_block() {

		$id = $_REQUEST['id'];
		$document = $_REQUEST['document'];

		$block = Cortex::get_block($document, $id);

		?>

			<!DOCTYPE HTML>
			<html <?php language_attributes()?>>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="x-ua-compatible" content="ie=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<?php wp_head() ?>
			</head>
			<body <?php body_class('preview') ?>>
				<?php

					if ($block) {
						$block->get_template()->enqueue_scripts();
						$block->get_template()->enqueue_styles();
						$block->enqueue_scripts();
						$block->enqueue_styles();
						$block->display();
					}

				?>
			</body>
			<?php wp_footer() ?>
			</html>

		<?php

		exit;
	}

	/**
	 * Moves a block from a document to another.
	 * @method move_block
	 * @since 0.1.0
	 */
	public function move_block() {

		$id = $_POST['id'];
		$src_document = $_POST['src_document'];
		$dst_document = $_POST['dst_document'];

		$block = Cortex::get_block($src_document, $id);

		if ($block == null) {
			return;
		}

		Cortex::move_block($src_document, $dst_document, $block);
		exit;
	}

	/**
	 * Copies a block from a document to another.
	 * @method copy_block
	 * @since 0.1.0
	 */
	public function copy_block() {

		$id = $_POST['id'];
		$src_document = $_POST['src_document'];
		$dst_document = $_POST['dst_document'];

		$block = Cortex::get_block($src_document, $id);

		if ($block == null) {
			return;
		}

		Cortex::copy_block($src_document, $dst_document, $block);
		exit;
	}

	/**
	 * Saves the block order.
	 * @method order
	 * @since 0.1.0
	 */
	public function order_blocks() {

		$order = $_POST['order'];
		$document = $_POST['document'];

		$order = stripslashes($order);
		$order = json_decode($order, true);

		if ($order == null) {
			return;
		}

		Cortex::order_blocks($document, $order);
		exit;
	}

	/**
	 * Sets the block parent layout and region.
	 * @method set_parent_block
	 * @since 0.1.0
	 */
	public function set_parent_block() {

		$id = $_POST['id'];
		$document = $_POST['document'];
		$parent_layout = $_POST['parent_layout'];
		$parent_region = $_POST['parent_region'];

		$block = Cortex::get_block($document, $id);

		if ($block == null) {
			return;
		}

		Cortex::set_parent_block($document, $block, $parent_layout, $parent_region);
		exit;
	}

	/**
	 * Returns the modification date of a specific block template file.
	 * @method get_block_template_file_date
	 * @since 0.1.0
	 */
	public function get_block_template_file_date() {

		$file = $_POST['file'];
		$guid = $_POST['guid'];

		$block = Cortex::get_block_template($guid);

		if ($block == null) {
			exit;
		}

		switch ($file) {

			case 'preview':
				echo $block->get_preview_file_date();
				exit;

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
	 * @method get_block_template_file_content
	 * @since 0.1.0
	 */
	public function get_block_template_file_content() {

		$file = $_POST['file'];
		$guid = $_POST['guid'];

		$block = Cortex::get_block_template($guid);

		if ($block == null) {
			exit;
		}

		switch ($file) {

			case 'preview':
				echo $block->get_preview_file_content();
				exit;

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
	 * Returns a list of document that can be used to copy/move blocks.
	 * @method get_documents
	 * @since 0.1.0
	 */
	public function get_documents() {

		$groups = array();
		$flags = array();

		if (function_exists('icl_get_languages')) {

			foreach (icl_get_languages() as $language) {

				$code = $language['language_code'];
				$flag = $language['country_flag_url'];

				$flags[$code] = $flag;
			}
		}

		foreach (Cortex::get_post_types() as $slug) {

			$name = get_post_type_object($slug)->label;

			$args = array(
				'posts_per_page'   => 50,
				'offset'           => 0,
				'post_type'        => $slug,
				'post_status'      => 'any',
				'suppress_filters' => true,
				'orderby'          => 'title',
				'order'            => 'ASC'
			);

			if (isset($_REQUEST['search']) && trim($_REQUEST['search'])) {
				$args['s'] = $_REQUEST['search'];
			}

			$count = wp_count_posts($slug);
			$total = $count->publish + $count->draft;

			$groups[] = array(
				'name'  => $name,
				'slug'  => $slug,
				'posts' => Timber::get_posts($args),
				'total' => $total
			);
		}

		Cortex::render_twig('cortex-post-selector.twig', array('groups' => $groups, 'icl' => class_exists('SitePress'), 'flags' => $flags));
		exit;
	}

	/**
	 * Synchronizes the block templates with the database.
	 * @method synchronize
	 * @since 0.1.0
	 */
	public function synchronize() {

		if (class_exists('acf') === false) {
			return;
		}

		$groups = array();

		foreach (acf_get_field_groups() as $field_group) {

			$guid = get_post_meta($field_group['ID'], '_cortex_block_guid', true);
			$date = get_post_meta($field_group['ID'], '_cortex_block_date', true);

			if ($guid == '') {
				continue;
			}

			$date = (int) $date;

			$groups[$guid] = array(
				'fields' => $field_group,
				'date' => $date,
				'guid' => $guid,
			);

			if (Cortex::has_block_template($guid) == false) {
				acf_delete_field_group($field_group['ID']);
			}
		}

		foreach (Cortex::get_block_templates() as $guid => $template) {

			$template_fields = $template->get_fields();

			if (empty($template_fields) === true) {
				continue;
			}

			$sync = false;
			$data = isset($groups[$guid]) ? $groups[$guid] : null;
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
				update_post_meta($field_group['ID'], '_cortex_block_guid', $guid);
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

		// because save_post is always called twice
		if (wp_is_post_revision($id)) {
			return;
		}

		// or even more
		if ($this->has_been_saved($id)) {
			return;
		}

		remove_action('save_post', array($this, 'save_post'));

		switch (get_post_type($id)) {

			case 'cortex-block':
				$this->save_block($id);
				break;

			case 'acf-field-group':
				$this->save_post_acf_field_group($id);
				break;
		}

		foreach (Cortex::get_post_types() as $post_type) if (get_post_type() === $post_type) {

			$blocks = Cortex::get_blocks($id);

			foreach ($blocks as &$block) {
				if ($block->get_revision()) {
					$block->set_revision(null);
				}
			}

			Cortex::set_blocks($id, $blocks);
		}

		add_action('save_post', array($this, 'save_post'));
	}

	/**
	 * Handles a save post of type cortex-block.
	 * @method save_block
	 * @since 0.1.0
	 */
	public function save_block($id) {

		if ($this->inserting) {
			return;
		}

		$post = get_post($id);

		$revisions = wp_get_post_revisions($id);
		$revision = array_shift($revisions);
		$revision = array_shift($revisions);

		if ($revision) {

			$blocks = Cortex::get_blocks($post->post_parent);

			foreach ($blocks as &$block) {
				if ($block->get_id() === $id) {
					$block->set_revision($revision->ID);
				}
			}

			Cortex::set_blocks($post->post_parent, $blocks);
		}

		do_action('cortex/save_block', $post->post_parent, $post->ID);
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

				if ($name == null || $name == '') {
					$name = $field_group['title'];
				}

				if ($create_block) {

					if ($path == null) {
						return;
					}

					$template = Cortex::create_block_template_folder($path, $field_group['title'], $name, $field_group);
					update_post_meta($id, '_cortex_block_guid', $template->get_guid());
					update_post_meta($id, '_cortex_block_date', $template->get_date());

					$defaults = Cortex::render_twig('cortex-empty-block-template.twig', array('fields' => $field_group['fields']), true);
					$_POST['cortex_block']   = !empty($_POST['cortex_block'])   ? $_POST['cortex_block']   : $defaults;
					$_POST['cortex_preview'] = !empty($_POST['cortex_preview']) ? $_POST['cortex_preview'] : $defaults;

				} else {

					$template = Cortex::get_block_template(get_post_meta($id, '_cortex_block_guid', true));

					$basename = basename($template->get_path());
					if ($basename != $name) {
						$template = Cortex::rename_block_template_folder($template, $name);
					}
				}

				$title = $_POST['post_title'];

				if ($field_group['title'] != $title) {
					$field_group['title'] = $title;
					$template->update_block_json_file_name($title);
				}

				$template->update_fields_json_file($field_group);

				try {

					$template->update_block_file(stripslashes($_POST['cortex_block']));
					$template->update_style_file(stripslashes($_POST['cortex_style']));
					$template->update_script_file(stripslashes($_POST['cortex_script']));
					$template->update_preview_file(stripslashes($_POST['cortex_preview']));

				} catch (Exception $e) {
					$this->add_error($e->getMessage());
				}
			}
		}
	}

	/**
	 * Handles a delete post of type cortex-block.
	 * @method save_block
	 * @since 0.1.0
	 */
	public function delete_post($id) {

		remove_action('delete_post', array($this, 'delete_post'));

		foreach (Cortex::get_post_types() as $post_type) if (get_post_type() === $post_type) {
			Cortex::clear_blocks($id);
		}

		add_action('delete_post', array($this, 'delete_post'));
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
	// Duplicate Post Plugin Extension
	//--------------------------------------------------------------------------

	/**
	 * Called when a page or post is duplicated.
	 * @method dp_duplicate_post
	 * @since 0.1.0
	 * @hidden
	 */
	public function dp_duplicate_post($new_post_id, $post, $status) {
		Cortex::copy_blocks($post->ID, $new_post_id, true);
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
		return $pagenow === 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) === 'acf-field-group' && get_post_meta($_GET['post'], '_cortex_block_guid', true);
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
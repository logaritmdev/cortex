<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-template.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-template-list.php';

/**
 * The core plugin class.
 * @class Cortex
 * @since 0.1.0
 */
class Cortex {

	//--------------------------------------------------------------------------
	// Static Properties
	//--------------------------------------------------------------------------

	/**
	 * The block groups.
	 * @since 0.1.0
	 * @hidden
	 */
	private static $block_groups = array();

	/**
	 * The block templates.
	 * @since 0.1.0
	 * @hidden
	 */
	private static $block_templates = array();

	//--------------------------------------------------------------------------
	// Static Methods
	//--------------------------------------------------------------------------

	/**
	 * Indicates whether a session variable exists.
	 * @method session_has
	 * @since 0.1.0
	 */
	public static function session_has($key) {
		return isset($_SESSION[$key]);
	}

	/**
	 * Returns a session variable.
	 * @method session_get
	 * @since 0.1.0
	 */
	public static function session_get($key, $default = null) {
		return self::session_has($key) ? $_SESSION[$key] : $default;
	}

	/**
	 * Assigns a session variable.
	 * @method session_set
	 * @since 0.1.0
	 */
	public static function session_set($key, $val) {
		$_SESSION[$key] = $val;
	}

	/**
	 * Append to a session value.
	 * @method session_add
	 * @since 0.1.0
	 */
	public static function session_add($key, $val) {

		if (self::session_has($key) == false) {
			self::session_set($key, array());
		}

		$array = self::session_get($key);
		$array[] = $val;

		self::session_set($key, $array);
	}

	/**
	 * Returns a clear a session varaible
	 * @method session_get
	 * @since 0.1.0
	 */
	public static function session_take($key, $default = null) {

		if (self::session_has($key) == false) {
			return $default;
		}

		$value = self::session_get($key, $default);

		unset($_SESSION[$key]);

		return $value;
	}

	/**
	 * Convenience method to render a twig template.
	 * @method render_twig
	 * @since 0.1.0
	 */
	public static function render_twig($template, array $vars = array(), $echo = false) {

		if ($echo) {
			ob_start();
		}

		Timber::render($template, array_merge(Timber::get_context(), $vars));

		if ($echo) {
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
	}

	/**
	 * Convenience method to manually render a block with custom data.
	 * @method render_block
	 * @since 0.1.0
	 */
	public static function render_block($template, $post_data = array(), $meta_data = array()) {

		$argv = func_get_args();
		$argc = func_num_args();

		if (is_numeric($argv[0]) &&
			is_numeric($argv[1])) {

			$block = Cortex::get_block($argv[0], $argv[1]);
			$block->get_template()->enqueue_scripts();
			$block->get_template()->enqueue_styles();
			$block->enqueue_scripts();
			$block->enqueue_styles();
			$block->display();
			return;
		}

		foreach (self::get_block_templates() as $block_template) {

			if ($block_template->is_type($template)) {

				$block = $block_template->create_block();
				$block->get_template()->enqueue_scripts();
				$block->get_template()->enqueue_styles();
				$block->enqueue_scripts();
				$block->enqueue_styles();

				$block->display(
					$post_data,
					$meta_data
				);

				return;
			}
		}
	}

	/**
	 * Returns a block instance for a specified document and block id.
	 * @method get_block
	 * @since 0.1.0
	 */
	public static function get_block($document, $id) {

		foreach (self::get_blocks($document) as $block) {
			if ($block->get_id() == $id) return $block;
		}

		return null;
	}

	/**
	 * Returns an array of block instance for a specifie document.
	 * @method get_blocks
	 * @since 0.1.0
	 */
	public static function get_blocks($document) {

		static $cache = array();

		$blocks = isset($cache[$document]) ? $cache[$document] : null;

		if ($blocks == null) {

			$blocks = json_decode(get_post_meta($document, '_cortex_blocks', true), true);

			if ($blocks == null) {
				return array();
			}

			$map = function($data) use ($document) {

				$block = self::create_block(
					$data['id'],
					$data['document'],
					$data['template'],
					$data['parent_layout'],
					$data['parent_region']
				);

				if ($block) {
					$block->set_revision($data['revision']);
					return $block;
				}

				return null;

			};

			$blocks = $cache[$document] = array_filter(array_map($map, $blocks));
		}

		return $blocks;
	}

	/**
	 * Assigns an array of block instance for a specifie document.
	 * @method set_blocks
	 * @since 0.1.0
	 */
	public static function set_blocks($document, $blocks) {

		$map = function($block) {

			return array(
				'id' => $block->get_id(),
				'revision' => $block->get_revision(),
				'template' => $block->get_template()->get_guid(),
				'document' => $block->get_document(),
				'parent_layout' => $block->get_parent_layout(),
				'parent_region' => $block->get_parent_region()
			);

		};

		update_post_meta($document, '_cortex_blocks', json_encode(array_map($map, $blocks)));
	}

	/**
	 * Inserts a block into the spcified document.
	 * @method insert_block
	 * @since 0.1.0
	 */
	public static function insert_block($document, CortexBlock $block) {
		$blocks = self::get_blocks($document);
		$blocks = self::block_array_insert($blocks, $block);
		self::set_blocks($document, $blocks);
	}

	/**
	 * Removes a block from the spcified document.
	 * @method remove_block
	 * @since 0.1.0
	 */
	public static function remove_block($document, CortexBlock $block) {
		$blocks = self::get_blocks($document);
		$blocks = self::block_array_remove($blocks, $block);
		$blocks = self::block_array_remove_children($blocks, $block);
		self::set_blocks($document, $blocks);
		wp_delete_post($block->get_id(), true);
	}

	/**
	 * Removes all blocks from the selected document.
	 * @method remove_block
	 * @since 0.1.0
	 */
	public static function clear_blocks($document) {
		$blocks = self::get_blocks($document);
		foreach ($blocks as $block) {
			self::remove_block($document, $block);
		}
	}

	/**
	 * Moves a block from a document to another.
	 * @method move_block;
	 * @since 0.1.0
	 */
	public static function move_block($src_document, $dst_document, CortexBlock $block) {

		$src_block_id = $block->get_id();

		$post = get_post($src_block_id);

		$args = array(
			'ID'             => $src_block_id,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $dst_document,
			'post_password'  => $post->post_password,
			'post_status'    => $post->post_status,
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
		);

		wp_update_post($args);

		$src_blocks = self::get_blocks($src_document);
		$dst_blocks = self::get_blocks($dst_document);

		$removed = array();

		$src_blocks = self::block_array_remove($src_blocks, $block);
		$src_blocks = self::block_array_remove_children($src_blocks, $block, $removed);
		$dst_blocks = self::block_array_insert($dst_blocks, $block);

		$dst_blocks = array_merge($dst_blocks, $removed);

		foreach ($dst_blocks as &$dst_block) {
			$dst_block->set_document($dst_document);
		}

		self::set_blocks($src_document, $src_blocks);
		self::set_blocks($dst_document, $dst_blocks);
	}

	/**
	 * Copies a block from a document to another.
	 * @method copy_block
	 * @since 0.1.0
	 */
	public static function copy_block($src_document, $dst_document, CortexBlock $block) {

		$dst_blocks = self::get_blocks($dst_document);

		$dst_block = self::duplicate_block($src_document, $dst_document, $block);
		$dst_nodes = self::duplicate_nodes($src_document, $dst_document, $block, $dst_block);

		self::set_blocks($dst_document, array_merge($dst_blocks, array($dst_block), $dst_nodes));
	}

	/**
	 * Copies all the blocks from a specified document.
	 * @method copy_block
	 * @since 0.1.0
	 */
	public static function copy_blocks($src_document, $dst_document, $override = false) {

		$src_blocks = self::get_blocks($src_document);
		$dst_blocks = self::get_blocks($dst_document);

		if ($override) {
			$dst_blocks = array();
		}

		foreach ($src_blocks as $src_block) {

			$dst_block = self::duplicate_block($src_document, $dst_document, $src_block);
			$dst_nodes = self::duplicate_nodes($src_document, $dst_document, $src_block, $dst_block);

			$dst_blocks = array_merge($dst_blocks, array($dst_block), $dst_nodes);
		}

		self::set_blocks($dst_document, $dst_blocks);
	}

	/**
	 * Private function that duplicates a block and return it.
	 * @method duplicate_block
	 * @since 0.1.0
	 */
	private static function duplicate_block($src_document, $dst_document, CortexBlock $block) {

		global $wpdb;

		$src_block_id = $block->get_id();

		$post = get_post($src_block_id);

		$args = array(
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $dst_document,
			'post_password'  => $post->post_password,
			'post_status'    => $post->post_status,
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
		);

		$id = wp_insert_post($args);

		$metas = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$src_block_id");

		if (count($metas) > 0) {

			$insert = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			$values = array();

			foreach ($metas as $source_meta) {

				$key = $source_meta->meta_key;
				$val = $source_meta->meta_value;
				$val = addslashes($val);

				$values[] = "SELECT $id, '$key', '$val'";
			}

			$wpdb->query($insert . implode(" UNION ALL ", $values));
		}

		return new CortexBlock(
			$id, $dst_document,
			$block->get_template(),
			$block->get_parent_layout(),
			$block->get_parent_region()
		);
	}

	/**
	 * Private function that duplicates a layout recursively and returns it.
	 * @method duplicate_nodes
	 * @since 0.1.0
	 */
	private static function duplicate_nodes($src_document, $dst_document, CortexBlock $src_layout, CortexBlock $dst_layout) {

		$src_blocks = self::get_blocks($src_document);
		$dst_blocks = array();

		$src_layout_id = $src_layout->get_id();
		$dst_layout_id = $dst_layout->get_id();

		foreach ($src_blocks as $src_block) {

			if ($src_block->get_parent_layout() === $src_layout_id) {

				$dst_block = self::duplicate_block($src_document, $dst_document, $src_block);
				$dst_block->set_parent_layout($dst_layout_id);

				$dst_blocks = array_merge($dst_blocks, array($dst_block), self::duplicate_nodes(
					$src_document,
					$dst_document,
					$src_block,
					$dst_block
				));
			}
		}

		return $dst_blocks;
	}

	/**
	 * @method get_block_index
	 * @since 1.0.0
	 * @hidden
	 */
	private static function get_block_index($document, $id) {

		$blocks = json_decode(get_post_meta($document, '_cortex_blocks', true), true);

		if ($blocks == null) {
			return false;
		}

		foreach ($blocks as $index => $block) {
			if ($block['id'] == $id) return $index;
		}

		return false;
	}

	/**
	 * @method get_block_template_after
	 * @since 1.0.0
	 * @hidden
	 */
	private static function get_block_template_after($document, $id) {

		$index = self::get_block_index($document, $id);

		if ($index === false) {
			return false;
		}

		$blocks = json_decode(get_post_meta($document, '_cortex_blocks', true), true);

		return isset($blocks[$index + 1]) ? $blocks[$index + 1]['template'] : null;
	}

	/**
	 * @method get_block_template_before
	 * @since 1.0.0
	 * @hidden
	 */
	private static function get_block_template_before($document, $id) {

		$index = self::get_block_index($document, $id);

		if ($index === false) {
			return false;
		}

		$blocks = json_decode(get_post_meta($document, '_cortex_blocks', true), true);

		return isset($blocks[$index - 1]) ? $blocks[$index - 1]['template'] : null;
	}

	/**
	 * @method is_type_of_block
	 * @since 0.1.0
	 * @hidden
	 */
	private static function is_type_of_block($guid, $name) {
		return substr($guid, -strlen($name)) == $name;
	}

	/**
	 * Sets the order of the blocks on a specific page.
	 * @method order_blocks.
	 * @since 0.1.0
	 */
	public static function order_blocks($document, $order) {

		$map = self::get_block_map($document);

		$blocks = array();
		$remove = array();

		foreach ($order as $id) {
			$blocks[] = $map[$id];
		}

		foreach ($map as $block) {
			if (self::block_array_search($blocks, $block) === false) {
				wp_delete_post($block->get_id());
			}
		}

		self::set_blocks($document, $blocks);
	}

	/**
	 * Sets the block parent block layout and region.
	 * @method set_parent_block.
	 * @since 0.1.0
	 */
	public static function set_parent_block($document, $block, $parent_layout, $parent_region) {

		$blocks = self::get_blocks($document);

		foreach ($blocks as &$child) {

			if ($child->get_id() === $block->get_id()) {
				$child->set_parent_layout($parent_layout);
				$child->set_parent_region($parent_region);
				break;
			}

		}

		self::set_blocks($document, $blocks);
	}

	/**
	 * Creates a block instances using the specified data.
	 * @method create_block.
	 * @since 0.1.0
	 */
	public static function create_block($id, $document, $template, $parent_layout, $parent_region) {

		$template = self::get_block_template($template);

		if ($template == null) {

			return null;
		}

		return $template->create_block($id, $document, $parent_layout, $parent_region);
	}

	/**
	 * Returns the locations where blocks can be stored.
	 * @method get_block_sources
	 * @since 0.1.0
	 */
	public static function get_block_sources($paths) {

		foreach (self::$block_templates as $template) {
			$paths[] = $template->get_path();
		}

		return $paths;
	}

	/**
	 * Returns the groups where blocks are categorized.
	 * @method get_block_groups
	 * @since 0.1.0
	 */
	public static function get_block_groups() {
		return self::$block_groups;
	}

	/**
	 * Indicates whether the specified block exists.
	 * @method has_block_template
	 * @since 0.1.0
	 */
	public static function has_block_template($guid) {
		return isset(self::$block_templates[$guid]);
	}

	/**
	 * Returns a block template.
	 * @method get_block_template
	 * @since 0.1.0
	 */
	public static function get_block_template($guid) {
		return isset(self::$block_templates[$guid]) ? self::$block_templates[$guid] : null;
	}

	/**
	 * Returns all block templates.
	 * @method get_block_templates
	 * @since 0.1.0
	 */
	public static function get_block_templates() {
		return self::$block_templates;
	}

	/**
	 * Returns the absolute locations where blocks are stored.
	 * @method get_block_locations
	 * @since 0.1.0
	 */
	public static function get_block_locations() {
		return apply_filters('cortex/block_locations', array(WP_PLUGIN_DIR . '/cortex/blocks', get_template_directory() . '/blocks'));
	}

	/**
	 * Returns the relative locations where blocks are stored.
	 * @method get_relative_block_locations
	 * @since 0.1.0
	 */
	public static function get_relative_block_locations() {

		$locations = array();

		foreach (self::get_block_locations() as $location) {
			$locations[] = str_replace(WP_CONTENT_DIR, '', $location);
		}

		return $locations;
	}

	/**
	 * Creates a new block template folder at the specified location.
	 * @method create_block_template_folder
	 * @since 0.1.0
	 */
	public static function create_block_template_folder($location, $name, $slug, $fields) {

		$path = '';

		foreach (Cortex::get_block_locations() as $candidate) {

			$base = str_replace(WP_CONTENT_DIR, '', $candidate);

			if ($base === $location) {
				$path = $candidate;
				break;
			}
		}

		if ($path === '') {
			trigger_error('Cannot create block template at invalid location ' . $location);
			return;
		}

		$slug = self::create_block_template_slug($slug);

		$path = "$path/$slug";

		@mkdir($path);
		@mkdir("$path/assets");
		@touch("$path/block.json");
		@touch("$path/block.twig");
		@touch("$path/fields.json");
		@touch("$path/preview.twig");

		$block = array(
			'name' => $name,
			'icon' => '',
			'hint' => '',
			'group' => ''
		);

		$guid = self::create_block_template_guid($path);

		$template = new CortexBlockTemplate(
			$guid,
			$path,
			$block['name'],
			$block['icon'],
			$block['hint'],
			$block['group'],
			'',
			$fields
		);

		$template->update_block_json_file($block);

		return $template;
	}

	/**
	 * Creates a new block template folder at the specified location.
	 * @method rename_block_template_folder
	 * @since 0.1.0
	 */
	public static function rename_block_template_folder($template, $name) {

		global $wpdb;

		$slug = self::create_block_template_slug($name);

		$old_path = $template->get_path();
		$old_guid = $template->get_guid();

		$new_path = explode('/', $old_path);
		array_pop($new_path);
		$new_path = implode('/', $new_path);
		$new_path = $new_path . '/' . $slug;

		$new_guid = self::create_block_template_guid($new_path);

		rename($old_path, $new_path);

		$wpdb->query("
			UPDATE
				$wpdb->postmeta
			SET
				meta_value = REPLACE(
					meta_value,
					'\"template\":\"$old_guid\"',
					'\"template\":\"$new_guid\"'
				)
		");

		$wpdb->query("
			UPDATE
				$wpdb->postmeta
			SET
				meta_value = '$new_guid'
			WHERE
				meta_value = '$old_guid' AND
				meta_key = '_cortex_block_guid'
		");

		$new_template = new CortexBlockTemplate(
			$new_guid,
			$new_path,
			$name,
			$template->get_icon(),
			$template->get_hint(),
			$template->get_group(),
			$template->get_class()
		);

		$new_template->set_order($template->get_order());
		$new_template->set_block_file_type($template->get_block_file_type());
		$new_template->set_style_file_type($template->get_style_file_type());
		$new_template->set_fields($template->get_fields());

		return $new_template;
	}

	/**
	 * Returns the post type where blocks can be inserted.
	 * @method get_post_types
	 * @since 0.1.0
	 */
	public static function get_post_types() {

		$post_types = get_option('cortex_post_types');

		if ($post_types) {
			$post_types = array_keys($post_types);
		} else {
			$post_types = array('page');
		}

		return apply_filters('cortex/post_types', $post_types);
	}

	/**
	 * @method create_block_template_guid
	 * @since 0.1.0
	 * @hidden
	 */
	private static function create_block_template_guid($path) {
		return str_replace(WP_CONTENT_DIR, '', $path);
	}

	/**
	 * @method create_block_template_slug
	 * @since 0.1.0
	 * @hidden
	 */
	private static function create_block_template_slug($name) {

		$name = trim($name);
		$name = preg_replace('/(?<!^)[A-Z]+/', ' $0', $name);
		$name = preg_replace('/\s+/', '-', $name);

		return sanitize_title($name);
	}

	/**
	 * @method get_block_map
	 * @since 0.1.0
	 * @hidden
	 */
	private static function get_block_map($document) {
		$map = array();
		foreach (self::get_blocks($document) as $block) $map[$block->get_id()] = $block;
		return $map;
	}

	/**
	 * @method block_array_insert
	 * @since 0.1.0
	 * @hidden
	 */
	private static function block_array_insert($array, $block) {

		$index = self::block_array_search($array, $block);
		if ($index === false) {
			array_push($array, $block);
		}

		return $array;
	}

	/**
	 * @method block_array_remove
	 * @since 0.1.0
	 * @hidden
	 */
	private static function block_array_remove($array, $block) {

		$index = self::block_array_search($array, $block);
		if ($index !== false) {
			array_splice($array, $index, 1);
		}

		return $array;
	}

	/**
	 * @method block_array_remove_children
	 * @since 0.1.0
	 * @hidden
	 */
	private static function block_array_remove_children($array, $parent, &$stack = array()) {

		$items = array();
		$child = array();

		foreach ($array as $block) if ($block->is_child_of($parent)) {

			$child[] = $block;
			$stack[] = $block;

		} else {

			$items[] = $block;

		}

		foreach ($child as $block) {
			$items = self::block_array_remove_children($items, $block, $stack);
		}

		return $items;
	}

	/**
	 * @method block_array_remove_children
	 * @since 1.0.1
	 * @hidden
	 */
	private static function block_array_search($block_array, $block) {

		foreach ($block_array as $index => $item) {
			if ($item->get_id() == $block->get_id()) return $index;
		}

		return false;
	}

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * Whether the plugin is initialized;
	 * @since 0.1.0
	 * @hidden
	 */
	private $initialized = false;

	/**
	 * The plugin loader.
	 * @since 0.1.0
	 * @hidden
	 */
	protected $loader;

	/**
	 * The name of this plugin.
	 * @since 0.1.0
	 * @hidden
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 * @since 0.1.0
	 * @hidden
	 */
	private $plugin_version;

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Define the core functionality of the plugin.
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 * @since 0.1.0
	 */
	public function __construct() {

		@session_start();

		$this->plugin_name = 'cortex';
		$this->plugin_version = CORTEX_PLUGIN_VERSION;

		$this->register_post_types();

		$this->load_dependencies();

		$this->set_locale();

		$this->define_settings();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_acf_hooks();
		$this->define_icl_hooks();
		$this->define_twig_hooks();

		$this->loader->add_action('init', $this, 'init', 20);
	}

	/**
	 * Register the post types required for this plugin.
	 * @method register_post_types
	 * @since 0.1.0
	 */
	protected function register_post_types() {

		register_post_type('cortex-block', array(
			'labels'			=> array(
				'name'					=> __('Cortex Blocks', 'acf' ),
				'singular_name'			=> __('Cortex Block', 'acf' ),
				'add_new'				=> __('Add New' , 'acf' ),
				'add_new_item'			=> __('Add New Cortex Block' , 'acf' ),
				'edit_item'				=> __('Edit Cortex Block' , 'acf' ),
				'new_item'				=> __('New Cortex Block' , 'acf' ),
				'view_item'				=> __('View Cortex Block', 'acf' ),
				'search_items'			=> __('Search Cortex Blocks', 'acf' ),
				'not_found'				=> __('No Cortex Blocks found', 'acf' ),
				'not_found_in_trash'	=> __('No Cortex Blocks found in Trash', 'acf' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'_builtin'            => false,
			'capability_type'     => 'post',
			'capabilities'        => array(),
			'hierarchical'        => true,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array('revisions'),
			'show_in_menu'        => false,
			'exclude_from_search' => false,
		));

	}

	/**
	 * Load the required dependencies for this plugin.
	 * @method load_dependencies
	 * @since 0.1.0
	 */
	protected function load_dependencies() {

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-settings.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-cortex-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-cortex-public.php';

		$this->loader = new Cortex_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 * @method set_locale
	 * @since 0.1.0
	 */
	protected function set_locale() {
		$plugin_i18n = new Cortex_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area of the plugin.
	 * @method define_admin_hooks
	 * @since 0.1.0
	 */
	protected function define_admin_hooks() {

		if (is_admin() === false) {
			return;
		}

		$plugin_admin = new Cortex_Admin($this, $this->get_plugin_name(), $this->get_plugin_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_init', $plugin_admin, 'validate_dependencies');
		$this->loader->add_action('admin_init', $plugin_admin, 'configure_timber');
		$this->loader->add_action('admin_init', $plugin_admin, 'configure_meta_box');
		$this->loader->add_action('admin_menu', $plugin_admin, 'configure_menu');
		$this->loader->add_action('admin_head', $plugin_admin, 'configure_ui', 30);
		$this->loader->add_action('save_post', $plugin_admin, 'save_post');
		$this->loader->add_action('delete_post', $plugin_admin, 'delete_post');
		$this->loader->add_action('wp_loaded', $plugin_admin, 'synchronize');
		$this->loader->add_action('admin_notices', $plugin_admin, 'display_notices');

		$this->loader->add_action('wp_ajax_preview_block', $plugin_admin, 'preview_block');
		$this->loader->add_action('wp_ajax_insert_block', $plugin_admin, 'insert_block');
		$this->loader->add_action('wp_ajax_remove_block', $plugin_admin, 'remove_block');
		$this->loader->add_action('wp_ajax_copy_block', $plugin_admin, 'copy_block');
		$this->loader->add_action('wp_ajax_move_block', $plugin_admin, 'move_block');
		$this->loader->add_action('wp_ajax_order_blocks', $plugin_admin, 'order_blocks');
		$this->loader->add_action('wp_ajax_set_parent_block', $plugin_admin, 'set_parent_block');
		$this->loader->add_action('wp_ajax_get_documents', $plugin_admin, 'get_documents');
		$this->loader->add_action('wp_ajax_get_block_template_file_date', $plugin_admin, 'get_block_template_file_date');
		$this->loader->add_action('wp_ajax_get_block_template_file_content', $plugin_admin, 'get_block_template_file_content');
		$this->loader->add_action('wp_ajax_render_block', $plugin_admin, 'render_block');
		$this->loader->add_action('wp_ajax_nopriv_render_block', $plugin_admin, 'render_block');
		$this->loader->add_filter('admin_body_class', $plugin_admin, 'configure_body_classes');

		// Duplicate Post Plugin Extension
		$this->loader->add_action('dp_duplicate_page', $plugin_admin, 'dp_duplicate_post', 10, 3);
		$this->loader->add_action('dp_duplicate_post', $plugin_admin, 'dp_duplicate_post', 10, 3);

	}

	/**
	 * Register all of the hooks related to the public area of the plugin.
	 * @method define_public_hooks
	 * @since 0.1.0
	 */
	protected function define_public_hooks() {

		if (is_admin() === true) {
			return;
		}

		$plugin_public = new Cortex_Public($this, $this->get_plugin_name(), $this->get_plugin_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 20);
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 20);
		$this->loader->add_action('init', $plugin_public, 'configure_timber');
		$this->loader->add_filter('the_content', $plugin_public, 'render');

		// Timber image resizing issue
		$this->loader->add_filter('home_url', $plugin_public, 'home_url');
	}

	/**
	 * Register all of the settings related to the plugin.
	 * @method define_settings
	 * @since 0.1.0
	 */
	protected function define_settings() {
		$plugin_settings = new Cortex_Settings($this, $this->get_plugin_name(), $this->get_plugin_version());
		$this->loader->add_action('admin_init', $plugin_settings, 'setup');
	}

	/**
	 * Register all of the hooks related to the advanced custom field plugin.
	 * @method define_acf_hooks
	 * @since 0.1.0
	 */
	protected function define_acf_hooks() {
		$this->loader->add_filter('acf/get_field_groups', $this, 'acf_get_field_groups');
	}

	/**
	 * Register all of the hooks related to WPML plugin.
	 * @method define_icl_hooks
	 * @since 0.1.0
	 */
	protected function define_icl_hooks() {
		$this->loader->add_action('icl_make_duplicate', $this, 'icl_make_duplicate', 10, 4);
	}

	/**
	 * Registers twig functions and filters.
	 * @method define_twig_hooks
	 * @since 0.1.0
	 */
	protected function define_twig_hooks() {

		add_filter('get_twig', function($twig) {

			$twig->addFunction(new \Twig_SimpleFunction('classes', function(array $args = array()) {

				$classes = array();

				foreach ($args as $arg) {

					if (is_array($arg)) {

						foreach ($arg as $key => $val) {
							if ($val) $classes[] = $key;
						}

						continue;
					}

					$classes[] = $arg;
				}

				return implode($classes, ' ');

			}, array('is_variadic' => true)));

			$twig->addFunction(new \Twig_SimpleFunction('is_right_before', function($block) {

				$current = CortexBlock::get_current_block();

				if ($current == null) {
					return false;
				}

				return self::is_type_of_block(self::get_block_template_after(
					$current->get_document(),
					$current->get_id()
				), $block);

			}));

			$twig->addFunction(new \Twig_SimpleFunction('is_right_after', function($block) {

				$current = CortexBlock::get_current_block();

				if ($current == null) {
					return false;
				}

				return self::is_type_of_block(self::get_block_template_before(
					$current->get_document(),
					$current->get_id()
				), $block);

			}));

			// deprecated
			$twig->addFunction(new \Twig_SimpleFunction('image', function($image, $resizeW = null, $resizeH = null, $format = null) {

				if (empty($image)) {
					return;
				}

				$image = new \TimberImage($image);

				if ($resizeW != null ||
					$resizeH != null) {

					$w = $image->width();
					$h = $image->height();

					if ($w > $resizeW || $h > $resizeH) {
						Cortex_Public::$resizing_image = true;
						$image = \TimberImageHelper::resize($image, $resizeW, $resizeH);
						Cortex_Public::$resizing_image = false;
					}
				}

				switch ($format) {
					case 'url': return sprintf('url(%s);', $image);
					case 'src': return sprintf('src="%s"', $image);
				}

				return $image;

			}));

			$twig->addFunction(new \Twig_SimpleFunction('render_block', function($template, $post_data, $meta_data) {

				self::render_block(
					$template,
					$post_data,
					$meta_data
				);

			}));

			$twig->addFilter(new \Twig_SimpleFilter('resized', function($image, $resizeW = null, $resizeH = null, $format = null) {

				if (empty($image)) {
					return;
				}

				$image = new \TimberImage($image);

				if ($resizeW != null ||
					$resizeH != null) {

					$w = $image->width();
					$h = $image->height();

					if (($resizeW && $w > $resizeW) ||
						($resizeH && $h > $resizeH)) {
						Cortex_Public::$resizing_image = true;
						$image = \TimberImageHelper::resize($image, $resizeW, $resizeH);
						Cortex_Public::$resizing_image = false;
					}
				}

				switch ($format) {
					case 'url': return sprintf('url(%s);', $image);
					case 'src': return sprintf('src="%s"', $image);
				}

				return $image;

			}));

			return $twig;

		});
	}

	/**
	 * Loads and store the block templates.
	 * @method load_block_templates
	 * @since 0.1.0
	 */
	protected function load_block_templates() {

		$this->for_each_location($this->get_block_locations(), '*', function($path) {

			$guid = str_replace(WP_CONTENT_DIR, '', $path);

			$data = array_merge(

				array(
					'name' => '',
					'hint' => '',
					'icon' => '',
					'group' => 'All',
					'class' => 'CortexBlock',
					'order' => 10,
					'block_type' => 'twig',
					'style_type' => 'scss',
					'disabled' => false
				),

				$this->get_json($path, 'block')

			);

			if ($data['group'] === '') {
				$data['group'] = 'all';
			}

			if (strtolower($data['group']) === 'all' ||
				strtolower($data['group']) === 'layout') {
				$data['group'] = ucfirst(strtolower($data['group']));
			}

			if ($this->has_json($path, 'fields') === false) {
				return;
			}

			$fields = $this->get_json($path, 'fields');

			$template = new CortexBlockTemplate(
				$guid,
				$path,
				$data['name'],
				$data['icon'],
				$data['hint'],
				$data['group'],
				$data['class']
			);

			$template->set_order($data['order']);
			$template->set_block_file_type($data['block_type']);
			$template->set_style_file_type($data['style_type']);
			$template->set_fields($fields);

			self::$block_templates[$guid] = $template;

		});

		uksort(self::$block_templates, function($a, $b) {

			$a = self::$block_templates[$a];
			$b = self::$block_templates[$b];

			if ($a->get_order() === $b->get_order()) {
				return strcmp(
					$a->get_name(),
					$b->get_name()
				);
			}

			return $a->get_order() > $b->get_order() ? 1 : -1;

		});
	}

	/**
	 * Loads and store the block groups.
	 * @method load_block_groups
	 * @since 0.1.0
	 */
	protected function load_block_groups() {

		$groups = array();

		foreach ($this->get_block_templates() as $template) {

			$group = strtolower($template->get_group());

			if ($group == 'all' ||
				$group == 'layout') {
				continue;
			}

			$groups[] = $template->get_group();
		}

		self::$block_groups = array_unique($groups);
	}

	/**
	 * Initializes the plugin at the appropriate time.
	 * @method init
	 * @since 0.1.0
	 */
	public function init() {
		if ($this->initialized == false) {
			$this->initialized = true;
			$this->load_block_templates();
			$this->load_block_groups();
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 * @method run
	 * @since 0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Returns the plugin loader.
	 * @method get_loader
	 * @since 0.1.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Returns the plugin name.
	 * @method get_plugin_name
	 * @since 0.1.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Returns the plugin version.
	 * @method get_plugin_version
	 * @since 0.1.0
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	//--------------------------------------------------------------------------
	// ACF Specifics
	//--------------------------------------------------------------------------

	/**
	 * Returns the fields to display.
	 * @methods acf_get_field_groups
	 * @sine 0.1.0
	 */
	public function acf_get_field_groups($field_groups) {

		global $post;

		if (get_post_type() === 'cortex-block') {

			$block = $this->get_block($post->post_parent, $post->ID);

			if ($block == null) {
				return $field_groups;
			}

			$block_template_fields_key = $block->get_template()->get_fields();
			$block_template_fields_key = $block_template_fields_key['key'];

			foreach ($field_groups as $field_group) {

				if ($field_group['key'] === $block_template_fields_key) {

					$field_group['style'] = 'seamless';
					$field_group['position'] = 'normal';
					$field_group['location'] = array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'cortex-block'
							),
						)
					);

					return array($field_group);
				}
			}
		}

		return $field_groups;
	}

	//--------------------------------------------------------------------------
	// WPML Specifics
	//--------------------------------------------------------------------------

	/**
	 * Returns the fields to display.
	 * @methods acf_get_field_groups
	 * @sine 0.1.0
	 */
	public function icl_make_duplicate($src_document, $lang, $post_data, $dst_document) {
		$this->init();
		self::copy_blocks($src_document, $dst_document, true);
	}

	//--------------------------------------------------------------------------
	// Private API
	//--------------------------------------------------------------------------

	/**
	 * @method for_each_location
	 * @since 0.1.0
	 * @hidden
	 */
	private function for_each_location($locations, $type, $callback) {

		if (is_array($locations)) {

			foreach ($locations as $location) {
				$this->for_each_location($location, $type, $callback);
			}

			return;
		}

		foreach (glob($locations . '/' . $type, GLOB_ONLYDIR) as $location) $callback($location);
	}

	/**
	 * @method has_json
	 * @since 0.1.0
	 * @hidden
	 */
	private function has_json($path, $file) {
		return is_readable($path . '/' . $file . '.json');
	}

	/**
	 * @method get_json
	 * @since 0.1.0
	 * @hidden
	 */
	private function get_json($path, $file) {

		$contents = @file_get_contents($path . '/' . $file . '.json');

		if ($contents == null) {
			return array();
		}

		$contents = json_decode($contents, true);

		return empty($contents) ? array() : $contents;
	}
}

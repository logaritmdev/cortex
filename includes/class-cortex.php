<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-type.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-list.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-renderer.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-twig-renderer.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cortex-block-blade-renderer.php';

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
	 * The groups.
	 * @since 2.0.0
	 * @hidden
	 */
	private static $groups = array();

	/**
	 * The blocks.
	 * @since 2.0.0
	 * @hidden
	 */
	private static $blocks = array();

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

		if (self::session_has($key) === false) {
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

		if (self::session_has($key) === false) {
			return $default;
		}

		$value = self::session_get($key, $default);

		unset($_SESSION[$key]);

		return $value;
	}

	/**
	 * Convenience method to render a block with specific data.
	 * @method render_block
	 * @since 0.1.0
	 */
	public static function render_block($type, $data = array()) {

		global $post;

		$block = self::get_block($type);

		if ($block === null) {
			return;
		}

		$block->enqueue_styles();
		$block->enqueue_scripts();
		$block->display(0, $post, $data);
	}

	/**
	 * Convenience method to render a php template.
	 * @method render_template
	 * @since 2.0.0
	 */
	public static function render_template($template, array $vars = array(), $return = false) {

		if ($return) {
			ob_start();
		}

		extract($vars);

		include __DIR__ . '/../views/' . $template;

		if ($return) {
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
	}

	/**
	 * Returns the groups where blocks are categorized.
	 * @method get_groups
	 * @since 2.0.0
	 */
	public static function get_groups() {
		return self::$groups;
	}

	/**
	 * Indicates whether the specified block exists.
	 * @method has_block
	 * @since 2.0.0
	 */
	public static function has_block($type) {
		return isset(self::$blocks[$type]);
	}

	/**
	 * Returns a block template.
	 * @method get_block
	 * @since 2.0.0
	 */
	public static function get_block($type) {
		return isset(self::$blocks[$type]) ? self::$blocks[$type] : null;
	}

	/**
	 * Returns all block templates.
	 * @method get_blocks
	 * @since 2.0.0
	 */
	public static function get_blocks() {
		return self::$blocks;
	}

	/**
	 * Indicates whether a block exists template using its ACF id.
	 * @method has_block_by_id
	 * @since 2.0.0
	 */
	public static function has_block_by_id($id) {

		$id = (int) $id;

		foreach (acf_get_field_groups() as $group) {

			if (isset($group['ID']) && $group['ID'] === $id) {

				$block_type = acf_maybe_get($group, '@block_type');
				$block_name = acf_maybe_get($group, '@block_name');

				if ($block_type &&
					$block_name) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns a block template using its ACF id.
	 * @method get_block_by_id
	 * @since 2.0.0
	 */
	public static function get_block_by_id($id) {

		$id = (int) $id;

		foreach (acf_get_field_groups() as $group) {

			if (isset($group['ID']) && $group['ID'] === $id) {

				$block_type = acf_maybe_get($group, '@block_type');
				$block_name = acf_maybe_get($group, '@block_name');

				if ($block_type &&
					$block_name) {
					return self::get_block($block_type);
				}
			}
		}

		return null;
	}

	/**
	 * Returns the absolute locations where blocks are stored.
	 * @method get_block_locations
	 * @since 2.0.0
	 */
	public static function get_block_locations() {
		return apply_filters('cortex/block_locations', array(WP_PLUGIN_DIR . '/cortex/blocks', get_template_directory() . '/blocks'));
	}

	/**
	 * Returns the relative locations where blocks are stored.
	 * @method get_relative_block_locations
	 * @since 2.0.0
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
	 * @method create_block_folder
	 * @since 2.0.0
	 */
	public static function create_block_folder($location, $name, $block_type, $style_type) {

		$path = '';

		foreach (Cortex::get_block_locations() as $folder) {

			$base = str_replace(WP_CONTENT_DIR, '', $folder);

			if ($base === $location) {
				$path = $folder;
				break;
			}
		}

		if ($path === '') {
			trigger_error('Cannot create block template at invalid location ' . $location);
			return;
		}

		$dest = "$path/$name";

		if (is_dir($dest) === false) {

			mkdir($dest);
			mkdir("$dest/assets");
			touch("$dest/assets/scripts.js");
			touch("$dest/assets/styles.css");
			touch("$dest/block.json");
			touch("$dest/fields.json");

			switch ($block_type) {

				case 'twig':
					touch("$dest/block.twig");
					break;

				case 'blade':
					touch("$dest/block.blade.php");
					break;
			}

			switch ($style_type) {

				case 'sass':
					touch("$dest/assets/styles.scss");
					break;

				case 'less':
					touch("$dest/assets/styles.less");
					break;
			}
		}

		return $dest;
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

		$this->load_dependencies();

		$this->set_locale();

		$this->define_settings();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_twig_hooks();

		$this->loader->add_action('acf/init', $this, 'load');
		$this->loader->add_action('acf/get_field_groups', $this, 'load_field_groups', 30);
		$this->loader->add_filter('block_categories', $this, 'register_categories', 10, 2);
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
		$this->loader->add_action('admin_init', $plugin_admin, 'synchronize');
		$this->loader->add_action('admin_init', $plugin_admin, 'configure_meta_box');
		$this->loader->add_action('admin_menu', $plugin_admin, 'configure_menu');
		$this->loader->add_action('admin_head', $plugin_admin, 'configure_ui', 30);
		$this->loader->add_action('admin_footer', $plugin_admin, 'configure_footer');
		$this->loader->add_action('save_post', $plugin_admin, 'save_post');
		$this->loader->add_action('admin_notices', $plugin_admin, 'display_notices');

		$this->loader->add_action('wp_ajax_get_block_file_date', $plugin_admin, 'get_block_file_date');
		$this->loader->add_action('wp_ajax_get_block_file_data', $plugin_admin, 'get_block_file_data');
		$this->loader->add_action('wp_ajax_render_block', $plugin_admin, 'render_block');
		$this->loader->add_action('wp_ajax_nopriv_render_block', $plugin_admin, 'render_block');

		$this->loader->add_filter('admin_body_class', $plugin_admin, 'configure_body_classes', 40);
		$this->loader->add_filter('the_title', $plugin_admin, 'filter_acf_field_group_title', 40, 2);
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
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_block_assets', 40);
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
	 * Registers twig functions and filters.
	 * @method define_twig_hooks
	 * @since 0.1.0
	 */
	protected function define_twig_hooks() {

		add_filter('get_twig', function($twig) {

			$twig->addFunction(new \Twig_SimpleFunction('render_block', function($type, $data) {
				self::render_block($type, $data);
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
						$image = \TimberImageHelper::resize($image, $resizeW, $resizeH);
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
	 * @method load_blocks
	 * @since 0.1.0
	 */
	protected function load_blocks() {

		$this->for_each_location($this->get_block_locations(), '*', function($path) {

			$type = basename($path);

			$data = array_merge(

				array(
					'name' => '',
					'hint' => '',
					'icon' => '',
					'class' => 'CortexBlock',
					'group' => 'Common',
					'block_file_type' => 'twig',
					'style_file_type' => 'sass',
					'disabled' => false,
					'version' => '1.0.0'
				),

				$this->get_json($path, 'block')

			);

			if ($data['group'] === '') {
				$data['group'] = 'Common';
			}

			if ($this->has_json($path, 'fields') === false) {
				return;
			}

			$fields = $this->get_json($path, 'fields');

			$block = new CortexBlockType($type, $path, $data);
			$block->set_block_file_type($data['block_file_type']);
			$block->set_style_file_type($data['style_file_type']);
			$block->set_fields($fields);

			self::$blocks[$type] = $block;
		});
	}

	/**
	 * Loads and store the block groups.
	 * @method load_groups
	 * @since 2.0.0
	 */
	protected function load_groups() {
		foreach ($this->get_blocks() as $block) {
			self::$groups[sanitize_title($block->get_group())] = $block->get_group();
		}
	}

	/**
	 * Load the blocks.
	 * @method load
	 * @since 2.0.0
	 */
	public function load() {

		if ($this->initialized) {
			return;
		}

		$this->load_blocks();
		$this->load_groups();
		$this->register_blocks();

		$this->initialized = true;
	}

	/**
	 * Injects block attributes inside acf field groups.
	 * @method load_field_groups
	 * @since 2.0.0
	 */
	public function load_field_groups($groups) {

		$blocks = array();

		foreach (self::get_blocks() as $block) {

			$fields = $block->get_fields();

			$key = $fields['key'];

			if ($key === '' ||
				$key === null) {
				continue;
			}

			$blocks[$key] = $block;
		}

		foreach ($groups as &$group) {

			$key = $group['key'];

			if ($key === '' ||
				$key === null) {
				continue;
			}

			$block = isset($blocks[$key]) ? $blocks[$key] : null;

			if ($block) {

				$fields = $block->get_fields();

				$group = array_merge($group, array(

					'@block_name' => $block->get_name(),
					'@block_type' => $block->get_type(),
					'@block_hint' => $block->get_hint(),
					'@block_icon' => $block->get_icon(),

					'@block_group' => $block->get_group(),
					'@block_class' => $block->get_class(),

					'active'      => $block->is_active(),
					'hidden'      => $block->is_hidden(),
					'modified'    => $fields['modified'],

				));

				$group['location'] = array(
					array(
						array(
							'param'    => 'block',
							'operator' => '==',
							'value'    => 'acf/' . $block->get_type()
						),
					)
				);

				unset($blocks[$key]);
			}
		}

		foreach ($blocks as $block) {

			$group = array(

				'@block_name' => $block->get_name(),
				'@block_type' => $block->get_type(),
				'@block_hint' => $block->get_hint(),
				'@block_icon' => $block->get_icon(),

				'@block_group' => $block->get_group(),
				'@block_class' => $block->get_class(),

				'active'      => $block->is_active(),
				'hidden'      => $block->is_hidden(),
				'modified'    => $fields['modified']
			);

			$groups[] = array_merge(
				$block->get_fields(),
				$group
			);
		}

		return $groups;
	}

	/**
	 * Register Gutenberg categories.
	 * @method register_categories
	 * @since 2.0.0
	 */
	public function register_categories($categories, $post) {

		foreach (self::get_groups() as $slug => $title) {
			$categories[] = array('slug' => $slug, 'title' => $title);
		}

		return $categories;
	}

	/**
	 * Register Gutenberg blocks.
	 * @method register_blocks
	 * @since 2.0.0
	 */
	public function register_blocks() {

		$enqueue_style = get_option('cortex_enqueue_style_admin');
		$enqueue_script = get_option('cortex_enqueue_style_admin');

		foreach (self::get_blocks() as $block) {

			if ($block->is_hidden()) {
				continue;
			}

			$render = function($block_data, $content, $preview, $post) use($block, $enqueue_style, $enqueue_script) {

				if ($preview) {
					echo '<div class="previewed">';
				}

				$block->display($block_data['id'], $post, get_fields());

				if ($preview) {
					echo '</div>';
				}

			};

			$category = sanitize_title($block->get_group());

			acf_register_block(array(

				'name'        => $block->get_type(),
				'title'       => $block->get_name(),
				'description' => $block->get_hint(),
				'icon'        => $block->get_icon(),
				'category'    => $category,

				'enqueue_style'  => '',
				'enquele_script' => '',
				'enqueue_assets' => function() use ($block, $enqueue_style, $enqueue_script) {

					/*
					 * Unless I'm doing something wrong, it seems that blocks styles and scripts
					 * are included in the footer. On non-admin page that can be problematic so the public
					 * class enqueue_block_assets method, will handle the assets instead of this one.
					 */

					if (is_admin() && (isset($_REQUEST['action']) === false || $_REQUEST['action'] != 'render_block')) {

						if ($enqueue_style) $block->enqueue_styles();
						if ($enqueue_script) $block->enqueue_scripts();

						return;
					}

				},

				'render_callback' => $render,
			));
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

		if ($contents === null) {
			return array();
		}

		$contents = json_decode($contents, true);

		return empty($contents) ? array() : $contents;
	}
}
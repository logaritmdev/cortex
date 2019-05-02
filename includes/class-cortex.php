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
	 * Convenience method to render a block with specific data.
	 * @method render_block
	 * @since 0.1.0
	 */
	public static function render_block($type, $data = array()) {

		global $post;

		$template = self::get_block_template($type);

		if ($template == null) {
			return;
		}

		$block = $template->create_block(
			0,
			$post,
			$template
		);

		if ($block) {
			$block->get_template()->enqueue_scripts();
			$block->get_template()->enqueue_styles();
			$block->display(get_fields());
		}
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
	public static function has_block_template($type) {
		return isset(self::$block_templates[$type]);
	}

	/**
	 * Returns a block template.
	 * @method get_block_template
	 * @since 0.1.0
	 */
	public static function get_block_template($type) {
		return isset(self::$block_templates[$type]) ? self::$block_templates[$type] : null;
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

		$slug = self::generate_block_template_slug($slug);

		$path = "$path/$slug";

		@mkdir($path);
		@mkdir("$path/assets");
		@touch("$path/block.json");
		@touch("$path/block.twig");
		@touch("$path/fields.json");

		$block = array(
			'name' => $name,
			'icon' => '',
			'hint' => '',
			'group' => ''
		);

		$type = self::generate_block_template_type($path);

		$template = new CortexBlockTemplate(
			$type,
			$path,
			$block['name'],
			$block['icon'],
			$block['hint'],
			$block['group'],
			'',
			$fields
		);

		$template->update_config_file($block);

		return $template;
	}

	/**
	 * Creates a new block template folder at the specified location.
	 * @method rename_block_template_folder
	 * @since 0.1.0
	 */
	public static function rename_block_template_folder($template, $name) {

		global $wpdb;

		$slug = self::generate_block_template_slug($name);

		$old_path = $template->get_path();
		$old_type = $template->get_type();

		if (file_exists($old_path) == false) {
			return $template;
		}

		unset(self::$block_templates[$old_type]);

		$new_path = explode('/', $old_path);
		array_pop($new_path);
		$new_path = implode('/', $new_path);
		$new_path = $new_path . '/' . $slug;

		$new_type = self::generate_block_template_type($new_path);

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

		$new_template = new CortexBlockTemplate(
			$new_type,
			$new_path,
			$name,
			$template->get_icon(),
			$template->get_hint(),
			$template->get_group(),
			$template->get_class()
		);

		$new_template->set_block_file_type($template->get_block_file_type());
		$new_template->set_style_file_type($template->get_style_file_type());
		$new_template->set_fields($template->get_fields());

		self::$block_templates[$new_type] = $new_template;

		return $new_template;
	}

	/**
	 * @method generate_block_template_type
	 * @since 2.0.0
	 * @hidden
	 */
	private static function generate_block_template_type($path) {
		return basename($path);
	}

	/**
	 * @method generate_block_template_slug
	 * @since 2.0.0
	 * @hidden
	 */
	private static function generate_block_template_slug($name) {

		$name = trim($name);
		$name = preg_replace('/(?<!^)[A-Z]+/', ' $0', $name);
		$name = preg_replace('/\s+/', '-', $name);

		return sanitize_title($name);
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
		$this->define_acf_hooks();
		$this->define_twig_hooks();

		$this->loader->add_action('acf/init', $this, 'init');

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
		$this->loader->add_action('admin_init', $plugin_admin, 'configure_timber');
		$this->loader->add_action('admin_init', $plugin_admin, 'configure_meta_box');
		$this->loader->add_action('admin_menu', $plugin_admin, 'configure_menu');
		$this->loader->add_action('admin_head', $plugin_admin, 'configure_ui', 30);
		$this->loader->add_action('save_post', $plugin_admin, 'save_post');
		$this->loader->add_action('wp_loaded', $plugin_admin, 'synchronize');
		$this->loader->add_action('pre_get_posts', $plugin_admin, 'filter_field_groups');
		$this->loader->add_action('admin_notices', $plugin_admin, 'display_notices');

		$this->loader->add_action('wp_ajax_get_block_template_file_date', $plugin_admin, 'get_block_template_file_date');
		$this->loader->add_action('wp_ajax_get_block_template_file_data', $plugin_admin, 'get_block_template_file_data');
		$this->loader->add_action('wp_ajax_render_block', $plugin_admin, 'render_block');
		$this->loader->add_action('wp_ajax_nopriv_render_block', $plugin_admin, 'render_block');
		$this->loader->add_filter('admin_body_class', $plugin_admin, 'configure_body_classes');
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

		$this->loader->add_action('init', $plugin_public, 'configure_timber');
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
	 * Registers twig functions and filters.
	 * @method define_twig_hooks
	 * @since 0.1.0
	 */
	protected function define_twig_hooks() {

		add_filter('get_twig', function($twig) {

			$twig->addFunction(new \Twig_SimpleFunction('render_block', function($template, $data) {
				self::render_block($template, $data);
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

						/*
							This is a huge and hopefully temporary hack. TimberLibrary has
							some issues resizing images when the image url contains the
							site language identifier. To fix this, in that exact moment,
							we simply remove the language code from the URL.
						*/

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

			$template = new CortexBlockTemplate($type, $path, $data);
			$template->set_block_file_type($data['block_file_type']);
			$template->set_style_file_type($data['style_file_type']);
			$template->set_fields($fields);

			self::$block_templates[$type] = $template;

		});
	}

	/**
	 * Loads and store the block groups.
	 * @method load_block_groups
	 * @since 0.1.0
	 */
	protected function load_block_groups() {
		foreach ($this->get_block_templates() as $template) {
			self::$block_groups[sanitize_title($template->get_group())] = $template->get_group();
		}
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
			$this->register_blocks();
		}
	}

	/**
	 * Register the ACF blocks.
	 * @method register_blocks
	 * @since 2.0.0
	 */
	public function register_blocks() {

		$enqueue_styles = get_option('cortex_enqueue_styles_admin');
		$enqueue_scripts = get_option('cortex_enqueue_styles_admin');

		foreach (self::get_block_templates() as $template) {

			$render = function($block, $content, $preview, $post) use($template, $enqueue_styles, $enqueue_scripts) {

				if ($preview) {
					echo '<div class="previewed">';
				}

				$id = $block['id'];

				$block = $template->create_block(
					$id,
					$post,
					$template
				);

				if ($block) {
					$block->display(get_fields());
				}

				if ($preview) {
					echo '</div>';
				}


			};

			$category = sanitize_title($template->get_group());

			acf_register_block(array(

				'name'        => $template->get_type(),
				'title'       => $template->get_name(),
				'description' => $template->get_hint(),
				'icon'        => $template->get_icon(),
				'category'    => $category,

				'enqueue_style'  => '',
				'enquele_script' => '',
				'enqueue_assets' => function() use ($template, $enqueue_styles, $enqueue_scripts) {

					if (is_admin() && (isset($_REQUEST['action']) == false || $_REQUEST['action'] != 'render_block')) {

						if ($enqueue_styles) $template->enqueue_styles();
						if ($enqueue_scripts) $template->enqueue_scripts();

						return;
					}

					$template->enqueue_styles();
					$template->enqueue_scripts();
				},

				'render_callback' => $render,

			));
		}
	}

	/**
	 * Register the blocks categories.
	 * @method register_categories
	 * @since 2.0.0
	 */
	public function register_categories($categories, $post) {

		foreach (self::get_block_groups() as $slug => $title) {
			$categories[] = array('slug' => $slug, 'title' => $title);
		}

		return $categories;
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

		foreach ($field_groups as &$field_group) {

			$template = self::get_block_template(get_post_meta($field_group['ID'], '_cortex_block_type', true));

			if ($template == null) {
				continue;
			}

			$field_group['location'] = array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/' . $template->get_type()
					),
				)
			);
		}

		return $field_groups;
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

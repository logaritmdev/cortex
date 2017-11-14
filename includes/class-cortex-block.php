<?php

/**
 * Contains the data related to a block instance.
 * @class CortexBlock
 * @since 0.1.0
 */
class CortexBlock {

	//--------------------------------------------------------------------------
	// Static
	//--------------------------------------------------------------------------

	/**
	 * @property current
	 * @since 1.0.0
	 * @hidden
	 */
	private static $current = null;

	/**
	 * Returns the block being rendered.
	 * @method get_current_block
	 * @since 1.0.0
	 */
	public static function get_current_block() {
		return self::$current;
	}

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * The block's id.
	 * @property id
	 * @since 0.1.0
	 */
	private $id = 0;

	/**
	 * The block's revision id;
	 * @property parent_region
	 * @since 0.1.0
	 */
	private $revision = 0;

	/**
	 * The block's document.
	 * @property document
	 * @since 0.1.0
	 */
	private $document = 0;

	/**
	 * The block's template.
	 * @property template
	 * @since 0.1.0
	 */
	private $template = null;

	/**
	 * The block's parent layout.
	 * @property parent_layout
	 * @since 0.1.0
	 */
	private $parent_layout = 0;

	/**
	 * The block's parent region.
	 * @property parent_region
	 * @since 0.1.0
	 */
	private $parent_region = '';

	/**
	 * The block raw post id used to display this block without an actual post.
	 * @property raw_id
	 * @since 1.0.0
	 */
	private $raw_id = null;

	/**
	 * The block raw post used to display this block without an actual post.
	 * @property raw_post_data
	 * @since 1.0.0
	 */
	private $raw_post_data = null;

	/**
	 * The block raw post data used to display this block without an actual post.
	 * @property raw_meta_data
	 * @since 1.0.0
	 */
	private $raw_meta_data = null;

	/**
	 * The block's instance id.
	 * @property instance_id
	 * @since 1.0.0
	 */
	private $instance_id = 0;

	//--------------------------------------------------------------------------
	// Accessors
	//--------------------------------------------------------------------------

	/**
	 * Returns the block id.
	 * @method get_id
	 * @since 0.1.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the block post revision.
	 * @method get_revision
	 * @since 0.1.0
	 */
	public function get_revision() {
		return $this->revision;
	}

	/**
	 * Assigns the block post revision.
	 * @method set_revision
	 * @since 0.1.0
	 */
	public function set_revision($revision) {
		$this->revision = (int) $revision;
	}

	/**
	 * Returns the block template.
	 * @method get_template
	 * @since 0.1.0
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Returns the block document.
	 * @method get_document
	 * @since 0.1.0
	 */
	public function get_document() {
		return $this->document;
	}

	/**
	 * @method set_document
	 * @since 0.1.0
	 * @hidden
	 */
	public function set_document($document) {
		$this->document = (int) $document;
	}

	/**
	 * Returns the block parent layout.
	 * @method get_parent_layout
	 * @since 0.1.0
	 */
	public function get_parent_layout() {
		return $this->parent_layout;
	}

	/**
	 * Assigns the block parent layout.
	 * @method set_parent_layout
	 * @since 0.1.0
	 */
	public function set_parent_layout($parent_layout) {
		$this->parent_layout = (int) $parent_layout;
	}

	/**
	 * Returns the block parent region.
	 * @method get_parent_region
	 * @since 0.1.0
	 */
	public function get_parent_region() {
		return $this->parent_region;
	}

	/**
	 * Assigns the block parent region.
	 * @method set_parent_region
	 * @since 0.1.0
	 */
	public function set_parent_region($parent_region) {
		$this->parent_region = (string) $parent_region;
	}

	/**
	 * Indicates whether this block is the parent of a specified block.
	 * @method is_parent_of
	 * @since 0.1.0
	 */
	public function is_parent_of($block) {
		return $block->get_parent_layout() == $this->id;
	}

	/**
	 * Indicates whether this block is the child of a specified block.
	 * @method is_parent_of
	 * @since 0.1.0
	 */
	public function is_child_of($block) {
		return $this->parent_layout == $block->get_id();
	}

	/**
	 * @method set_revision
	 * @since 0.1.0
	 * @hidden
	 */
	private function set_id($id) {
		$this->id = (int) $id;
	}

	/**
	 * @method set_template
	 * @since 0.1.0
	 * @hidden
	 */
	private function set_template($template) {
		$this->template = $template;
	}

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the block.
	 * @constructor
	 * @since 0.1.0
	 */
	public function __construct($id, $document, $template, $parent_layout, $parent_region) {

		$this->instance_id = uniqid();

		$this->set_id($id);
		$this->set_document($document);
		$this->set_template($template);
		$this->set_parent_layout($parent_layout);
		$this->set_parent_region($parent_region);

		add_filter('timber_post_get_meta_field', array($this, 'timber_post_get_meta_field'), 20, 4);
	}

	/**
	 * Renders the main template of this block.
	 * @method display
	 * @since 0.1.0
	 */
	public function display($post_data = null, $meta_data = null) {

		self::$current = $this;

		$context = Timber::get_context();

		$id = $this->id;

		if (is_admin() === false && is_preview() === false && $this->revision) {
			$id = $this->revision;
		}

		if ($post_data ||
			$meta_data) {

			$post_data = $post_data ? $post_data : array();
			$meta_data = $meta_data ? $meta_data : array();

			$context['post'] = $this->load_raw_post(
				$post_data,
				$meta_data
			);

		} else {
			$context['post'] = new TimberPost($id);
		}

		$context['document'] = $this->document;
		$context['template'] = $this->template;
		$context['block'] = $this;

		$this->render_template('block.twig', $this->render($context));

		self::$current = null;
	}

	/**
	 * Renders the preview template of this block.
	 * @method preview
	 * @since 0.1.0
	 */
	public function preview() {

		self::$current = $this;

		$context = Timber::get_context();

		$context['post'] = new TimberPost($this->id);
		$context['document'] = $this->document;
		$context['template'] = $this->template;
		$context['block'] = $this;

		$this->render_template('preview.twig', $this->render($context));

		self::$current = null;
	}

	/**
	 * Renders this block.
	 * @method render
	 * @since 0.1.0
	 */
	public function render($context) {
		return $context;
	}

	/**
	 * Enqueue styles file.
	 * @method enqueue_style
	 * @since 0.1.0
	 */
	public function enqueue_styles() {

	}

	/**
	 * Enqueue scripts file.
	 * @method enqueue_scripts.
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

	}

	//--------------------------------------------------------------------------
	// ACF Shortcuts
	//--------------------------------------------------------------------------

	/**
	 * Convenience method to retrieve the value of a specified field.
	 * @method get_field.
	 * @since 0.1.0
	 */
	public function get_field($field) {
		return $this->raw_id ? $this->get_raw_meta_data($field) : get_field($field, $this->id);
	}

	//--------------------------------------------------------------------------
	// Private API
	//--------------------------------------------------------------------------

	/**
	 * @method preview_region
	 * @since 0.1.0
	 */
	public function preview_region($region) {

		$layout = $this->id;

		$blocks = array_filter(Cortex::get_blocks($this->document), function($block) use ($layout, $region) {

			if ($block->get_template()->is_active() == false) {
				return false;
			}

			if ($block->get_parent_layout() === $layout &&
				$block->get_parent_region() === $region) {
				return true;
			}

			return false;

		});

		$context = Timber::get_context();
		$context['layout'] = $layout;
		$context['region'] = $region;
		$context['blocks'] = $blocks;

		Timber::render('cortex-block-list-item-region.twig', $context);
	}

	/**
	 * @method display_region
	 * @since 0.1.0
	 */
	public function display_region($region) {
		foreach (Cortex::get_blocks($this->document) as $block) {
			if ($block->get_template()->is_active() &&
				$block->get_parent_layout() === $this->id &&
				$block->get_parent_region() === $region) {
				$block->display();
			}
		}
	}

	/**
	 * @function render_template
	 * @since 0.1.0
	 * @hidden
	 */
	private function render_template($file, $context) {
		$locations = Timber::$locations;
		array_unshift(Timber::$locations, $this->template->get_path());
		Timber::render($file, $context);
		Timber::$locations = $locations;
	}

	/**
	 * @function load_raw_post
	 * @since 1.0.0
	 * @hidden
	 */
	private function load_raw_post($post_data, $meta_data) {

		static $fake_id = 0;

		$fake_id++;

		$this->raw_id = $fake_id;
		$this->raw_post_data = $post_data;
		$this->raw_meta_data = $meta_data;

		$post = new TimberPost(false);
		$post->ID = $this->raw_id;
		$post->id = $this->raw_id;

		foreach ($this->raw_post_data as $key => $val) {
			$post->$key = $val;
		}

		return $post;
	}

	/**
	 * @function has_raw_meta_data
	 * @since 1.0.0
	 * @hidden
	 */
	protected function has_raw_meta_data($field) {
		return isset($this->raw_meta_data[$field]);
	}

	/**
	 * @function get_raw_meta_data
	 * @since 1.0.0
	 * @hidden
	 */
	protected function get_raw_meta_data($field, $default = null) {
		return $this->has_raw_meta_data($field) ? $this->raw_meta_data[$field] : $default;
	}

	//--------------------------------------------------------------------------
	// Hooks
	//--------------------------------------------------------------------------

	/**
	 * @function timber_post_get_meta_field
	 * @since 1.0.0
	 * @hidden
	 */
	public function timber_post_get_meta_field($value, $id, $field, $post) {

		if ($this->raw_id == $id) {
			return $this->get_raw_meta_data($field);
		}

		return $value;
	}

	//--------------------------------------------------------------------------
	// Twig
	//--------------------------------------------------------------------------

	public function getid() { return $this->get_id(); }
	public function gettemplate() { return $this->get_template(); }
	public function getdocument() { return $this->get_document(); }
	public function getparent_layout() { return $this->get_parent_layout(); }
	public function getparent_region() { return $this->get_parent_region(); }
	public function getinstance_id() { return $this->instance_id; }
}
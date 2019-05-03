<?php

// TODO
// Finish this

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
	 * Enqueue styles file.
	 * @method enqueue_style
	 * @since 0.1.0
	 */
	public static function enqueue_styles() {

	}

	/**
	 * Enqueue scripts file.
	 * @method enqueue_scripts.
	 * @since 0.1.0
	 */
	public static function enqueue_scripts() {

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
	 * The block's post.
	 * @property post
	 * @since 0.1.0
	 */
	private $post = 0;

	/**
	 * The block's template.
	 * @property template
	 * @since 0.1.0
	 */
	private $template = null;

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
	 * @method set_id
	 * @since 0.1.0
	 * @hidden
	 */
	private function set_id($id) {
		$this->id = $id;
	}

	/**
	 * Returns the block post.
	 * @method get_post
	 * @since 2.0.0
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * @method set_post
	 * @since 2.0.0
	 * @hidden
	 */
	public function set_post($post) {
		$this->post = $post;
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
	public function __construct($id, $post, $template) {
		$this->set_id($id);
		$this->set_post($post);
		$this->set_template($template);
	}

	/**
	 * Renders the main template of this block.
	 * @method display
	 * @since 0.1.0
	 */
	public function display(array $data = array()) {

		$type = $this->template->get_block_file_type();

		if ($type == 'twig') {
			$this->render_twig_template($data);
			return;
		}

		if ($type == 'blade') {
			$this->render_blade_template($data);
			return;
		}
	}

	/**
	 * Renders this block.
	 * @method render
	 * @since 0.1.0
	 */
	public function render($data) {
		return $data;
	}

	//--------------------------------------------------------------------------
	// Private API
	//--------------------------------------------------------------------------

	/**
	 * @method get_link
	 * @since 0.2.0
	 */
	public function get_link() {

		$block_url = admin_url('admin-ajax.php') . '?action=render_block&post=' . $this->post . '&id=' . $this->id;
		$block_lng = apply_filters('wpml_current_language', NULL);

		if ($block_lng) {
			$block_url = add_query_arg('lang', $block_lng, $block_url);
		}

		return $block_url;
	}

	/**
	 * @function render_twig_template
	 * @since 0.1.0
	 * @hidden
	 */
	protected function render_twig_template(array $data = array()) {

		$locations = Timber::$locations;

		array_unshift(Timber::$locations, $this->template->get_path());

		$context = Timber::get_context();
		$context['block'] = $this;

		if ($data) {
			$context = array_merge($context, $data);
		}

		Timber::render('block.twig', $this->render($context));

		Timber::$locations = $locations;
	}

	/**
	 * @function render_blade_template
	 * @since 2.0.0
	 * @hidden
	 */
	protected function render_blade_template($file, $context) {
		sage('blade')->render($file, $context);
	}

	//--------------------------------------------------------------------------
	// Template
	//--------------------------------------------------------------------------

	public function getId() { return $this->get_id(); }
	public function getLink() { return $this->get_link(); }
	public function getPost() { return $this->get_post(); }
	public function getTemplate() { return $this->get_template(); }
}
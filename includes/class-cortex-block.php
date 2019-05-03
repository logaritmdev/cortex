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
	 * The renderer classes.
	 * @method renderers
	 * @since 2.0.0
	 */
	public static $renderers = array(
		'twig'  => 'CortexBlockTwigRenderer',
		'blade' => 'CortexBlockBladeRenderer'
	);

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

	/**
	 * Returns the specified renderer.
	 * @method get_renderer.
	 * @since 2.0.0
	 */
	private static function get_renderer($type, $self) {
		return isset(self::$renderers[$type]) ? new self::$renderers[$type]($self) : null;
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
	 * The block's type.
	 * @property type
	 * @since 2.0.0
	 */
	private $type = null;

	/**
	 * The block's renderer.
	 * @property renderer
	 * @since 0.1.0
	 */
	private $renderer = null;

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
	 * Returns the block type.
	 * @method get_type
	 * @since 0.1.0
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @method set_type
	 * @since 0.1.0
	 * @hidden
	 */
	private function set_type($type) {
		$this->type = $type;
	}

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the block.
	 * @constructor
	 * @since 0.1.0
	 */
	public function __construct($id, $post, CortexBlockType $type) {
		$this->set_id($id);
		$this->set_post($post);
		$this->set_type($type);
	}

	/**
	 * Renders the main type of this block.
	 * @method display
	 * @since 0.1.0
	 */
	public function display(array $vars = array()) {

		if ($this->renderer == null) {
			$this->renderer = self::get_renderer(
				$this->type->get_block_file_type(),
				$this
			);
		}

		if ($this->renderer) {
			$this->renderer->render($this->render($vars));
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
	 * @since 2.0.0
	 * @hidden
	 */
	public function get_link() {
		return $this->append_lang(admin_url('admin-ajax.php') . '?action=render_block&post=' . $this->post . '&id=' . $this->id);
	}

	/**
	 * @function render_twig_type
	 * @since 0.1.0
	 * @hidden
	 */
	protected function render_twig_type(array $data = array()) {

	}

	/**
	 * @function render_blade_type
	 * @since 2.0.0
	 * @hidden
	 */
	protected function render_blade_type($file, $context) {
		sage('blade')->render($file, $context);
	}

	//--------------------------------------------------------------------------
	// WPML
	//--------------------------------------------------------------------------

	/**
	 * @function append_lang
	 * @since 2.0.0
	 * @hidden
	 */
	protected function append_lang($link) {
		return (($lang = apply_filters('wpml_current_language', null)) == null) ? $link : add_query_arg('lang', $lang, $link);
	}

	//--------------------------------------------------------------------------
	// type
	//--------------------------------------------------------------------------

	public function getId() { return $this->get_id(); }
	public function getLink() { return $this->get_link(); }
	public function getPost() { return $this->get_post(); }
	public function gettype() { return $this->get_type(); }
}
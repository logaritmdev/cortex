<?php

/**
 * Contains the data related to a block instance.
 * @class CortexBlock
 * @since 0.1.0
 */
class CortexBlock {

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

	/**
	 * @method set_document
	 * @since 0.1.0
	 * @hidden
	 */
	private function set_document($document) {
		$this->document = (int) $document;
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
		$this->set_id($id);
		$this->set_document($document);
		$this->set_template($template);
		$this->set_parent_layout($parent_layout);
		$this->set_parent_region($parent_region);
	}

	/**
	 * Renders the main template of this block.
	 * @method display
	 * @since 0.1.0
	 */
	public function display() {

		$context = Timber::get_context();

		$id = $this->id;

		if (is_admin() === false && is_preview() === false && $this->revision) {
			$id = $this->revision;
		}

		$context['post'] = new TimberPost($id);
		$context['document'] = $this->document;
		$context['template'] = $this->template;
		$context['block'] = $this;

		$this->render_template('block.twig', $this->render($context));
	}

	/**
	 * Renders the preview template of this block.
	 * @method preview
	 * @since 0.1.0
	 */
	public function preview() {

		$context = Timber::get_context();

		$context['post'] = new TimberPost($this->id);
		$context['document'] = $this->document;
		$context['template'] = $this->template;
		$context['block'] = $this;

		$this->render_template('preview.twig', $this->render($context));
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

	protected function get_field($field) {
		return get_field($field, $this->id);
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

	//--------------------------------------------------------------------------
	// Twig
	//--------------------------------------------------------------------------

	public function getid() { return $this->get_id(); }
	public function gettemplate() { return $this->get_template(); }
	public function getdocument() { return $this->get_document(); }
	public function getparent_layout() { return $this->get_parent_layout(); }
	public function getparent_region() { return $this->get_parent_region(); }
}
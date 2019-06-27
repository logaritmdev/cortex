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
	 * Renders this block.
	 * @method render
	 * @since 0.1.0
	 */
	public function render($data) {
		return $data;
	}

	/**
	 * Renders the main type of this block.
	 * @method display
	 * @since 0.1.0
	 */
	public function display($vars = null) {

		if ($vars == null ||
			$vars == false) {
			$vars = array();
		}

		if ($this->renderer === null) {
			$this->renderer = self::get_renderer(
				$this->type->get_block_file_type(),
				$this
			);
		}

		$vars = $this->render($vars);

		if ($vars === null) {
			trigger_error('The block render method must return an array, ' . $vars . ' given');
		}

		if ($this->renderer) {
			$this->renderer->render($vars);
		}
	}

	/**
	 * Renders a preview of this block
	 * @method preview
	 * @since 2.0.0
	 */
	public function preview($vars = null) {

		if (get_option('cortex_generate_previews') == false) {
			$this->display($vars);
			return;
		}

		$hash = CortexPreview::generate_preview_hash($this->id, $this->post, $vars);

		$preview_hash = CortexPreview::get_preview_hash($this->id, $this->post);
		$preview_size = CortexPreview::get_preview_size($this->id, $this->post);
		$preview_src  = CortexPreview::get_preview_src($this->id, $this->post);
		$preview_url  = CortexPreview::get_preview_url($this->id, $this->post);

		if ($preview_hash != $hash || is_readable($preview_src) == false) {

			$preview_hash = $hash;
			$preview_url  = '';
			$preview_src  = '';

			/*
			 * This will set the preview data to render the preview with. If
			 * we don't don this, the preview will use the previously saved
			 * data which might be out of sync with the current data.
			 */

			CortexPreview::set_preview_vars($this->id, $this->post, $vars);
		}

		$preview_w = $preview_size[0];
		$preview_h = $preview_size[1];
		$ratio = 0;

		if ($preview_w &&
			$preview_h) {
			$ratio = $preview_h / $preview_w * 100;
		}

		?>
			<div
				class="cortex-preview"
				data-hash="<?php echo $hash ?>"
				data-preview-width="<?php echo $preview_w ?>"
				data-preview-height="<?php echo $preview_h ?>">

				<?php if ($preview_url): ?>

					<div class="cortex-preview-image" style="background-image: url('<?php echo $preview_url ?>')"></div>

				<?php else: ?>

					<script type="text/javascript">
						(function() {
							Cortex.generatePreview("<?php echo $this->id ?>", "<?php echo $this->post ?>", "<?php echo $hash ?>", "<?php echo $this->get_preview_link() ?>");
						})(jQuery);
					</script>

					<div class="cortex-preview-spinner">

					</div>

				<?php endif ?>

			</div>



		<?php
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
		return $this->type->get_link();
	}

	/**
	 * @method get_preview_link
	 * @since 2.0.0
	 * @hidden
	 */
	public function get_preview_link() {
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
		return (($lang = apply_filters('wpml_current_language', null)) === null) ? $link : add_query_arg('lang', $lang, $link);
	}

	//--------------------------------------------------------------------------
	// type
	//--------------------------------------------------------------------------

	public function getId() {
		return $this->get_id();
	}

	public function getLink() {
		return $this->get_link();
	}

	public function getPreviewLink() {
		return $this->get_preview_link();
	}

	public function getPost() {
		return $this->get_post();
	}

	public function getType() {
		return $this->get_type();
	}
}
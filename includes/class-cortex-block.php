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
	 * The block's attributes.
	 * @property attributes
	 * @since 2.0.0
	 */
	private $attributes = [];

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
	// Convenience
	//--------------------------------------------------------------------------

	/**
	 * Returns the block definition's name.
	 * @method get_block_name
	 * @since 2.1.1
	 */
	public function get_block_name() {
		return $this->type->get_type();
	}

	/**
	 * Returns the block definition's type.
	 * @method get_block_type
	 * @since 2.1.1
	 */
	public function get_block_type() {
		return $this->type->get_type();
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

		$base = 'block';
		$name = $type->get_type();

		$this->attributes = $this->type->get_attributes();
		$this->attributes['class'] = $this->type->get_attribute('class', []);
		$this->attributes['style'] = $this->type->get_attribute('style', []);

		array_unshift($this->attributes['class'], $name);
		array_unshift($this->attributes['class'], $base);
	}

	/**
	 * Appends a CSS class to the block.
	 * @method append_class
	 * @since 2.1.0
	 */
	public function append_class($class, $condition = true) {
		if ($condition) $this->do_append_class($class);
	}

	/**
	 * Removes a CSS class from the block.
	 * @method remove_class
	 * @since 2.1.0
	 */
	public function remove_class($class, $condition = true) {
		if ($condition) $this->do_remove_class($class);
	}

	/**
	 * Appends a CSS style to the block.
	 * @method append_style
	 * @since 2.1.0
	 */
	public function append_style($style, $value, $condition = true) {
		if ($condition) $this->do_append_style($style, $value);
	}

	/**
	 * Removes a CSS style from the block.
	 * @method remove_style
	 * @since 2.1.0
	 */
	public function remove_style($style, $condition = true) {
		if ($condition) $this->do_remove_style($style);
	}

	/**
	 * Apppends an attribute from the block.
	 * @method append_attribute
	 * @since 2.1.0
	 */
	public function append_attribute($key, $val, $condition = true) {
		if ($condition) $this->do_append_attribute($key, $val);
	}

	/**
	 * Removes an attribute from the block.
	 * @method remove_attribute
	 * @since 2.1.0
	 */
	public function remove_attribute($key, $condition = true) {
		if ($condition) $this->do_remove_attribute($key, $val);
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

		$vars = apply_filters('cortex/render', $vars, $this);

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
	 * @method get_render_link
	 * @since 2.1.3
	 * @hidden
	 */
	public function get_render_link() {
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

	/**
	 * @function do_append_class
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_append_class($class) {
		$this->attributes['class'][] = $class;
	}

	/**
	 * @function do_remove_class
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_remove_class($class) {
		array_splice($this->attributes['class'], $this->get_class_index($class), 1);
	}

	/**
	 * @function do_append_style
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_append_style($style, $value) {
		$this->attributes['style'][] = [$style, $value];
	}

	/**
	 * @function do_append_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_append_attribute($key, $val) {
		$this->attributes[$key] = $val;
	}

	/**
	 * @function do_remove_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_remove_attribute($key, $val) {
		unset($this->attributes[$key]);
	}

	/**
	 * @function do_remove_style
	 * @since 2.1.0
	 * @hidden
	 */
	private function do_remove_style($style, $value) {
		array_splice($this->attributes['style'], $this->get_style_index($class), 1);
	}

	/**
	 * @function has_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function has_attribute($name) {
		return isset($this->attributes[$name]) && empty($this->attributes[$name]) == false;
	}

	/**
	 * @function get_class_index
	 * @since 2.1.0
	 * @hidden
	 */
	private function get_class_index($class) {

		foreach ($this->attributes['class'] as $index => $value) {
			if ($value == $class) return $index;
		}

		return -1;
	}

	/**
	 * @function get_style_index
	 * @since 2.1.0
	 * @hidden
	 */
	private function get_style_index($style) {

		foreach ($this->attributes['style'] as $index => $tuple) {

			$style = $tuple[0];
			$value = $tuple[1];

			if ($value == $style) return $index;
		}

		return -1;
	}

	/**
	 * @function generate_id_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_id_attribute() {

		$id = isset($this->attributes['id']) ? $this->attributes['id'] : null;

		if ($id == null) {
			if ($id = $this->get_id()) {
				$id = str_replace('block_', 'block-', $id);
			}
		}

		return $id ? $this->generate_attribute('id', $id) : null;
	}

	/**
	 * @function generate_class_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_class_attribute() {
		return $this->has_attribute('class') ? $this->generate_attribute('class', $this->attributes['class']) : null;
	}

	/**
	 * @function generate_style_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_style_attribute() {
		return $this->has_attribute('style') ? $this->generate_attribute('style', $this->attributes['style']) : null;
	}

	/**
	 * @function generate_attribute
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_attribute($key, $val) {

		if ($this->is_primitive($val)) {
			$val = htmlentities($val);
		} else if ($this->is_seq($val)) {
			$val = $this->generate_attribute_seq($val);
		} else if ($this->is_map($val)) {
			$val = $this->generate_attribute_map($val);
		}

		return sprintf('%s="%s"', $key, $val);
	}

	/**
	 * @function generate_attribute_seq
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_attribute_seq($val) {
		return implode(' ', array_filter($val));
	}

	/**
	 * @function generate_attribute_map
	 * @since 2.1.0
	 * @hidden
	 */
	private function generate_attribute_map($val) {

		$function = function($tuple) {
			return sprintf(
				'%s: %s;',
				$tuple[0],
				$tuple[1]
			);
		};

		return implode(' ', array_map($function, $val));
	}

	/**
	 * @function is_primitive
	 * @since 2.1.0
	 * @hidden
	 */
	private function is_primitive($val) {
		return is_scalar($val);
	}

	/**
	 * @function is_seq
	 * @since 2.1.0
	 * @hidden
	 */
	private function is_seq($val) {
		return is_array($val) && isset($val[0]) && is_scalar($val[0]);
	}

	/**
	 * @function is_map
	 * @since 2.1.0
	 * @hidden
	 */
	private function is_map($val) {
		return is_array($val)&& isset($val[0]) && is_array($val[0]);
	}

	//--------------------------------------------------------------------------
	// type
	//--------------------------------------------------------------------------

	/**
	 * @function id
	 * @since 2.1.3
	 * @hidden
	 */
	public function id() {
		return $this->get_id();
	}

	/**
	 * @function type
	 * @since 2.1.3
	 * @hidden
	 */
	public function type() {
		return $this->get_type();
	}

	/**
	 * @function link
	 * @since 2.1.3
	 * @hidden
	 */
	public function link() {
		return $this->get_link();
	}

	/**
	 * @function post
	 * @since 2.1.3
	 * @hidden
	 */
	public function post() {
		return Timber::get_post($this->get_post());
	}

	/**
	 * @function attributes
	 * @since 2.1.3
	 * @hidden
	 */
	public function attributes() {

		$attrs   = [];
		$attrs[] = $this->generate_id_attribute();
		$attrs[] = $this->generate_class_attribute();
		$attrs[] = $this->generate_style_attribute();

		foreach ($this->attributes as $key => $val) {

			if ($key == 'class' ||
				$key == 'style') {
				continue;
			}

			$attrs[] = $this->generate_attribute($key, $val);
		}

		return implode(' ', array_filter($attrs));
	}

	/**
	 * @function render_link
	 * @since 2.1.3
	 * @hidden
	 */
	public function render_link() {
		return $this->get_render_link();
	}

	/**
	 * @function getPreviewLink
	 * @since 1.0.0
	 * @hidden
	 */
	public function getPreviewLink() {
		// Deprecated
		return $this->get_preview_link();
	}
}
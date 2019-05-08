<?php

/**
 * Wraps the creation of a meta box.
 * @class CortexMetaBox
 * @since 0.1.0
 */
class CortexMetaBox {

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * The meta box options.
	 * @property options
	 * @since 0.1.0
	 */
	private $options = array();

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initialize and display a meta box.
	 * @constructor
	 * @since 0.1.0
	 */
	public function __construct($name, $slug, $post_type = 'post', array $options = array(), $context = 'normal', $priority = 'high') {
		$this->options = $options;
		add_meta_box($slug, $name, array($this, 'render'), $post_type, $context, $priority);
	}

	/**
	 * Loads the meta box context data.
	 * @method load
	 * @since 0.1.0
	 */
	protected function load($context) {
		return $context;
	}

	/**
	 * Returns the meta box template.
	 * @method template
	 * @since 0.1.0
	 */
	protected function template() {
		return '';
	}

	/**
	 * Renders the meta box.
	 * @method render
	 * @since 0.1.0
	 */
	public function render() {

		$context = $this->options;
		$context = $this->load($context);

		extract($context);

		include __DIR__ . '/../views/' . $this->template();
	}
}
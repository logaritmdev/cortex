<?php

/**
 * Contains the data related to a block template.
 * @class CortexBlockTemplate
 * @since 0.1.0
 */
class CortexBlockTemplate {

	const CSS_FILE_PATH = 'assets/styles.css';
	const LESS_FILE_PATH = 'assets/styles.less';
	const SCSS_FILE_PATH = 'assets/styles.scss';
	const SCRIPT_FILE_PATH = 'assets/scripts.js';

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * The block template's unique identifier.
	 * @property guid
	 * @since 0.1.0
	 */
	private $guid = null;

	/**
	 * The block template's location.
	 * @property path
	 * @since 0.1.0
	 */
	private $path = null;

	/**
	 * The block template's name.
	 * @property name
	 * @since 0.1.0
	 */
	private $name = null;

	/**
	 * The block template's icon.
	 * @property icon
	 * @since 0.1.0
	 */
	private $icon = null;

	/**
	 * The block template's hint.
	 * @property hint
	 * @since 0.1.0
	 */
	private $hint = null;

	/**
	 * The block template's group.
	 * @property group
	 * @since 0.1.0
	 */
	private $group = null;

	/**
	 * The block template's class.
	 * @property class
	 * @since 0.1.0
	 */
	private $class = null;

	/**
	 * The block template's order.
	 * @property class
	 * @since 0.1.0
	 */
	private $order = 10;

	/**
	 * The block template's fields.
	 * @property fields
	 * @since 0.1.0
	 */
	private $fields = null;

	/**
	 * The block template's block file extension.
	 * @property block_file_type
	 * @since 0.1.0
	 */
	private $block_file_type = 'twig';

	/**
	 * The block template's preview file extension.
	 * @property preview_file_type
	 * @since 0.1.0
	 */
	private $preview_file_type = 'twig';

	/**
	 * The block template's style file extension.
	 * @property style_file_type
	 * @since 0.1.0
	 */
	private $style_file_type = '';

	/**
	 * Whether the block template is active.
	 * @property active
	 * @since 0.1.0
	 */
	private $active = true;

	/**
	 * The block template verison.
	 * @property active
	 * @since 0.1.0
	 */
	private $version = '1.0.0';

	//--------------------------------------------------------------------------
	// Accessors
	//--------------------------------------------------------------------------

	/**
	 * Returns the block template's unique identifier.
	 * @method get_guid
	 * @since 0.1.0
	 */
	public function get_guid() {
		return $this->guid;
	}

	/**
	 * Returns the block template's path.
	 * @method get_path
	 * @since 0.1.0
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Returns the block template's name.
	 * @method get_name
	 * @since 0.1.0
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the block template's icon.
	 * @method get_icon
	 * @since 0.1.0
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Returns the block template's hint.
	 * @method get_hint
	 * @since 0.1.0
	 */
	public function get_hint() {
		return $this->hint;
	}

	/**
	 * Returns the block template's group.
	 * @method get_group
	 * @since 0.1.0
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Returns the block template's class.
	 * @method get_class
	 * @since 0.1.0
	 */
	public function get_class() {

		if ($this->class !== '' &&
			$this->class !== 'CortexBlock') {
			require_once $this->path . '/block.php';
			return $this->class;
		}

		return 'CortexBlock';
	}

	/**
	 * Assigns the block template's order.
	 * @method set_order
	 * @since 0.1.0
	 */
	public function set_order($order) {
		$this->order = $order;
	}

	/**
	 * Returns the block template's order.
	 * @method get_order
	 * @since 0.1.0
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * Assigns the block template's fields.
	 * @method set_fields
	 * @since 0.1.0
	 */
	public function set_fields($fields) {
		$this->fields = $fields;
	}

	/**
	 * Returns the block template's fields.
	 * @method get_fields
	 * @since 0.1.0
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Returns the block template's preview file extension.
	 * @method get_preview_file_type
	 * @since 0.1.0
	 */
	public function get_preview_file_type() {
		return $this->preview_file_type;
	}

	/**
	 * Returns the block tempalte's preview file path.
	 * @method get_preview_file_path
	 * @since 0.1.0
	 */
	public function get_preview_file_path() {
		return sprintf('%s/preview.%s', $this->path, $this->preview_file_type);
	}

	/**
	 * Returns the block template's preview file content.
	 * @method get_preview_file_content
	 * @since 0.1.0
	 */
	public function get_preview_file_content() {
		return @file_get_contents($this->get_preview_file_path());
	}

	/**
	 * Returns the block template's preview file modification date.
	 * @method get_preview_file_date
	 * @since 0.1.0
	 */
	public function get_preview_file_date() {
		return filemtime($this->get_preview_file_path());
	}

	/**
	 * Returns the block template's block file extension.
	 * @method get_block_file_type
	 * @since 0.1.0
	 */
	public function get_block_file_type() {
		return $this->block_file_type;
	}

	/**
	 * Assigns the block template's block file extension.
	 * @method set_block_file_type
	 * @since 0.1.0
	 */
	public function set_block_file_type($type) {
		$this->block_file_type = $type;
	}

	/**
	 * Returns the block tempalte's block file path.
	 * @method get_block_file_path
	 * @since 0.1.0
	 */
	public function get_block_file_path() {
		return sprintf('%s/block.%s', $this->path, $this->block_file_type);
	}

	/**
	 * Returns the block template's block file content.
	 * @method get_block_file_content
	 * @since 0.1.0
	 */
	public function get_block_file_content() {
		return @file_get_contents($this->get_block_file_path());
	}

	/**
	 * Returns the block template's block file modification date.
	 * @method get_block_file_date
	 * @since 0.1.0
	 */
	public function get_block_file_date() {
		return filemtime($this->get_block_file_path());
	}

	/**
	 * Returns the block template's style file extension.
	 * @method get_style_file_type
	 * @since 0.1.0
	 */
	public function get_style_file_type() {
		return $this->style_file_type;
	}

	/**
	 * Assigns the block template's style file extension.
	 * @method set_style_file_type
	 * @since 0.1.0
	 */
	public function set_style_file_type($type) {
		$this->style_file_type = $type;
	}

	/**
	 * Returns the block template's style file path.
	 * @method get_style_file_path
	 * @since 0.1.0
	 */
	public function get_style_file_path() {

		switch ($this->style_file_type) {
			case 'less': return sprintf('%s/%s', $this->path, self::LESS_FILE_PATH);
			case 'scss': return sprintf('%s/%s', $this->path, self::SCSS_FILE_PATH);
		}

		return sprintf('%s/%s', $this->path, self::CSS_FILE_PATH);
	}

	/**
	 * Returns block template's style file content.
	 * @method get_style_file_content
	 * @since 0.1.0
	 */
	public function get_style_file_content() {
		return @file_get_contents($this->get_style_file_path());
	}

	/**
	 * Returns block template's style file modification date.
	 * @method get_style_file_date
	 * @since 0.1.0
	 */
	public function get_style_file_date() {
		return @filemtime($this->get_style_file_path());
	}

	/**
	 * Returns the block template's script file path.
	 * @method get_script_file_path
	 * @since 0.1.0
	 */
	public function get_script_file_path() {
		return sprintf('%s/%s', $this->path, self::SCRIPT_FILE_PATH);
	}

	/**
	 * Returns the block template's script file content.
	 * @method get_script_file_content
	 * @since 0.1.0
	 */
	public function get_script_file_content() {
		return @file_get_contents($this->get_script_file_path());
	}

	/**
	 * Returns the block template's script file modification date.
	 * @method get_script_file_date
	 * @since 0.1.0
	 */
	public function get_script_file_date() {
		return @filemtime($this->get_script_file_path());
	}

	/**
	 * Returns the block template's icon url.
	 * @method get_icon_url
	 * @since 0.1.0
	 */
	public function get_icon_url() {
		return empty($this->icon) ? plugins_url('../admin/images/block-icon.png', __FILE__) : $this->get_url($this->icon);
	}

	/**
	 * Returns the block template's style file url.
	 * @method get_style_file_url
	 * @since 0.1.0
	 */
	public function get_style_file_url() {
		$path = $this->get_style_file_path();
  		return is_readable($path) && filesize($path) ? $this->get_url(self::CSS_FILE_PATH) : null;
	}

	/**
	 * Returns the block template's script file url.
	 * @method get_style_file_url
	 * @since 0.1.0
	 */
	public function get_script_file_url() {
		$path = $this->get_script_file_path();
  		return is_readable($path) && filesize($path) ? $this->get_url(self::SCRIPT_FILE_PATH) : null;
	}

	/**
	 * Returns the block template's url.
	 * @method get_url
	 * @since 0.1.0
	 */
	public function get_url($file = '') {

		if ($file) {
			$file = '/' . $file;
		}

		return str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $this->path) . $file;
	}

	/**
	 * Returns the block template's fields.json update date.
	 * @method get_date
	 * @since 0.1.0
	 */
	public function get_date() {
		return filemtime(sprintf('%s/fields.json', $this->path));
	}

	/**
	 * Returns whether the block is active.
	 * @method get_parent_region
	 * @since 0.1.0
	 */
	public function is_active() {
		return $this->active;
	}

	/**
	 * Indicates whether this block template is of the given type.
	 * @method is_type
	 * @since 1.0.0
	 */
	public function is_type($name) {
		return substr($this->guid, -strlen($name)) === $name;
	}

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the block template.
	 * @cosntructor
	 * @since 0.1.0
	 */
	public function __construct($guid, $path, $name, $icon, $hint, $group, $class) {

		static $block_status = null;

		$this->guid = $guid;
		$this->path = $path;
		$this->name = $name;
		$this->icon = $icon;
		$this->hint = $hint;
		$this->group = $group;
		$this->class = $class;

		if ($block_status == null) {
			$block_status = get_option('cortex_block_status');
		}

		$this->active = $block_status == false || !isset($block_status[$guid]) || $block_status[$guid] == 'enabled';
	}

	/**
	 * Creates a block from this template.
	 * @method create_block
	 * @since 0.1.0
	 */
	public function create_block($id = 0, $document = 0, $parent_layout = null, $parent_region = '') {
		return $this->create_block_with($this->get_class(), $id, $document, $parent_layout, $parent_region);
	}

	/**
	 * Creates a block from this template using a specific class.
	 * @method create_block_with
	 * @since 0.1.0
	 */
	public function create_block_with($class, $id = 0, $document = 0, $parent_layout = null, $parent_region = '') {
		return new $class($id, $document, $this, $parent_layout, $parent_region);
	}

	/**
	 * Updates the preview file.
	 * @method update_preview_file
	 * @since 0.1.0
	 */
	public function update_preview_file($data) {
		file_put_contents($this->get_preview_file_path(), $data);
	}

	/**
	 * Updates the block file.
	 * @method update_block_file
	 * @since 0.1.0
	 */
	public function update_block_file($data) {
		file_put_contents($this->get_block_file_path(), $data);
	}

	/**
	 * Updates the style file.
	 * @method update_style_file
	 * @since 0.1.0
	 */
	public function update_style_file($data) {

		file_put_contents($this->get_style_file_path(), $data);

		switch ($this->style_file_type) {
			case 'scss': file_put_contents($this->path . '/' . self::CSS_FILE_PATH, CortexSassCompiler::compile($data)); break;
			case 'less': file_put_contents($this->path . '/' . self::CSS_FILE_PATH, CortexLessCompiler::compile($data)); break;
		}
	}

	/**
	 * Updates the style file.
	 * @method update_script_file
	 * @since 0.1.0
	 */
	public function update_script_file($data) {
		file_put_contents($this->get_script_file_path(), $data);
	}

	/**
	 * Updates the block json file.
	 * @method update_block_json_file
	 * @since 0.1.0
	 */
	public function update_block_json_file(array $data) {
		file_put_contents("{$this->path}/block.json", acf_json_encode($data));
	}

	/**
	 * Updates the block json file name field.
	 * @method update_block_json_file_name
	 * @since 0.1.0
	 */
	public function update_block_json_file_name($name) {
		$content = file_get_contents("{$this->path}/block.json");
		$content = json_decode($content, true);
		$content['name'] = $name;
		file_put_contents("{$this->path}/block.json", acf_json_encode($content));
	}

	/**
	 * Updates the fields json file.
	 * @method update_fields_json_file
	 * @since 0.1.0
	 */
	public function update_fields_json_file(array $data) {
		file_put_contents("{$this->path}/fields.json", acf_json_encode($data));
	}

	/**
	 * Enqueue styles file.
	 * @method enqueue_style
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		if ($url = $this->get_style_file_url()) wp_enqueue_style($this->guid, $url, array(), $this->version , 'all');
	}

	/**
	 * Enqueue scripts file.
	 * @method enqueue_scripts.
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		if ($url = $this->get_script_file_url()) wp_enqueue_script($this->guid,  $url, array(), $this->version, true);
	}

	//--------------------------------------------------------------------------
	// Twig
	//--------------------------------------------------------------------------

	public function getguid()   { return $this->get_guid(); }
	public function getpath()   { return $this->get_path(); }
	public function getname()   { return $this->get_name(); }
	public function geticon()   { return $this->get_icon(); }
	public function gethint()   { return $this->get_hint(); }
	public function getgroup()  { return $this->get_group(); }
	public function getclass()  { return $this->get_class(); }
	public function getfields() { return $this->get_fields(); }
	public function geturl()    { return $this->get_url(); }
	public function getactive() { return $this->is_active(); }
}
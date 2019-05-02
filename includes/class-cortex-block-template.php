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
	 * The block template's type.
	 * @property type
	 * @since 2.0.0
	 */
	private $type = null;

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
	 * The block template's class.
	 * @property class
	 * @since 0.1.0
	 */
	private $class = null;

	/**
	 * The block template's group.
	 * @property group
	 * @since 0.1.0
	 */
	private $group = 'Common';

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
	 * The block template's style file extension.
	 * @property style_file_type
	 * @since 0.1.0
	 */
	private $style_file_type = '';

	/**
	 * The block template's verison.
	 * @property active
	 * @since 0.1.0
	 */
	private $version = '1.0.0';

	/**
	 * Whether the block template is active.
	 * @property active
	 * @since 0.1.0
	 */
	private $active = true;

	//--------------------------------------------------------------------------
	// Accessors
	//--------------------------------------------------------------------------

	/**
	 * Returns the block template's type.
	 * @method get_type
	 * @since 2.0.0
	 */
	public function get_type() {
		return $this->type;
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
	 * Returns the block template's class.
	 * @method get_class
	 * @since 0.1.0
	 */
	public function get_class() {
		return $this->class;
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
	 * Returns the block template's block file modification date.
	 * @method get_block_file_date
	 * @since 0.1.0
	 */
	public function get_block_file_date() {
		return @filemtime($this->get_block_file_path());
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
	 * Returns block template's style file modification date.
	 * @method get_style_file_date
	 * @since 0.1.0
	 */
	public function get_style_file_date() {
		return @filemtime($this->get_style_file_path());
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
	 * Returns the block template's style file url.
	 * @method get_style_file_url
	 * @since 0.1.0
	 */
	public function get_style_file_url() {
		return is_readable($this->get_style_file_path()) && filesize($this->get_style_file_path()) ? $this->get_link() . '/' . self::CSS_FILE_PATH : null;
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
	 * Returns the block template's script file modification date.
	 * @method get_script_file_date
	 * @since 0.1.0
	 */
	public function get_script_file_date() {
		return @filemtime($this->get_script_file_path());
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
 	 * Returns the block template's script file url.
 	 * @method get_style_file_url
 	 * @since 0.1.0
 	 */
	  public function get_script_file_url() {
		return is_readable($this->get_script_file_path()) && filesize($this->get_script_file_path()) ? $this->get_link() . '/' . self::SCRIPT_FILE_PATH : null;
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
	 * Returns the block template's url.
	 * @method get_link
	 * @since 2.0.0
	 */
	public function get_link() {
		return str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $this->path);
	}

	/**
	 * Returns the block template's fields.json update date.
	 * @method get_date
	 * @since 0.1.0
	 */
	public function get_date() {
		return @filemtime(sprintf('%s/fields.json', $this->path));
	}

	/**
	 * Returns whether the block is active.
	 * @method get_parent_region
	 * @since 0.1.0
	 */
	public function is_active() {
		return $this->active;
	}

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the block template.
	 * @cosntructor
	 * @since 0.1.0
	 */
	public function __construct($type, $path, $data) {

		static $blocks_status = null;

		if ($blocks_status == null) {
			$blocks_status = get_option('cortex_block_status');
		}

		$this->type = $type;
		$this->path = $path;
		$this->name = isset($data['name']) ? $data['name'] : $this->name;
		$this->icon = isset($data['icon']) ? $data['icon'] : $this->icon;
		$this->hint = isset($data['hint']) ? $data['hint'] : $this->hint;
		$this->class = isset($data['class']) ? $data['class'] : $this->class;
		$this->group = isset($data['group']) ? $data['group'] : $this->group;
		$this->version = isset($data['version']) ? $data['version'] : $this->version;

		if ($this->class !== '' &&
			$this->class !== 'CortexBlock') {
			require_once $this->path . '/block.php';
		}

		$this->active = $blocks_status == false || !isset($blocks_status[$type]) || $blocks_status[$type] == 'enabled';
	}

	/**
	 * Creates a block from this template.
	 * @method create_block
	 * @since 0.1.0
	 */
	public function create_block($type = 0, $post = 0) {
		return new $this->class($type, $post, $this);
	}

	/**
	 * Updates a specific config.
	 * @method update_config
	 * @since 2.0.0
	 */
	public function update_config($key, $val) {
		$configs = $this->read_configs();
		$configs[$key] = $val;
		$this->save_configs($configs);
	}

	/**
	 * Updates the block json file.
	 * @method update_config_file
	 * @since 2.0.0
	 */
	public function update_config_file(array $data) {
		file_put_contents("{$this->path}/block.json", acf_json_encode($data));
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
	 * Updates the fields json file.
	 * @method update_field_file
	 * @since 0.1.0
	 */
	public function update_field_file(array $data) {
		file_put_contents("{$this->path}/fields.json", acf_json_encode($data));
	}

	/**
	 * Enqueue styles file.
	 * @method enqueue_style
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		if ($url = $this->get_style_file_url()) wp_enqueue_style($this->type, $url, array(), $this->version , 'all');
		$this->class::enqueue_styles();
	}

	/**
	 * Enqueue scripts file.
	 * @method enqueue_scripts.
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		if ($url = $this->get_script_file_url()) wp_enqueue_script($this->type,  $url, array(), $this->version, true);
		$this->class::enqueue_scripts();
	}

	/**
	 * @method read_configs
	 * @since 2.0.0
	 * @hidden
	 */
	private function read_configs() {
		$data = file_get_contents("{$this->path}/block.json");
		$data = json_decode($data, true);
		return $data;
	}

	/**
	 * @method save_configs
	 * @since 2.0.0
	 * @hidden
	 */
	private function save_configs($data) {
		file_put_contents("{$this->path}/block.json", acf_json_encode($data));
	}

	//--------------------------------------------------------------------------
	// Twig
	//--------------------------------------------------------------------------

	public function getLink()   { return $this->get_link(); }
	public function getType()   { return $this->get_type(); }
	public function getPath()   { return $this->get_path(); }
	public function getName()   { return $this->get_name(); }
	public function getIcon()   { return $this->get_icon(); }
	public function getHint()   { return $this->get_hint(); }
	public function getGroup()  { return $this->get_group(); }
	public function getClass()  { return $this->get_class(); }
}
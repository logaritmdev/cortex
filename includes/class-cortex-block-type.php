<?php

/**
 * Contains the block definition data.
 * @class CortexBlockType
 * @since 2.0.0
 */
class CortexBlockType {

	const CSS_FILE_PATH = 'assets/styles.css';
	const LESS_FILE_PATH = 'assets/styles.less';
	const SCSS_FILE_PATH = 'assets/styles.scss';
	const SCRIPT_FILE_PATH = 'assets/scripts.js';

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * the block's  type.
	 * @property type
	 * @since 2.0.0
	 */
	private $type = null;

	/**
	 * the block's  location.
	 * @property path
	 * @since 2.0.0
	 */
	private $path = null;

	/**
	 * the block's  name.
	 * @property name
	 * @since 2.0.0
	 */
	private $name = null;

	/**
	 * the block's  icon.
	 * @property icon
	 * @since 2.0.0
	 */
	private $icon = null;

	/**
	 * the block's  hint.
	 * @property hint
	 * @since 2.0.0
	 */
	private $hint = null;

	/**
	 * the block's  class.
	 * @property class
	 * @since 2.0.0
	 */
	private $class = null;

	/**
	 * the block's  group.
	 * @property group
	 * @since 2.0.0
	 */
	private $group = 'Common';

	/**
	 * the block's  fields.
	 * @property fields
	 * @since 2.0.0
	 */
	private $fields = null;

	/**
	 * the block's  block file extension.
	 * @property block_file_type
	 * @since 2.0.0
	 */
	private $block_file_type = 'twig';

	/**
	 * the block's  style file extension.
	 * @property style_file_type
	 * @since 2.0.0
	 */
	private $style_file_type = 'sass';

	/**
	 * the block's  verison.
	 * @property active
	 * @since 2.0.0
	 */
	private $version = '1.0.0';

	/**
	 * Whether the block is active.
	 * @property active
	 * @since 2.0.0
	 */
	private $active = true;

	/**
	 * Whether the block is hidden.
	 * @property active
	 * @since 2.0.0
	 */
	private $hidden = false;

	//--------------------------------------------------------------------------
	// Accessors
	//--------------------------------------------------------------------------

	/**
	 * Assigns the block's type.
	 * @method set_type
	 * @since 2.0.0
	 */
	public function set_type($type) {
		$this->type = $type;
	}

	/**
	 * Returns the block's type.
	 * @method get_type
	 * @since 2.0.0
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Assigns the block's path.
	 * @method set_path
	 * @since 2.0.0
	 */
	public function set_path($path) {
		$this->path = $path;
	}

	/**
	 * Returns the block's path.
	 * @method get_path
	 * @since 2.0.0
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Assigns the block's name.
	 * @method set_name
	 * @since 2.0.0
	 */
	public function set_name($name) {
		$this->name = $name;
	}

	/**
	 * Returns the block's name.
	 * @method get_name
	 * @since 2.0.0
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the block's icon.
	 * @method get_icon
	 * @since 2.0.0
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Returns the block's hint.
	 * @method get_hint
	 * @since 2.0.0
	 */
	public function get_hint() {
		return $this->hint;
	}

	/**
	 * Returns the block's class.
	 * @method get_class
	 * @since 2.0.0
	 */
	public function get_class() {
		return $this->class;
	}

	/**
	 * Returns the block's group.
	 * @method get_group
	 * @since 2.0.0
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Assigns the block's fields.
	 * @method set_fields
	 * @since 2.0.0
	 */
	public function set_fields($fields) {
		$this->fields = $fields;
	}

	/**
	 * Returns the block's fields.
	 * @method get_fields
	 * @since 2.0.0
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Returns the block's block file extension.
	 * @method get_block_file_type
	 * @since 2.0.0
	 */
	public function get_block_file_type() {
		return $this->block_file_type;
	}

	/**
	 * Assigns the block's block file extension.
	 * @method set_block_file_type
	 * @since 2.0.0
	 */
	public function set_block_file_type($type) {
		$this->block_file_type = $type;
	}

	/**
	 * Returns the block's block file modification date.
	 * @method get_block_file_date
	 * @since 2.0.0
	 */
	public function get_block_file_date() {
		return @filemtime($this->get_block_file_path());
	}

	/**
	 * Returns the block tempalte's block file path.
	 * @method get_block_file_path
	 * @since 2.0.0
	 */
	public function get_block_file_path() {
		return sprintf('%s/block.%s', $this->path, $this->block_file_type);
	}

	/**
	 * Returns the block's block file content.
	 * @method get_block_file_content
	 * @since 2.0.0
	 */
	public function get_block_file_content() {
		return @file_get_contents($this->get_block_file_path());
	}

	/**
	 * Returns the block's style file extension.
	 * @method get_style_file_type
	 * @since 2.0.0
	 */
	public function get_style_file_type() {
		return $this->style_file_type;
	}

	/**
	 * Assigns the block's style file extension.
	 * @method set_style_file_type
	 * @since 2.0.0
	 */
	public function set_style_file_type($type) {
		$this->style_file_type = $type;
	}

	/**
	 * Returns block template's style file modification date.
	 * @method get_style_file_date
	 * @since 2.0.0
	 */
	public function get_style_file_date() {
		return @filemtime($this->get_style_file_path());
	}

	/**
	 * Returns the block's style file path.
	 * @method get_style_file_path
	 * @since 2.0.0
	 */
	public function get_style_file_path() {

		switch ($this->style_file_type) {
			case 'less': return sprintf('%s/%s', $this->path, self::LESS_FILE_PATH);
			case 'scss': return sprintf('%s/%s', $this->path, self::SCSS_FILE_PATH);
		}

		return sprintf('%s/%s', $this->path, self::CSS_FILE_PATH);
	}

	/**
	 * Returns the block's style file url.
	 * @method get_style_file_url
	 * @since 2.0.0
	 */
	public function get_style_file_url() {
		return is_readable($this->get_style_file_path()) && filesize($this->get_style_file_path()) ? $this->get_link() . '/' . self::CSS_FILE_PATH : null;
  	}

	/**
	 * Returns block template's style file content.
	 * @method get_style_file_content
	 * @since 2.0.0
	 */
	public function get_style_file_content() {
		return @file_get_contents($this->get_style_file_path());
	}

	/**
	 * Returns the block's script file modification date.
	 * @method get_script_file_date
	 * @since 2.0.0
	 */
	public function get_script_file_date() {
		return @filemtime($this->get_script_file_path());
	}

	/**
	 * Returns the block's script file path.
	 * @method get_script_file_path
	 * @since 2.0.0
	 */
	public function get_script_file_path() {
		return sprintf('%s/%s', $this->path, self::SCRIPT_FILE_PATH);
	}

	/**
 	 * Returns the block's script file url.
 	 * @method get_style_file_url
 	 * @since 2.0.0
 	 */
	  public function get_script_file_url() {
		return is_readable($this->get_script_file_path()) && filesize($this->get_script_file_path()) ? $this->get_link() . '/' . self::SCRIPT_FILE_PATH : null;
	}

	/**
	 * Returns the block's script file content.
	 * @method get_script_file_content
	 * @since 2.0.0
	 */
	public function get_script_file_content() {
		return @file_get_contents($this->get_script_file_path());
	}

	/**
	 * Returns the block's url.
	 * @method get_link
	 * @since 2.0.0
	 */
	public function get_link() {
		return str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $this->path);
	}

	/**
	 * Returns the block's fields.json update date.
	 * @method get_date
	 * @since 2.0.0
	 */
	public function get_date() {
		return @filemtime(sprintf('%s/fields.json', $this->path));
	}

	/**
	 * Returns whether the block is active.
	 * @method get_parent_region
	 * @since 2.0.0
	 */
	public function is_active() {
		return $this->active;
	}

	/**
	 * Returns whether the block is active.
	 * @method get_parent_region
	 * @since 2.0.0
	 */
	public function is_hidden() {
		return $this->hidden;
	}

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initializes the block template.
	 * @cosntructor
	 * @since 2.0.0
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
		$this->active = isset($data['active']) ? $data['active'] : $this->active;
		$this->hidden = isset($data['hidden']) ? $data['hidden'] : $this->hidden;
		$this->version = isset($data['version']) ? $data['version'] : $this->version;

		if ($this->active) {
			$this->active = $blocks_status == false || !isset($blocks_status[$type]) || $blocks_status[$type] == 'enabled';
		}
	}

	/**
	 * Creates a block from this template.
	 * @method create_block
	 * @since 2.0.0
	 */
	public function create_block($id, $post) {

		if ($this->class !== '' &&
			$this->class !== 'CortexBlock') {
			require_once $this->path . '/block.php';
		}

		return new $this->class($id, $post, $this);
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
	 * @since 2.0.0
	 */
	public function update_block_file($data) {
		file_put_contents($this->get_block_file_path(), $data);
	}

	/**
	 * Updates the style file.
	 * @method update_style_file
	 * @since 2.0.0
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
	 * @since 2.0.0
	 */
	public function update_script_file($data) {
		file_put_contents($this->get_script_file_path(), $data);
	}

	/**
	 * Updates the fields json file.
	 * @method update_field_file
	 * @since 2.0.0
	 */
	public function update_field_file(array $data) {
		file_put_contents("{$this->path}/fields.json", acf_json_encode($data));
	}

	/**
	 * Enqueue styles file.
	 * @method enqueue_style
	 * @since 2.0.0
	 */
	public function enqueue_styles() {
		if ($url = $this->get_style_file_url()) wp_enqueue_style($this->type, $url, array(), $this->version , 'all');
		$this->class::enqueue_styles();
	}

	/**
	 * Enqueue scripts file.
	 * @method enqueue_scripts.
	 * @since 2.0.0
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
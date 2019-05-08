<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

/**
 * @class CortexBlockList
 * @since 0.2.0
 * @hidden
 */
class CortexBlockList extends WP_List_Table {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * @constructor
	 * @since 0.2.0
	 */
	function __construct() {

		parent::__construct(array(
			'singular' => __('block', 'cortex'),
			'plural'   => __('blocks', 'cortex'),
			'ajax'     => false
		));

	}

	/**
	 * @method no_items
	 * @since 0.2.0
	 * @hidden
	 */
	public function no_items() {
		echo __('No blocks', 'cortex');
	}

	/**
	 * @method column_default
	 * @since 0.2.0
	 * @hidden
	 */
	public function column_default($group, $column_name) {

		switch ($column_name) {
			case 'icon':     return $this->get_icon($group['@block_icon']);
			case 'name':     return $this->get_name($group['@block_name']);
			case 'category': return $group['@block_group'];

			default:
				return '';
		}
	}

	/**
	 * @method get_columns
	 * @since 0.2.0
	 * @hidden
	 */
	public function get_columns() {

		$columns = array(
			'icon' => '',
			'name' => __('Name', 'cortex'),
			'category' => __('Category', 'cortex'),
		);

		return $columns;
	}

	/**
	 * @method get_bulk_actions
	 * @since 0.2.0
	 * @hidden
	 */
	public function get_bulk_actions() {
        return array();
    }

	/**
	 * @method column_cb
	 * @since 0.2.0
	 * @hidden
	 */
	public function column_cb($group) {
        return sprintf(
            '<input type="checkbox" name="block[]" value="%s" />', $group['ID']
        );
    }

	/**
	 * @method column_name
	 * @since 0.2.0
	 * @hidden
	 */
	public function column_name($group) {
		return sprintf('
			<strong><a class="row-title cortex-update-block-link" href="%s">%s</a></strong>', sprintf('post.php?post=%s&action=edit&mode=cortex-block', $group['ID']), stripslashes($group['@block_name'])
		);
	}

	/**
	 * @method extra_tablename
	 * @since 0.2.0
	 * @hidden
	 */
	public function extra_tablenav($which) {

	}

	/**
	 * @method prepare_items
	 * @since 0.2.0
	 * @hidden
	 */
	public function prepare_items() {

		if (class_exists('acf') === false) {
			return;
		}

		$columns_hidden = array();
		$columns_header = $this->get_columns();
		$columns_sorted = array();

		$this->_column_headers = array(
			$columns_header,
			$columns_hidden,
			$columns_sorted,
		);

		$blocks = array();
		$groups = acf_get_field_groups();

		foreach ($groups as $group) {

			$block_name = acf_maybe_get($group, '@block_name');
			$block_type = acf_maybe_get($group, '@block_type');

			if ($block_name === null ||
				$block_type === null) {
				continue;
			}

			if ($group['active'] && $group['hidden'] === false) {
				$blocks[] = $group;
			}
		}

		$limit = 40;
		$total = count($blocks);

		$this->set_pagination_args(array('total_items' => $total, 'per_page' => $limit));

		$this->items = array_slice($blocks, ($this->get_pagenum() - 1) * $limit, $limit);
	}

	/**
	 * @method process_bulk_action
	 * @since 0.2.0
	 * @hidden
	 */
  	public function process_bulk_action() {

    }

	/**
	 * @method get_name
	 * @since 2.0.0
	 * @hidden
	 */
	private function get_name($group) {
		return (
			'<div class="cortex-block-name">' . $group['@block_name'] . '</div>' .
			'<div class="cortex-block-hint">' . $group['@block_hint'] . '</div>'
		);
	}

	/**
	 * @method get_icon
	 * @since 2.0.0
	 * @hidden
	 */
	private function get_icon($group) {

		$icon = acf_maybe_get($group, '@block_icon');

		if ($icon === '' ||
			$icon === null) {
			$icon = plugins_url('../admin/images/icon.svg', __FILE__);
		}

		if ($this->icon_is_svg_file($icon)) {
			return ('
				<div class="cortex-block-icon">
					<img src="' . $icon . '" width="24" height="20">
				</div>
			');
		}

		if ($this->icon_is_svg_code($icon)) {
			return ('
				<div class="cortex-block-icon">' . $icon . '</div>
			');
		}

		return ('
			<div class="cortex-block-icon">
				<div class="dashicons-before ' . $icon . '"></div>
			</div>
		');
	}

	/**
	 * @method icon_is_svg_file
	 * @since 2.0.0
	 * @hidden
	 */
	private function icon_is_svg_file($icon) {
		return preg_match_all('/\.svg$/s', $icon);
	}

	/**
	 * @method icon_is_svg_code
	 * @since 2.0.0
	 * @hidden
	 */
	private function icon_is_svg_code($icon) {
		return preg_match_all('/(<svg)([^<]*|[^>]*)/s', $icon);
	}

	/**
	 * @method get_block
	 * @since 0.2.0
	 * @hidden
	 */
	private function get_block($group) {
		return Cortex::get_block(Cortex::get_acf_field_group_block_type($group));
	}
}

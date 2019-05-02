<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
// TODO retouch
/**
 * @class CortexBlockTemplateList
 * @since 0.1.0
 * @hidden
 */
class CortexBlockTemplateList extends WP_List_Table {

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * @constructor
	 * @since 0.1.0
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
	 * @since 0.1.0
	 * @hidden
	 */
	public function no_items() {
		echo __('No blocks', 'cortex');
	}

	/**
	 * @method column_default
	 * @since 0.1.0
	 * @hidden
	 */
	public function column_default($field_group, $column_name) {

		$template = $this->get_block_template($field_group);

		switch ($column_name) {

			case 'name':  return $template->get_name();
			case 'hint':  return $template->get_hint();
			case 'group': return $template->get_group();

			default:
				return '';
		}
	}

	/**
	 * @method get_columns
	 * @since 0.1.0
	 * @hidden
	 */
	public function get_columns() {

		$columns = array(
			'name'  => __('Name', 'cortex'),
			'hint'  => __('Hint', 'cortex'),
			'category' => __('Category', 'cortex'),
		);

		return $columns;
	}

	/**
	 * @method get_bulk_actions
	 * @since 0.1.0
	 * @hidden
	 */
	public function get_bulk_actions() {
        return array();
    }

	/**
	 * @method column_cb
	 * @since 0.1.0
	 * @hidden
	 */
	public function column_cb($field_group) {
        return sprintf(
            '<input type="checkbox" name="block_template[]" value="%s" />', $field_group['ID']
        );
    }

	/**
	 * @method column_name
	 * @since 0.1.0
	 * @hidden
	 */
	public function column_name($field_group) {

		$edit_fields_url = sprintf('post.php?post=%s&action=edit&mode=cortex-block', $field_group['ID']);

		$actions = array(
			'edit_fields'  => sprintf('<a class="cortex-update-block-link" href="%s">%s</a>', $edit_fields_url, __('Edit Fields', 'cortex')),
		);

		return sprintf('<strong><a class="row-title cortex-update-block-link" href="%s">%s</a></strong> %s', $edit_fields_url, $this->get_block_template($field_group)->get_name(), $this->row_actions($actions));
	}

	/**
	 * @method extra_tablename
	 * @since 0.1.0
	 * @hidden
	 */
	public function extra_tablenav($which) {

	}

	/**
	 * @method prepare_items
	 * @since 0.1.0
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

		$rows = array();

		foreach (acf_get_field_groups() as $field_group) {

			$template = $this->get_block_template($field_group);
			if ($template == null) {
				continue;
			}

			if ($template->is_active() === false) {
				continue;
			}

			$rows[] = $field_group;
		}

		$limit = 15;
		$index = $this->get_pagenum() - 1;
		$total = count($rows);

		$this->set_pagination_args(array('total_items' => $total, 'per_page' => $limit));

		$this->items = array_slice($rows, $index * $limit, $limit);
	}

	/**
	 * @method process_bulk_action
	 * @since 0.1.0
	 * @hidden
	 */
  	public function process_bulk_action() {

    }

	/**
	 * @method get_block_template
	 * @since 0.1.0
	 * @hidden
	 */
	private function get_block_template($field_group) {
		return Cortex::get_block_template(get_post_meta($field_group['ID'], '_cortex_block_type', true));
	}
}

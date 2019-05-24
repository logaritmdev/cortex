<?php

/**
 * Manages previews.
 * @class CortexPreview
 * @since 0.1.0
 */
class CortexPreview {

	/**
	 * Generates a preview hash.
	 * @method generate_preview_hash
	 * @since 2.0.0
	 */
	public static function generate_preview_hash($id, $post, $vars) {
		return md5($id . $post . json_encode($vars));
	}

	/**
	 * Returns the preview's hash.
	 * @method get_preview_hash
	 * @since 2.0.0
	 */
	public static function get_preview_hash($id, $post) {
		return self::get_preview_key($id, $post, 'hash');
	}

	/**
	 * Sets the preview's hash.
	 * @method set_preview_hash
	 * @since 2.0.0
	 */
	public static function set_preview_hash($id, $post, $hash) {
		self::set_preview_key($id, $post, 'hash', $hash);
	}

	/**
	 * Returns the preview's content variables.
	 * @method get_preview_vars
	 * @since 2.0.0
	 */
	public static function get_preview_vars($id, $post) {
		return self::get_preview_key($id, $post, 'vars');
	}

	/**
	 * Sets the preview's content variables.
	 * @method set_preview_vars
	 * @since 2.0.0
	 */
	public static function set_preview_vars($id, $post, $vars) {
		self::set_preview_key($id, $post, 'vars', $vars);
	}

	/**
	 * Returns the preview's image source.
	 * @method get_preview_src
	 * @since 2.0.0
	 */
	public static function get_preview_src($id, $post) {
		return self::get_preview_key($id, $post, 'src');
	}

	/**
	 * Sets the preview's image source.
	 * @method set_preview_src
	 * @since 2.0.0
	 */
	public static function set_preview_src($id, $post, $src) {
		self::set_preview_key($id, $post, 'src', $src);
	}

	/**
	 * Returns the preview's image url.
	 * @method get_preview_url
	 * @since 2.0.0
	 */
	public static function get_preview_url($id, $post) {
		return self::get_preview_key($id, $post, 'url');
	}

	/**
	 * Sets the preview's image url.
	 * @method set_preview_url
	 * @since 2.0.0
	 */
	public static function set_preview_url($id, $post, $url) {
		self::set_preview_key($id, $post, 'url', $url);
	}

	/**
	 * Returns the preview's image size.
	 * @method get_preview_size
	 * @since 2.0.0
	 */
	public static function get_preview_size($id, $post) {
		return self::get_preview_key($id, $post, 'size');
	}

	/**
	 * Sets the preview's image size.
	 * @method set_preview_size
	 * @since 2.0.0
	 */
	public static function set_preview_size($id, $post, $size) {
		self::set_preview_key($id, $post, 'size', $size);
	}

	/**
	 * Saves the preview data into a file.
	 * @method save_preview
	 * @since 2.0.0
	 */
	public static function save_preview($id, $post, $hash, $data, $size) {

		$filename = $id . '-' . $post . '.png';

		$src = WP_CONTENT_DIR . '/cache/cortex-previews/' . $filename;
		$url = WP_CONTENT_URL . '/cache/cortex-previews/' . $filename;

		@mkdir(dirname($src), 0777, true);

		file_put_contents($src, base64_decode(str_replace('data:image/png;base64,', '', $data)));

		self::set_preview_src($id, $post, $src);
		self::set_preview_url($id, $post, $url);
		self::set_preview_size($id, $post, $size);
		self::set_preview_hash($id, $post, $hash);
	}

	//--------------------------------------------------------------------------
	// Private API
	//--------------------------------------------------------------------------

	/**
	 * @method get_preview_key
	 * @since 2.0.0
	 * @hidden
	 */
	private static function get_preview_key($id, $post, $key) {

		$data = self::get_previews($post);

		if ($data == null) {
			return null;
		}

		return isset($data[$id][$key]) ? $data[$id][$key] : null;
	}

	/**
	 * @method get_preview_key
	 * @since 2.0.0
	 * @hidden
	 */
	private static function set_preview_key($id, $post, $key, $val) {
		$data = self::get_previews($post);
		$data[$id][$key] = $val;
		self::set_previews($post, $data);
	}

	/**
	 * @method get_previews
	 * @since 2.0.0
	 * @hidden
	 */
	private static function get_previews($post) {

		$data = get_post_meta($post, '_cortex_post_previews');

		if ($data === null ||
			$data === false) {
			return null;
		}

		return isset($data[0]) ? $data[0] : array();
	}

	/**
	 * @method set_previews
	 * @since 2.0.0
	 * @hidden
	 */
	private static function set_previews($post, $data) {
		update_post_meta($post, '_cortex_post_previews', $data);
	}
}
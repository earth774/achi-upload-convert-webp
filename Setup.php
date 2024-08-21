<?php

/**
 * Setup Hide Admin Bar.
 *
 * @package Hide_Admin_Bar
 */

namespace Achi\UploadConvertWebp;

/**
 * Setup Hide Admin Bar.
 */
class Setup
{

	/**
	 * Whether to remove admin bar for current user.
	 *
	 * @var bool
	 */
	public static $remove_admin_bar = false;

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Get instance of the class.
	 */
	public static function get_instance()
	{

		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Init the class setup.
	 */
	public static function init()
	{

		self::$instance = new self();

		add_action('plugins_loaded', array(self::$instance, 'setup'));
	}


	/**
	 * Setup action & filters.
	 */
	public function setup()
	{
		if(get_option('handle_webp_show_button_convert')){
			add_action('admin_enqueue_scripts', array($this, 'webp_converter_enqueue_script'));
			add_filter('attachment_fields_to_edit', array($this, 'add_webp_convert_button'), 10, 2);
			add_action('wp_ajax_convert_to_webp', array($this, 'handle_webp_conversion'));
		}

		if(get_option('handle_webp_upload_webp')){
			add_filter('wp_handle_upload', array($this, 'convert_upload_to_webp'));
		}

		add_action('admin_menu', array($this, 'handle_webp_menu'));
		add_action('admin_init', array($this, 'handle_webp_register_settings'));
	}

	// Add a menu page for the plugin
	function handle_webp_menu()
	{
		if (current_user_can('administrator')) {
			add_options_page(
				'Upload Convert Webp', // Page title
				'Upload Convert Webp Settings', // Menu title
				'manage_options', // Capability
				'handle_webp-settings', // Menu slug
				array($this, 'handle_webp_settings_page') // Function to display the settings page
			);
		}
	}

	// Display the settings page
	public function handle_webp_settings_page()
	{
		require_once(plugin_dir_path(__FILE__) . 'templates/settings-page.php');
	}

	// Register settings
	public function handle_webp_register_settings()
	{
			register_setting('handle-webp-settings-group', 'handle_webp_upload_webp');
			register_setting('handle-webp-settings-group', 'handle_webp_show_button_convert');
			register_setting('handle-webp-settings-group', 'handle_webp_image_quality');
	}

	// เพิ่ม JavaScript เพื่อแสดงปุ่มในหน้า Attachment details
	public function webp_converter_enqueue_script()
	{
		wp_enqueue_script('webp-converter-script', plugin_dir_url(__FILE__) . 'assets/js/webp-converter.js', array('jquery'), null, true);
		wp_localize_script('webp-converter-script', 'webpConverter', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('achi_webp_convert_nonce')
		));
	}

	// เพิ่มปุ่ม "Convert to WebP" ในหน้า Attachment details
	public function add_webp_convert_button($form_fields, $post)
	{
		if (in_array($post->post_mime_type, array('image/jpeg', 'image/png', 'image/gif'))) {
			$form_fields['convert_to_webp'] = array(
				'label' => __('Convert to WebP'),
				'input' => 'html',
				'html'  => '<button type="button" class="button" id="convert-to-webp" data-attachment-id="' . $post->ID . '">' . __('Convert to WebP') . '</button>',
			);
		}

		return $form_fields;
	}

	// ดำเนินการแปลงภาพเมื่อคลิกปุ่ม
	public function handle_webp_conversion()
	{
		check_ajax_referer('achi_webp_convert_nonce', 'nonce');

		if (!current_user_can('upload_files') || !isset($_POST['attachment_id'])) {
			wp_send_json_error('Permission denied.');
		}

		$attachment_id = intval($_POST['attachment_id']);
		$file = get_attached_file($attachment_id);

		if ($file) {
			$webp_file = convert_image_to_webp($file);

			if ($webp_file) {
				// ลบไฟล์ต้นฉบับหลังจากการแปลงสำเร็จ
				unlink($file);

				// ลบไฟล์ขนาดย่อยทั้งหมด
				$metadata = wp_get_attachment_metadata($attachment_id);
				$upload_dir = wp_upload_dir();
				$base_path = $upload_dir['basedir'] . '/' . dirname($metadata['file']) . '/';

				if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
					foreach ($metadata['sizes'] as $size) {
						$size_file = $base_path . $size['file'];
						if (file_exists($size_file)) {
							unlink($size_file);
						}
					}
				}

				update_attached_file($attachment_id, $webp_file);
				wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $webp_file));
				wp_send_json_success('Image converted to WebP successfully.');
			}
		}

		wp_send_json_error('Failed to convert the image to WebP.');
	}


	// Upload convert to webp
	function convert_upload_to_webp($upload)
	{
		$file = $upload['file'];
		$webp_file = convert_image_to_webp($file);

		if ($webp_file) {
			// ลบไฟล์ต้นฉบับถ้าไม่ต้องการเก็บไว้
			unlink($file);

			// เปลี่ยน path ของไฟล์ที่อัปโหลดเป็นไฟล์ WebP
			$upload['file'] = $webp_file;

			// เปลี่ยน URL ของไฟล์ที่อัปโหลดเป็นไฟล์ WebP
			$upload['url'] = str_replace(pathinfo($file, PATHINFO_EXTENSION), 'webp', $upload['url']);

			// เปลี่ยน mime type เป็น image/webp
			$upload['type'] = 'image/webp';
		}

		return $upload;
	}
}

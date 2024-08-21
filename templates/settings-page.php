<?php

/**
 * Settings page template.
 *
 * @package Hide_Admin_Bar
 */

defined('ABSPATH') || die("Can't access directly");

?>
<div class="wrap">
	<h1>Upload Convert Webp</h1>
	<form method="post" action="options.php">
		<?php
		settings_fields('handle-webp-settings-group');
		do_settings_sections('handle-webp-settings-group');
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Upload Webp : </th>
				<td>
					<input type="checkbox" name="handle_webp_upload_webp" value="1" <?php checked(1, get_option('handle_webp_upload_webp'), true); ?> />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Show Button Convert : </th>
				<td>
					<input type="checkbox" name="handle_webp_show_button_convert" value="1" <?php checked(1, get_option('handle_webp_show_button_convert'), true); ?> />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Image Quanlity : </th>
				<td>
					<input type="number" name="handle_webp_image_quality" min="0" max="100" value="<?php echo get_option('handle_webp_image_quality'); ?>" style="width: 200px;">
					<p>Quanlity convert image 0 - 100</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php

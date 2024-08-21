<?php

/**
 * Utility functions.
 *
 * @package UploadConvertWebp 
 */


// ฟังก์ชันแปลงภาพเป็น WebP
function convert_image_to_webp($source, $quality = 80)
{
    if(get_option('handle_webp_image_quality')){
        $quality = get_option('handle_webp_image_quality');
    }

    $image_info = getimagesize($source);
    $mime = $image_info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $path = pathinfo($source, PATHINFO_DIRNAME);
    $filename = pathinfo($source, PATHINFO_FILENAME) . '.webp';
    $unique_filename = generate_unique_filename_with_timestamp($path, $filename);

    $destination = $path . '/' . $unique_filename;
    $result = imagewebp($image, $destination, $quality);
    imagedestroy($image);

    return $result ? $destination : false;
}

function generate_unique_filename_with_timestamp($path, $filename) {
    $file_info = pathinfo($filename);
    $extension = isset($file_info['extension']) ? '.' . $file_info['extension'] : '';
    $basename = sanitize_file_name($file_info['filename']);
    
    // สร้างชื่อไฟล์ไม่ซ้ำกันโดยใช้ timestamp
    $unique_filename = $basename . '-' . time() . $extension;

    // ตรวจสอบว่าชื่อไฟล์นี้มีอยู่แล้วหรือไม่
    while (file_exists($path . '/' . $unique_filename)) {
        $unique_filename = $basename . '-' . time() . $extension;
    }

    return $unique_filename;
}

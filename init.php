<?php

/**
 * Plugin Name:     Achi Upload Convert Webp 
 * Plugin URI:      https://wordpress.org/plugins/achi-upload-convert-webp    
 * Description:     
 * Version:         1.0.0          
 * Requires at least: 6.4.2 
 * Requires PHP:    8.0 
 * Author:          Sutthipong Nuanma
 * Author URI:      https://amiearth.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

require __DIR__ . '/helpers.php';
require __DIR__ . '/vendor/autoload.php';

Achi\UploadConvertWebp\Setup::init();

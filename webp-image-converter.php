<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://yourwebsite.com
 * @since             1.0.0
 * @package           WebP_Image_Converter
 *
 * @wordpress-plugin
 * Plugin Name:       WebP Image Converter
 * Plugin URI:        https://yourwebsite.com/webp-image-converter
 * Description:       A simple tool to convert and resize images to WebP format for WordPress optimization.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://yourwebsite.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webp-image-converter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('WEBP_IMAGE_CONVERTER_VERSION', '1.0.0');

/**
 * The core plugin class
 */
require plugin_dir_path(__FILE__) . 'includes/class-webp-converter.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_webp_image_converter() {
    $plugin = new WebP_Image_Converter();
    $plugin->run();
}
run_webp_image_converter();
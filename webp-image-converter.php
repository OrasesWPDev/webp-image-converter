<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/OrasesWPDev/webp-image-converter
 * @since             1.0.0
 * @package           WebP_Image_Converter
 *
 * @wordpress-plugin
 * Plugin Name:       WebP Image Converter
 * Plugin URI:        https://github.com/OrasesWPDev/webp-image-converter
 * Description:       Convert WordPress media library images to WebP format with bulk processing and optimization features.
 * Version:           1.3.0
 * Author:            Orases
 * Author URI:        https://orases.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webp-image-converter
 * Domain Path:       /languages
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Update URI:        https://github.com/OrasesWPDev/webp-image-converter
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('WEBP_IMAGE_CONVERTER_VERSION', '1.3.0');

/**
 * Debug flag - set to true to enable comprehensive logging
 */
define('WEBP_IMAGE_CONVERTER_DEBUG', false);

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
=== WebP Image Converter ===
Contributors: orases
Tags: webp, image, optimization, convert, media-library, bulk-processing
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert WordPress media library images to WebP format with bulk processing and optimization features.

== Description ==

WebP Image Converter is a simple tool that allows you to:

* Convert any image format to WebP
* Resize images to preset WordPress dimensions
* Compare original and converted images side-by-side
* See detailed metadata and compression stats
* Easily download the optimized images

The tool is designed to help optimize images for your WordPress site by leveraging the superior compression of the WebP format.

== Installation ==

1. Upload the `webp-image-converter` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Access the converter from Tools > WebP Converter

== Frequently Asked Questions ==

= What image formats can I convert? =

You can convert JPEG, PNG, GIF, BMP, and TIFF images to WebP format.

= What dimensions are available for resizing? =

The plugin offers the following preset dimensions:
* Main Header Images (1500 × 500)
* Blog Post Thumbnails (300 × 200)
* Featured Images (1200 × 630)
* Internal Page Images (500 × 500)
* Gallery Thumbnails (150 × 150)
* Custom dimensions (your choice)

= What server requirements are needed? =

Your server should be running:
* WordPress 6.5 or higher
* PHP 8.0 or higher
* GD library with WebP support or ImageMagick

== Screenshots ==

1. The main converter interface
2. Side-by-side comparison of original and converted images

== Changelog ==

= 1.3.2 =
* Fixed GitHub Actions workflow to prevent unnecessary runs and duplicate releases
* Removed pull_request trigger to eliminate race conditions between PR and merge events
* Added path filters to only run workflow when plugin files change
* Added version change detection to skip releases when version is unchanged
* Enhanced conditional logic and improved skip messaging
* Reduced GitHub Actions usage and improved workflow reliability

= 1.3.1 =
* HOTFIX: Fixed incorrect vendor path for Plugin Update Checker library
* Resolved fatal error preventing plugin activation
* Corrected require_once path from vendor/plugin-update-checker/ to vendor/vendor/plugin-update-checker/

= 1.3.0 =
* Added GitHub-based auto-updater system using YahnisElsts Plugin Update Checker v5.6
* Implemented WebP_Auto_Updater class for seamless updates from GitHub releases
* Added GitHub Actions workflow for automatic release creation on version changes
* Configured release-based updates instead of branch-based for better version control
* Added GitHub token authentication support for private repositories
* Integrated comprehensive logging for update operations and status tracking
* Added manual update check functionality and automated plugin ZIP distribution
* Configured 1-minute update check frequency for immediate update detection

= 1.2.0 =
* Added comprehensive debug logging system
* Implemented WEBP_IMAGE_CONVERTER_DEBUG constant for debug control
* Added WebP_Image_Converter_Logger class with static methods
* Integrated logging throughout image processing and AJAX operations
* Added JavaScript debug support with console logging
* Created secured /logs/ directory with protection files
* Added daily log files with multiple log levels (debug, info, warning, error)

= 1.1.0 =
* Updated plugin header with Orases branding
* Enhanced requirements (WordPress 6.5+, PHP 8.0+)
* Improved plugin description and metadata
* Added comprehensive development roadmap

= 1.0.0 =
* Initial release
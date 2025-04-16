<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/admin
 */
class WebP_Image_Converter_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The processor instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      WebP_Image_Converter_Processor    $processor    The image processor instance.
     */
    private $processor;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name    The name of this plugin.
     * @param    string    $version        The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->processor = new WebP_Image_Converter_Processor();

        // Register AJAX handlers
        $this->register_ajax_handlers();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook) {
        // Only load on our plugin page
        if ('tools_page_webp-image-converter' !== $hook) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/webp-converter-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook) {
        // Only load on our plugin page
        if ('tools_page_webp-image-converter' !== $hook) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/webp-converter-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Add nonce and ajax url for security
        wp_localize_script(
            $this->plugin_name,
            'webpConverterData',
            array(
                'saveMediaNonce' => wp_create_nonce('webp_converter_save_media'),
                'ajaxUrl' => admin_url('admin-ajax.php')
            )
        );
    }

    /**
     * Add menu item for the plugin.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_submenu_page(
            'tools.php',                          // Parent slug
            'WebP Image Converter',               // Page title
            'WebP Converter',                     // Menu title
            'upload_files',                       // Capability
            'webp-image-converter',               // Menu slug
            array($this, 'display_plugin_admin_page')  // Callback
        );
    }

    /**
     * Display the admin page content.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        // Process form submission if needed
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
            $this->processor->process_image();
        }

        // Get the processing results
        $original_image = $this->processor->get_original_image();
        $converted_image = $this->processor->get_converted_image();
        $original_meta = $this->processor->get_original_meta();
        $converted_meta = $this->processor->get_converted_meta();
        $error_msg = $this->processor->get_error();

        // Include the partial for display
        include_once('partials/webp-converter-admin-display.php');
    }

    /**
     * Register AJAX handlers for the plugin.
     *
     * @since    1.0.0
     */
    public function register_ajax_handlers() {
        add_action('wp_ajax_webp_save_to_media_library', array($this, 'ajax_save_to_media_library'));
    }

    /**
     * AJAX handler to save the converted image to media library
     *
     * @since    1.0.0
     */
    public function ajax_save_to_media_library() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'webp_converter_save_media')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'webp-image-converter')));
        }

        // Check for required data
        if (!isset($_POST['image_data']) || empty($_POST['image_data'])) {
            wp_send_json_error(array('message' => __('No image data provided.', 'webp-image-converter')));
        }

        // Get the base64 encoded image data
        $image_data = $_POST['image_data'];
        // Remove the data URL prefix if present
        if (strpos($image_data, 'data:image/webp;base64,') === 0) {
            $image_data = str_replace('data:image/webp;base64,', '', $image_data);
        }

        // Use the original filename if provided, otherwise use a default
        $title = isset($_POST['title']) && !empty($_POST['title'])
            ? sanitize_text_field($_POST['title'])
            : 'WebP Conversion';

        // Set the converted image data in the processor
        $this->processor->set_converted_image($image_data);

        // Save to media library
        $result = $this->processor->save_to_media_library($title);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } else {
            wp_send_json_success(array(
                'message' => __('Image saved to Media Library successfully!', 'webp-image-converter'),
                'attachment' => $result
            ));
        }
    }
}
<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/includes
 */
class WebP_Image_Converter {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WebP_Image_Converter_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The auto-updater instance.
     *
     * @since    1.3.0
     * @access   protected
     * @var      WebP_Auto_Updater    $auto_updater    Handles GitHub-based auto-updates.
     */
    protected $auto_updater;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('WEBP_IMAGE_CONVERTER_VERSION')) {
            $this->version = WEBP_IMAGE_CONVERTER_VERSION;
        } else {
            $this->version = '1.3.0';
        }
        $this->plugin_name = 'webp-image-converter';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->init_auto_updater();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-webp-converter-loader.php';

        /**
         * The class responsible for processing images.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-webp-converter-processor.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-webp-converter-admin.php';

        /**
         * The class responsible for debug logging functionality.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-webp-converter-logger.php';

        /**
         * The class responsible for GitHub-based auto-updates.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-webp-auto-updater.php';

        $this->loader = new WebP_Image_Converter_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new WebP_Image_Converter_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
    }

    /**
     * Initialize the auto-updater system
     *
     * @since    1.3.0
     * @access   private
     */
    private function init_auto_updater() {
        $plugin_file = plugin_dir_path(dirname(__FILE__)) . 'webp-image-converter.php';
        $this->auto_updater = new WebP_Auto_Updater($plugin_file);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    WebP_Image_Converter_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the auto-updater instance.
     *
     * @since     1.3.0
     * @return    WebP_Auto_Updater    The auto-updater instance.
     */
    public function get_auto_updater() {
        return $this->auto_updater;
    }
}
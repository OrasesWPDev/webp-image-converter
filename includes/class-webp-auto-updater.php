<?php
/**
 * Auto-updater class for GitHub-based plugin updates.
 *
 * @since      1.3.0
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/includes
 */
class WebP_Auto_Updater {

    /**
     * Plugin main file path
     *
     * @since 1.3.0
     * @var string
     */
    private $plugin_file;

    /**
     * Update checker instance
     *
     * @since 1.3.0
     * @var Puc_v4p13_Plugin_UpdateChecker
     */
    private $update_checker;

    /**
     * Initialize the auto-updater
     *
     * @since 1.3.0
     * @param string $plugin_file Path to the main plugin file
     */
    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->init_update_checker();
        
        WebP_Image_Converter_Logger::info('Auto-updater initialized', [
            'plugin_file' => $this->plugin_file,
            'github_repo' => 'OrasesWPDev/webp-image-converter',
            'update_method' => 'GitHub Releases (tag-based)'
        ]);
    }

    /**
     * Initialize the Plugin Update Checker
     *
     * @since 1.3.0
     */
    private function init_update_checker() {
        // Load the Plugin Update Checker library
        require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/plugin-update-checker/plugin-update-checker.php';

        // Create the update checker instance for GitHub releases
        $this->update_checker = Puc_v4p13_Factory::buildUpdateChecker(
            'https://github.com/OrasesWPDev/webp-image-converter/',
            $this->plugin_file,
            'webp-image-converter'
        );

        // Use GitHub releases instead of branch for more reliable version detection
        $this->update_checker->getVcsApi()->enableReleaseAssets();

        // Set update check frequency to 1 minute (for development/testing)
        // In production, this might be changed to a longer interval
        $this->update_checker->setUpdateCheckThrottling(1 * MINUTE_IN_SECONDS);

        // Add authentication if GitHub token is available
        $this->setup_authentication();

        // Add custom filters
        $this->add_filters();

        WebP_Image_Converter_Logger::debug('Plugin Update Checker configured', [
            'repository' => 'https://github.com/OrasesWPDev/webp-image-converter/',
            'update_source' => 'GitHub Releases',
            'check_frequency' => '1 minute',
            'slug' => 'webp-image-converter',
            'release_assets' => 'enabled'
        ]);
    }

    /**
     * Setup GitHub authentication if token is available
     *
     * @since 1.3.0
     */
    private function setup_authentication() {
        // Check for GitHub token in various locations
        $github_token = $this->get_github_token();
        
        if ($github_token) {
            $this->update_checker->setAuthentication($github_token);
            WebP_Image_Converter_Logger::info('GitHub authentication configured for auto-updater');
        } else {
            WebP_Image_Converter_Logger::warning('No GitHub token found - updates may be rate-limited');
        }
    }

    /**
     * Get GitHub token from various sources
     *
     * @since 1.3.0
     * @return string|null
     */
    private function get_github_token() {
        // Check WordPress option first (set via settings page in future)
        $token = get_option('webp_converter_github_token');
        if (!empty($token)) {
            return $token;
        }

        // Check environment variable
        $token = getenv('WEBP_CONVERTER_GITHUB_TOKEN');
        if (!empty($token)) {
            return $token;
        }

        // Check constant (can be defined in wp-config.php)
        if (defined('WEBP_CONVERTER_GITHUB_TOKEN')) {
            return WEBP_CONVERTER_GITHUB_TOKEN;
        }

        return null;
    }

    /**
     * Add custom filters for update process
     *
     * @since 1.3.0
     */
    private function add_filters() {
        // Add filter to log update checks
        add_filter('puc_request_info_result-webp-image-converter', [$this, 'log_update_check'], 10, 2);
        
        // Add filter to log successful updates
        add_action('upgrader_process_complete', [$this, 'log_update_complete'], 10, 2);
    }

    /**
     * Log update check results
     *
     * @since 1.3.0
     * @param array $update_info Update information
     * @param array $result Result of the request
     * @return array
     */
    public function log_update_check($update_info, $result) {
        if (is_wp_error($result)) {
            WebP_Image_Converter_Logger::error('Update check failed', [
                'error' => $result->get_error_message(),
                'error_code' => $result->get_error_code()
            ]);
        } else {
            $current_version = defined('WEBP_IMAGE_CONVERTER_VERSION') ? WEBP_IMAGE_CONVERTER_VERSION : '1.0.0';
            $latest_version = isset($update_info['version']) ? $update_info['version'] : 'unknown';
            
            WebP_Image_Converter_Logger::info('Update check completed', [
                'current_version' => $current_version,
                'latest_version' => $latest_version,
                'update_available' => version_compare($current_version, $latest_version, '<'),
                'source' => 'GitHub Releases'
            ]);
        }

        return $update_info;
    }

    /**
     * Log when plugin update is completed
     *
     * @since 1.3.0
     * @param WP_Upgrader $upgrader_object
     * @param array $options
     */
    public function log_update_complete($upgrader_object, $options) {
        if ($options['action'] === 'update' && 
            $options['type'] === 'plugin' && 
            isset($options['plugins']) && 
            in_array(plugin_basename($this->plugin_file), $options['plugins'])) {
            
            WebP_Image_Converter_Logger::info('Plugin update completed successfully', [
                'updated_plugin' => plugin_basename($this->plugin_file),
                'update_type' => $options['type']
            ]);
        }
    }

    /**
     * Force check for updates
     *
     * @since 1.3.0
     * @return array|WP_Error
     */
    public function check_for_updates() {
        WebP_Image_Converter_Logger::info('Manual update check requested');
        
        if ($this->update_checker) {
            return $this->update_checker->checkForUpdates();
        }
        
        WebP_Image_Converter_Logger::error('Update checker not initialized');
        return new WP_Error('no_updater', 'Update checker not initialized');
    }

    /**
     * Get current update checker instance
     *
     * @since 1.3.0
     * @return Puc_v4p13_Plugin_UpdateChecker|null
     */
    public function get_update_checker() {
        return $this->update_checker;
    }

    /**
     * Set GitHub token for authentication
     *
     * @since 1.3.0
     * @param string $token GitHub personal access token
     */
    public function set_github_token($token) {
        update_option('webp_converter_github_token', sanitize_text_field($token));
        
        if ($this->update_checker) {
            $this->update_checker->setAuthentication($token);
        }
        
        WebP_Image_Converter_Logger::info('GitHub token updated for auto-updater');
    }

    /**
     * Remove GitHub token
     *
     * @since 1.3.0
     */
    public function remove_github_token() {
        delete_option('webp_converter_github_token');
        
        if ($this->update_checker) {
            $this->update_checker->setAuthentication(null);
        }
        
        WebP_Image_Converter_Logger::info('GitHub token removed from auto-updater');
    }
}
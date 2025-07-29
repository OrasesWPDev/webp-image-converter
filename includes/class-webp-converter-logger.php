<?php
/**
 * Logger class for debug functionality.
 *
 * @since      1.2.0
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/includes
 */
class WebP_Image_Converter_Logger {

    /**
     * Debug flag - must be defined as WEBP_IMAGE_CONVERTER_DEBUG constant
     *
     * @since 1.2.0
     * @var bool
     */
    private static $debug_enabled = null;

    /**
     * Log levels
     *
     * @since 1.2.0
     * @var array
     */
    const LOG_LEVELS = [
        'debug' => 'DEBUG',
        'info' => 'INFO',
        'warning' => 'WARNING',
        'error' => 'ERROR'
    ];

    /**
     * Initialize debug status
     *
     * @since 1.2.0
     * @return bool
     */
    private static function is_debug_enabled() {
        if (self::$debug_enabled === null) {
            self::$debug_enabled = defined('WEBP_IMAGE_CONVERTER_DEBUG') && WEBP_IMAGE_CONVERTER_DEBUG === true;
        }
        return self::$debug_enabled;
    }

    /**
     * Get log file path for current date
     *
     * @since 1.2.0
     * @return string
     */
    private static function get_log_file_path() {
        $log_dir = plugin_dir_path(dirname(__FILE__)) . 'logs/';
        $date = date('Y-m-d');
        return $log_dir . "webp-converter-{$date}.log";
    }

    /**
     * Ensure logs directory exists and is protected
     *
     * @since 1.2.0
     * @return bool
     */
    private static function ensure_log_directory() {
        $log_dir = plugin_dir_path(dirname(__FILE__)) . 'logs/';
        
        if (!is_dir($log_dir)) {
            if (!wp_mkdir_p($log_dir)) {
                return false;
            }
        }

        // Create index.php protection file
        $index_file = $log_dir . 'index.php';
        if (!file_exists($index_file)) {
            file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }

        // Create .htaccess protection file
        $htaccess_file = $log_dir . '.htaccess';
        if (!file_exists($htaccess_file)) {
            $htaccess_content = "# Deny direct access to log files\n";
            $htaccess_content .= "Order deny,allow\n";
            $htaccess_content .= "Deny from all\n";
            $htaccess_content .= "<Files ~ \"\.log$\">\n";
            $htaccess_content .= "    Order deny,allow\n";
            $htaccess_content .= "    Deny from all\n";
            $htaccess_content .= "</Files>\n";
            file_put_contents($htaccess_file, $htaccess_content);
        }

        return true;
    }

    /**
     * Write log message to file
     *
     * @since 1.2.0
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     */
    private static function write_log($level, $message, $context = []) {
        if (!self::is_debug_enabled()) {
            return;
        }

        if (!self::ensure_log_directory()) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $log_level = self::LOG_LEVELS[$level] ?? 'INFO';
        
        $context_string = '';
        if (!empty($context)) {
            $context_string = ' | Context: ' . json_encode($context);
        }

        $log_entry = "[{$timestamp}] [{$log_level}] {$message}{$context_string}" . PHP_EOL;
        
        $log_file = self::get_log_file_path();
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log debug message
     *
     * @since 1.2.0
     * @param string $message Debug message
     * @param array $context Additional context data
     */
    public static function debug($message, $context = []) {
        self::write_log('debug', $message, $context);
    }

    /**
     * Log info message
     *
     * @since 1.2.0
     * @param string $message Info message
     * @param array $context Additional context data
     */
    public static function info($message, $context = []) {
        self::write_log('info', $message, $context);
    }

    /**
     * Log warning message
     *
     * @since 1.2.0
     * @param string $message Warning message
     * @param array $context Additional context data
     */
    public static function warning($message, $context = []) {
        self::write_log('warning', $message, $context);
    }

    /**
     * Log error message
     *
     * @since 1.2.0
     * @param string $message Error message
     * @param array $context Additional context data
     */
    public static function error($message, $context = []) {
        self::write_log('error', $message, $context);
    }

    /**
     * Get JavaScript debug flag for frontend use
     *
     * @since 1.2.0
     * @return bool
     */
    public static function get_js_debug_flag() {
        return self::is_debug_enabled();
    }
}
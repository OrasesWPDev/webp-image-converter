<?php
/**
 * Handles the image conversion and processing.
 *
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/includes
 */
class WebP_Image_Converter_Processor {

    /**
     * Original image data
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $original_image    Base64 encoded original image
     */
    private $original_image = null;

    /**
     * Converted image data
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $converted_image    Base64 encoded WebP image
     */
    private $converted_image = null;

    /**
     * Original image metadata
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $original_meta    Original image metadata
     */
    private $original_meta = null;

    /**
     * Converted image metadata
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $converted_meta    Converted image metadata
     */
    private $converted_meta = null;

    /**
     * Error message
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $error_msg    Error message
     */
    private $error_msg = "";

    /**
     * Process an uploaded image
     *
     * @since    1.0.0
     * @return   boolean    True if successful, false otherwise
     */
    public function process_image() {
        // Verify nonce for security
        if (!isset($_POST['webp_converter_nonce']) || !wp_verify_nonce($_POST['webp_converter_nonce'], 'webp_converter_action')) {
            $this->error_msg = "Security verification failed.";
            return false;
        }

        if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != 0) {
            $this->error_msg = "Error uploading file. Code: " . (isset($_FILES["image"]) ? $_FILES["image"]["error"] : "No file uploaded");
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];

        // Verify it's an image
        $fileInfo = getimagesize($_FILES["image"]["tmp_name"]);
        if (!$fileInfo || !in_array($fileInfo['mime'], $allowedTypes)) {
            $this->error_msg = "Uploaded file is not a valid image.";
            return false;
        }

        // Get original image info
        $originalFile = $_FILES["image"]["tmp_name"];
        $originalSize = round($_FILES["image"]["size"] / 1024, 2); // KB
        $originalType = $_FILES["image"]["type"];
        $originalDimensions = $fileInfo[0] . ' × ' . $fileInfo[1]; // width × height
        $aspectRatio = round($fileInfo[0] / $fileInfo[1], 2);
        $this->original_meta = [
            'size' => $originalSize . ' KB',
            'type' => $originalType,
            'dimensions' => $originalDimensions,
            'aspectRatio' => $aspectRatio,
            'bits' => isset($fileInfo['bits']) ? $fileInfo['bits'] : 'Unknown'
        ];

        // Get dimensions from form
        $width = $_POST['width'];
        $height = $_POST['height'];

        // Check if using custom dimensions
        if ($_POST['sizeOption'] === 'custom') {
            $width = intval($_POST['customWidth']);
            $height = intval($_POST['customHeight']);
        }

        // Quality setting
        $quality = isset($_POST['quality']) ? intval($_POST['quality']) : 80;

        // Create source image based on its type
        switch ($fileInfo['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($originalFile);
                break;
            case 'image/png':
                $source = imagecreatefrompng($originalFile);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($originalFile);
                break;
            case 'image/bmp':
                $source = imagecreatefrombmp($originalFile);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($originalFile);
                break;
            default:
                $source = imagecreatefromjpeg($originalFile); // Fallback
        }

        if (!$source) {
            $this->error_msg = "Failed to create image resource.";
            return false;
        }

        // Create destination image with the specified dimensions
        $destination = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG images
        if ($fileInfo['mime'] === 'image/png') {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $width, $height, $transparent);
        }

        // Resize the image
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $fileInfo[0], $fileInfo[1]);

        // Create a temporary file for the converted WebP image
        $upload_dir = wp_upload_dir();
        $tmpWebpFile = $upload_dir['basedir'] . '/webp-converter-temp-' . uniqid() . '.webp';

        // Save as WebP
        imagewebp($destination, $tmpWebpFile, $quality);

        // Get converted image info
        $convertedSize = round(filesize($tmpWebpFile) / 1024, 2); // KB
        $convertedType = 'image/webp';
        $convertedDimensions = $width . ' × ' . $height;
        $newAspectRatio = round($width / $height, 2);
        $this->converted_meta = [
            'size' => $convertedSize . ' KB',
            'type' => $convertedType,
            'dimensions' => $convertedDimensions,
            'aspectRatio' => $newAspectRatio,
            'bits' => isset($fileInfo['bits']) ? $fileInfo['bits'] : 'Unknown',
            'quality' => $quality . '%'
        ];

        // Save original image details for display
        $this->original_image = base64_encode(file_get_contents($_FILES["image"]["tmp_name"]));
        $this->converted_image = base64_encode(file_get_contents($tmpWebpFile));

        // Release memory
        imagedestroy($source);
        imagedestroy($destination);
        unlink($tmpWebpFile);

        return true;
    }

    /**
     * Get error message
     *
     * @since    1.0.0
     * @return   string    Error message
     */
    public function get_error() {
        return $this->error_msg;
    }

    /**
     * Get original image data
     *
     * @since    1.0.0
     * @return   string    Base64 encoded original image
     */
    public function get_original_image() {
        return $this->original_image;
    }

    /**
     * Get converted image data
     *
     * @since    1.0.0
     * @return   string    Base64 encoded converted image
     */
    public function get_converted_image() {
        return $this->converted_image;
    }

    /**
     * Get original image metadata
     *
     * @since    1.0.0
     * @return   array    Original image metadata
     */
    public function get_original_meta() {
        return $this->original_meta;
    }

    /**
     * Get converted image metadata
     *
     * @since    1.0.0
     * @return   array    Converted image metadata
     */
    public function get_converted_meta() {
        return $this->converted_meta;
    }

    /**
     * Save converted image to media library
     *
     * @since    1.0.0
     * @return   array|WP_Error    Media library attachment data or error
     */
    public function save_to_media_library($title = '') {
        // Make sure we have a converted image
        if (!$this->converted_image) {
            return new WP_Error('no_image', __('No converted image available to save.', 'webp-image-converter'));
        }

        // Set a default title if none provided
        if (empty($title)) {
            $title = 'WebP Conversion ' . date('Y-m-d H:i:s');
        }

        // Create a file in the WordPress uploads directory
        $upload_dir = wp_upload_dir();
        $filename = wp_unique_filename($upload_dir['path'], sanitize_file_name($title) . '.webp');
        $filepath = $upload_dir['path'] . '/' . $filename;

        // Decode the base64 image and save it to the uploads directory
        $decoded_image = base64_decode($this->converted_image);
        $save_result = file_put_contents($filepath, $decoded_image);

        if (!$save_result) {
            return new WP_Error('save_error', __('Failed to save image to uploads directory.', 'webp-image-converter'));
        }

        // Get file type
        $filetype = wp_check_filetype($filename, null);

        // Prepare the attachment data
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => $title,
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment into the media library
        $attach_id = wp_insert_attachment($attachment, $filepath);

        if (is_wp_error($attach_id)) {
            return $attach_id;
        }

        // Generate metadata for the attachment
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return array(
            'id'  => $attach_id,
            'url' => wp_get_attachment_url($attach_id),
            'title' => $title,
            'filename' => $filename
        );
    }

    /**
     * Set converted image data
     *
     * @since    1.0.0
     * @param    string    $image_data    Base64 encoded image data
     */
    public function set_converted_image($image_data) {
        $this->converted_image = $image_data;
    }
}
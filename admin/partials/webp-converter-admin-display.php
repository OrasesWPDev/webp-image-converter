<?php
/**
 * Provide a admin area view for the plugin
 *
 * @package    WebP_Image_Converter
 * @subpackage WebP_Image_Converter/admin/partials
 */
?>

<div class="wrap webp-converter-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if ($error_msg): ?>
        <div class="notice notice-error"><p><?php echo esc_html($error_msg); ?></p></div>
    <?php endif; ?>

    <div class="card">
        <h2><?php _e('Upload & Convert', 'webp-image-converter'); ?></h2>
        <div class="inside">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('webp_converter_action', 'webp_converter_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="image"><?php _e('Select Image', 'webp-image-converter'); ?></label></th>
                        <td><input type="file" id="image" name="image" accept="image/*" required></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="sizeOption"><?php _e('Size Options', 'webp-image-converter'); ?></label></th>
                        <td>
                            <select id="sizeOption" name="sizeOption">
                                <option value="header"><?php _e('Main Header Images (1500 × 500)', 'webp-image-converter'); ?></option>
                                <option value="thumbnail"><?php _e('Blog Post Thumbnails (300 × 200)', 'webp-image-converter'); ?></option>
                                <option value="featured"><?php _e('Featured Images (1200 × 630)', 'webp-image-converter'); ?></option>
                                <option value="internal"><?php _e('Internal Page Images (500 × 500)', 'webp-image-converter'); ?></option>
                                <option value="gallery"><?php _e('Gallery Thumbnails (150 × 150)', 'webp-image-converter'); ?></option>
                                <option value="custom"><?php _e('Custom Dimensions', 'webp-image-converter'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <!-- Hidden fields to store the actual width and height values -->
                    <input type="hidden" id="width" name="width" value="1500">
                    <input type="hidden" id="height" name="height" value="500">

                    <tr id="customDimensions" style="display: none;">
                        <th scope="row"><?php _e('Custom Dimensions', 'webp-image-converter'); ?></th>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <div>
                                    <label for="customWidth"><?php _e('Width (px)', 'webp-image-converter'); ?></label>
                                    <input type="number" id="customWidth" name="customWidth" min="1" value="800" style="width: 100px;">
                                </div>
                                <div>
                                    <label for="customHeight"><?php _e('Height (px)', 'webp-image-converter'); ?></label>
                                    <input type="number" id="customHeight" name="customHeight" min="1" value="600" style="width: 100px;">
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="quality"><?php _e('WebP Quality (1-100)', 'webp-image-converter'); ?></label></th>
                        <td>
                            <input type="range" id="quality" name="quality" min="1" max="100" value="80" style="width: 300px;">
                            <div style="display: flex; justify-content: space-between; width: 300px;">
                                <small><?php _e('Lower quality', 'webp-image-converter'); ?></small>
                                <small id="qualityValue">80%</small>
                                <small><?php _e('Higher quality', 'webp-image-converter'); ?></small>
                            </div>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Convert to WebP', 'webp-image-converter')); ?>
            </form>
        </div>
    </div>

    <?php if ($original_image && $converted_image): ?>
    <div class="card" style="margin-top: 20px;">
        <h2><?php _e('Conversion Results', 'webp-image-converter'); ?></h2>
        <div class="inside">
            <div class="comparison-container">
                <div class="comparison-item">
                    <h3><?php _e('Original Image', 'webp-image-converter'); ?></h3>
                    <div class="image-container">
                        <img src="data:<?php echo esc_attr($original_meta['type']); ?>;base64,<?php echo $original_image; ?>" alt="Original Image">
                    </div>
                    <div class="meta-info">
                        <h4><?php _e('Metadata:', 'webp-image-converter'); ?></h4>
                        <ul>
                            <li><strong><?php _e('File Size:', 'webp-image-converter'); ?></strong> <?php echo esc_html($original_meta['size']); ?></li>
                            <li><strong><?php _e('File Type:', 'webp-image-converter'); ?></strong> <?php echo esc_html($original_meta['type']); ?></li>
                            <li><strong><?php _e('Dimensions:', 'webp-image-converter'); ?></strong> <?php echo esc_html($original_meta['dimensions']); ?></li>
                            <li><strong><?php _e('Aspect Ratio:', 'webp-image-converter'); ?></strong> <?php echo esc_html($original_meta['aspectRatio']); ?></li>
                            <li><strong><?php _e('Color Depth:', 'webp-image-converter'); ?></strong> <?php echo esc_html($original_meta['bits']); ?> bits</li>
                        </ul>
                    </div>
                </div>

                <div class="comparison-item">
                    <h3><?php _e('Converted WebP Image', 'webp-image-converter'); ?></h3>
                    <div class="image-container">
                        <img src="data:image/webp;base64,<?php echo $converted_image; ?>" alt="Converted Image">
                    </div>
                    <div class="meta-info">
                        <h4><?php _e('Metadata:', 'webp-image-converter'); ?></h4>
                        <ul>
                            <li><strong><?php _e('File Size:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['size']); ?></li>
                            <li><strong><?php _e('File Type:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['type']); ?></li>
                            <li><strong><?php _e('Dimensions:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['dimensions']); ?></li>
                            <li><strong><?php _e('Aspect Ratio:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['aspectRatio']); ?></li>
                            <li><strong><?php _e('Color Depth:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['bits']); ?> bits</li>
                            <li><strong><?php _e('Quality:', 'webp-image-converter'); ?></strong> <?php echo esc_html($converted_meta['quality']); ?></li>
                        </ul>
                    </div>
                    <div style="margin-top: 15px; text-align: center;">
                        <a href="data:image/webp;base64,<?php echo $converted_image; ?>" download="converted_image.webp" class="button button-secondary"><?php _e('Download WebP Image', 'webp-image-converter'); ?></a>
                        <button id="saveToMediaBtn" class="button button-primary"><?php _e('Save to Media Library', 'webp-image-converter'); ?></button>
                    </div>

                    <!-- Add a notification area for the media library save result -->
                    <div id="mediaLibraryNotice" class="notice" style="display: none; margin-top: 10px; padding: 5px 10px;"></div>
                </div>
            </div>

            <?php
            // Calculate size reduction percentage
            $originalSizeKB = floatval($original_meta['size']);
            $convertedSizeKB = floatval($converted_meta['size']);
            $reduction = $originalSizeKB > 0 ? round(($originalSizeKB - $convertedSizeKB) / $originalSizeKB * 100, 1) : 0;
            ?>

            <div class="notice notice-info" style="margin-top: 15px;">
                <p><strong><?php _e('Optimization Summary', 'webp-image-converter'); ?></strong></p>
                <p><?php printf(__('The WebP conversion reduced file size by <strong>%s%%</strong> (from %s to %s)', 'webp-image-converter'),
                        $reduction, $original_meta['size'], $converted_meta['size']); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
(function($) {
    'use strict';

    // Debug logging function
    function debugLog(message, data) {
        if (typeof webpConverterData !== 'undefined' && webpConverterData.debugEnabled) {
            console.log('[WebP Converter Debug] ' + message, data || '');
        }
    }

    $(document).ready(function() {
        debugLog('Admin script initialized', {
            debugEnabled: typeof webpConverterData !== 'undefined' ? webpConverterData.debugEnabled : false,
            ajaxUrl: typeof webpConverterData !== 'undefined' ? webpConverterData.ajaxUrl : 'undefined'
        });
        // Update quality value display
        $('#quality').on('input', function() {
            var qualityValue = $(this).val() + '%';
            $('#qualityValue').text(qualityValue);
            debugLog('Quality value changed', { quality: qualityValue });
        });

        // Toggle custom dimensions
        $('#sizeOption').on('change', function() {
            const sizeOption = $(this).val();
            const customDiv = $('#customDimensions');
            
            debugLog('Size option changed', { sizeOption: sizeOption });

            if (sizeOption === 'custom') {
                customDiv.show();
                debugLog('Custom dimensions shown');
            } else {
                customDiv.hide();

                // Update hidden fields based on the selection
                const widthField = $('#width');
                const heightField = $('#height');
                var dimensions = {};

                switch(sizeOption) {
                    case 'header':
                        widthField.val(1500);
                        heightField.val(500);
                        dimensions = { width: 1500, height: 500 };
                        break;
                    case 'thumbnail':
                        widthField.val(300);
                        heightField.val(200);
                        dimensions = { width: 300, height: 200 };
                        break;
                    case 'featured':
                        widthField.val(1200);
                        heightField.val(630);
                        dimensions = { width: 1200, height: 630 };
                        break;
                    case 'internal':
                        widthField.val(500);
                        heightField.val(500);
                        dimensions = { width: 500, height: 500 };
                        break;
                    case 'gallery':
                        widthField.val(150);
                        heightField.val(150);
                        dimensions = { width: 150, height: 150 };
                        break;
                }
                debugLog('Preset dimensions applied', dimensions);
            }
        });

        // Media Library Save functionality
        $('#saveToMediaBtn').on('click', function() {
            debugLog('Save button clicked');

            const btn = $(this);
            const noticeArea = $('#mediaLibraryNotice');

            debugLog('Button and notice area found', { buttonFound: btn.length > 0, noticeAreaFound: noticeArea.length > 0 });

            // Disable button and show loading state
            btn.prop('disabled', true).text('Saving...');

            // Get the base64 image data
            const imageData = $('img[alt="Converted Image"]').attr('src');
            debugLog('Image data retrieval', { imageDataFound: !!imageData, imageDataLength: imageData ? imageData.length : 0 });

            // Get original file name if available
            let originalFileName = $('#image').val().split('\\').pop();
            debugLog('Original filename extracted', { originalFileName: originalFileName });

            // Remove extension
            if (originalFileName) {
                originalFileName = originalFileName.substring(0, originalFileName.lastIndexOf('.')) || originalFileName;
                originalFileName += '-webp'; // Add webp identifier
            }

            // Make AJAX request
            $.ajax({
                url: typeof ajaxurl !== 'undefined' ? ajaxurl : webpConverterData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'webp_save_to_media_library',
                    nonce: webpConverterData.saveMediaNonce,
                    image_data: imageData,
                    title: originalFileName
                },
                success: function(response) {
                    debugLog('AJAX response received', response);
                    if (response.success) {
                        // Show success message
                        noticeArea.removeClass('notice-error').addClass('notice-success')
                            .html('<p>' + response.data.message + '</p><p><a href="' + response.data.attachment.url + '" target="_blank">View Image</a> | <a href="/wp-admin/post.php?post=' + response.data.attachment.id + '&action=edit" target="_blank">Edit in Media Library</a></p>')
                            .slideDown();
                    } else {
                        // Show error message
                        noticeArea.removeClass('notice-success').addClass('notice-error')
                            .html('<p>Error: ' + response.data.message + '</p>')
                            .slideDown();
                    }
                },
                error: function(xhr, status, error) {
                    debugLog('AJAX error occurred', { status: status, error: error, xhr: xhr });
                    // Show generic error message
                    noticeArea.removeClass('notice-success').addClass('notice-error')
                        .html('<p>Error: Could not connect to the server. ' + error + '</p>')
                        .slideDown();
                },
                complete: function() {
                    // Reset button state
                    btn.prop('disabled', false).text('Save to Media Library');
                    debugLog('AJAX request completed, button state reset');
                }
            });
        });
    });
})(jQuery);
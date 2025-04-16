(function($) {
    'use strict';

    $(document).ready(function() {
        // Update quality value display
        $('#quality').on('input', function() {
            $('#qualityValue').text($(this).val() + '%');
        });

        // Toggle custom dimensions
        $('#sizeOption').on('change', function() {
            const sizeOption = $(this).val();
            const customDiv = $('#customDimensions');

            if (sizeOption === 'custom') {
                customDiv.show();
            } else {
                customDiv.hide();

                // Update hidden fields based on the selection
                const widthField = $('#width');
                const heightField = $('#height');

                switch(sizeOption) {
                    case 'header':
                        widthField.val(1500);
                        heightField.val(500);
                        break;
                    case 'thumbnail':
                        widthField.val(300);
                        heightField.val(200);
                        break;
                    case 'featured':
                        widthField.val(1200);
                        heightField.val(630);
                        break;
                    case 'internal':
                        widthField.val(500);
                        heightField.val(500);
                        break;
                    case 'gallery':
                        widthField.val(150);
                        heightField.val(150);
                        break;
                }
            }
        });

        // Media Library Save functionality
        $('#saveToMediaBtn').on('click', function() {
            console.log('Save button clicked');

            const btn = $(this);
            const noticeArea = $('#mediaLibraryNotice');

            console.log('Button and notice area:', btn.length, noticeArea.length);

            // Disable button and show loading state
            btn.prop('disabled', true).text('Saving...');

            // Get the base64 image data
            const imageData = $('img[alt="Converted Image"]').attr('src');
            console.log('Image data found?', !!imageData);

            // Get original file name if available
            let originalFileName = $('#image').val().split('\\').pop();
            console.log('Original filename:', originalFileName);

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
                    console.log('AJAX response:', response);
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
                    console.log('AJAX error:', status, error);
                    // Show generic error message
                    noticeArea.removeClass('notice-success').addClass('notice-error')
                        .html('<p>Error: Could not connect to the server. ' + error + '</p>')
                        .slideDown();
                },
                complete: function() {
                    // Reset button state
                    btn.prop('disabled', false).text('Save to Media Library');
                }
            });
        });
    });
})(jQuery);
jQuery(document).ready(function($) {
    jQuery(document).on('click', '#convert-to-webp', function() {
        console.log('aaaa');
        var attachmentId = $(this).data('attachment-id');
        var $button = $(this);
        $button.attr('disabled', true).text('Converting...');

        $.ajax({
            url: webpConverter.ajax_url,
            method: 'POST',
            data: {
                action: 'convert_to_webp',
                attachment_id: attachmentId,
                nonce: webpConverter.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Image converted to WebP successfully.');
                    location.reload(); // รีเฟรชหน้าเว็บหลังจากแสดง alert
                } else {
                    alert('Failed to convert the image.');
                    $button.attr('disabled', false).text('Convert to WebP');
                }
            }
        });
    });
});

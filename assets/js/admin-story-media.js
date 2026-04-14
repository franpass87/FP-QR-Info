/**
 * Media picker per immagine storia (bottiglia) nelle QR Landing.
 */
(function ($) {
    'use strict';

    $(function () {
        var frame;
        var $input = $('#fp_qr_info_story_image_id');
        var $preview = $('#fp-qri-story-image-preview');
        var $select = $('#fp-qri-story-select-image');
        var $remove = $('#fp-qri-story-remove-image');

        if (!$input.length || !$select.length) {
            return;
        }

        $select.on('click', function (e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: FP_QRI_STORY_MEDIA.title,
                button: { text: FP_QRI_STORY_MEDIA.button },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.id);
                if ($preview.length && attachment.url) {
                    $preview.empty();
                    $('<img/>', {
                        src: attachment.url,
                        alt: '',
                        css: {
                            maxWidth: '100%',
                            height: 'auto',
                            borderRadius: '8px',
                            border: '1px solid #dcdcde'
                        }
                    }).appendTo($preview);
                }
                $remove.show();
            });
            frame.open();
        });

        $remove.on('click', function (e) {
            e.preventDefault();
            $input.val('0');
            $preview.empty();
            $(this).hide();
        });
    });
}(jQuery));

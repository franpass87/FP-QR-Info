/**
 * Inserimento modelli normativi e esempio blocchi smaltimento (Tappo / Bottiglia / Capsula).
 */
(function ($) {
    'use strict';

    $(function () {
        var cfg = window.FP_QRI_LANDING_PRESETS_CFG || {};
        var presets = cfg.presets || {};
        var confirmMsg = cfg.confirmOverwrite || '';
        var confirmBlocks = cfg.confirmDisposalBlocks || '';
        var blockExamples = cfg.disposalBlockExamples || {};
        var blockSlugs = cfg.disposalBlockSlugs || ['cork', 'bottle', 'capsule'];

        function disposalBlocksHaveInput() {
            return blockSlugs.some(function (slug) {
                return ['code', 'it', 'en'].some(function (suffix) {
                    var id = suffix === 'code'
                        ? '#fp_qr_info_disposal_block_' + slug + '_code'
                        : '#fp_qr_info_disposal_block_' + slug + '_' + suffix;
                    var $el = $(id);
                    return $el.length && ($el.val() || '').trim() !== '';
                });
            });
        }

        $(document).on('click', '.fp-qri-insert-preset', function (e) {
            e.preventDefault();
            var key = $(this).data('preset');

            if (key === 'disposal-blocks') {
                if (disposalBlocksHaveInput() && confirmBlocks && !window.confirm(confirmBlocks)) {
                    return;
                }
                blockSlugs.forEach(function (slug) {
                    var row = blockExamples[slug] || {};
                    $('#fp_qr_info_disposal_block_' + slug + '_code').val(typeof row.code === 'string' ? row.code : '');
                    $('#fp_qr_info_disposal_block_' + slug + '_it').val(typeof row.it === 'string' ? row.it : '');
                    $('#fp_qr_info_disposal_block_' + slug + '_en').val(typeof row.en === 'string' ? row.en : '');
                });
                return;
            }

            if (!key || !presets[key]) {
                return;
            }
            var pack = presets[key];
            var $it = $('#fp_qr_info_' + key + '_it');
            var $en = $('#fp_qr_info_' + key + '_en');
            if (!$it.length || !$en.length) {
                return;
            }
            var hasContent = ($it.val() || '').trim() !== '' || ($en.val() || '').trim() !== '';
            if (hasContent && confirmMsg && !window.confirm(confirmMsg)) {
                return;
            }
            $it.val(typeof pack.it === 'string' ? pack.it : '');
            $en.val(typeof pack.en === 'string' ? pack.en : '');
        });
    });
}(jQuery));

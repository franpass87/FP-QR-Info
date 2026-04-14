/**
 * Inserimento modelli normativi (smaltimento, nutrizionali, ingredienti) nelle textarea landing.
 */
(function ($) {
    'use strict';

    $(function () {
        var cfg = window.FP_QRI_LANDING_PRESETS_CFG || {};
        var presets = cfg.presets || {};
        var confirmMsg = cfg.confirmOverwrite || '';

        $(document).on('click', '.fp-qri-insert-preset', function (e) {
            e.preventDefault();
            var key = $(this).data('preset');
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

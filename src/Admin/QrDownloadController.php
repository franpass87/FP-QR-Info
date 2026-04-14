<?php

declare(strict_types=1);

namespace FP\QrInfo\Admin;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

/**
 * Download QR PNG/SVG per landing token.
 */
final class QrDownloadController
{
    /**
     * Registra endpoint admin-post per download QR.
     */
    public function register(): void
    {
        add_action('admin_post_fp_qr_info_download', [$this, 'handleDownload']);
        add_action('admin_post_fp_qr_info_print_label', [$this, 'handlePrintLabel']);
    }

    /**
     * Gestisce download QR code richiesto da admin.
     */
    public function handleDownload(): void
    {
        $postId = isset($_GET['post_id']) ? absint((string) $_GET['post_id']) : 0;
        $format = isset($_GET['format']) ? sanitize_key((string) $_GET['format']) : 'png';
        $inline = isset($_GET['inline']) && sanitize_key((string) $_GET['inline']) === '1';

        if ($postId <= 0 || !current_user_can('edit_post', $postId)) {
            wp_die(esc_html__('Permessi insufficienti.', 'fp-qr-info'));
        }

        check_admin_referer('fp_qr_info_download_' . $postId);

        $token = (string) get_post_meta($postId, 'fp_qr_info_token', true);
        if ($token === '') {
            wp_die(esc_html__('Token non trovato.', 'fp-qr-info'));
        }

        $landingUrl = home_url('/qr-info/' . rawurlencode($token));
        $qrCode = new QrCode(
            data: $landingUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 900,
            margin: 14
        );

        $filenameBase = 'fp-qr-info-' . $postId . '-' . $token;

        try {
            if ($format === 'svg') {
                $result = $this->runWithoutDeprecated(
                    static fn (): \Endroid\QrCode\Writer\Result\ResultInterface => (new SvgWriter())->write($qrCode)
                );
                $this->prepareBinaryResponse();
                header('Content-Type: image/svg+xml; charset=UTF-8');
                header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $filenameBase . '.svg"');
                echo $result->getString();
                exit;
            }

            $result = $this->runWithoutDeprecated(
                static fn (): \Endroid\QrCode\Writer\Result\ResultInterface => (new PngWriter())->write($qrCode)
            );
            $this->prepareBinaryResponse();
            header('Content-Type: image/png');
            header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $filenameBase . '.png"');
            echo $result->getString();
        } catch (\Throwable $exception) {
            wp_die(esc_html__('Generazione QR non riuscita.', 'fp-qr-info'));
        }

        exit;
    }

    /**
     * Genera pagina HTML stampabile per etichetta con QR.
     */
    public function handlePrintLabel(): void
    {
        $postId = isset($_GET['post_id']) ? absint((string) $_GET['post_id']) : 0;
        if ($postId <= 0 || !current_user_can('edit_post', $postId)) {
            wp_die(esc_html__('Permessi insufficienti.', 'fp-qr-info'));
        }

        check_admin_referer('fp_qr_info_download_' . $postId);

        $token = (string) get_post_meta($postId, 'fp_qr_info_token', true);
        if ($token === '') {
            wp_die(esc_html__('Token non trovato.', 'fp-qr-info'));
        }

        $landingUrl = home_url('/qr-info/' . rawurlencode($token));
        $title = get_the_title($postId);

        $qrCode = new QrCode(
            data: $landingUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 900,
            margin: 10
        );
        $qrSvg = $this->runWithoutDeprecated(
            static fn (): string => (new SvgWriter())->write($qrCode)->getString()
        );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html__('Stampa etichetta QR', 'fp-qr-info'); ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f6f7fb; }
                .fpqri-print-wrap { max-width: 420px; margin: 0 auto; }
                .fpqri-label {
                    background: #fff;
                    border: 1px solid #dcdcde;
                    border-radius: 12px;
                    padding: 18px;
                    text-align: center;
                }
                .fpqri-label h1 { font-size: 1rem; margin: 0 0 12px; }
                .fpqri-qrcode svg { width: 240px; height: 240px; }
                .fpqri-url { font-size: 11px; color: #4b5563; margin-top: 12px; word-break: break-all; }
                .fpqri-actions { margin-top: 14px; text-align: center; }
                .fpqri-actions button { padding: 8px 14px; }
                @media print {
                    body { background: #fff; padding: 0; }
                    .fpqri-actions { display: none; }
                    .fpqri-label { border: none; border-radius: 0; }
                }
            </style>
        </head>
        <body>
            <div class="fpqri-print-wrap">
                <div class="fpqri-label">
                    <h1><?php echo esc_html($title); ?></h1>
                    <div class="fpqri-qrcode"><?php echo $qrSvg; ?></div>
                    <div class="fpqri-url"><?php echo esc_html($landingUrl); ?></div>
                </div>
                <div class="fpqri-actions">
                    <button type="button" onclick="window.print()"><?php esc_html_e('Stampa', 'fp-qr-info'); ?></button>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Esegue callback QR evitando output di warning/deprecated nei binary response.
     *
     * @template T
     * @param callable():T $callback Callback da eseguire.
     * @return T
     */
    private function runWithoutDeprecated(callable $callback): mixed
    {
        $previousDisplayErrors = ini_get('display_errors');
        $previousReporting = error_reporting();

        ini_set('display_errors', '0');
        error_reporting($previousReporting & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        set_error_handler(
            static function (int $severity): bool {
                if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
                    return true;
                }

                return false;
            }
        );

        try {
            return $callback();
        } finally {
            restore_error_handler();
            error_reporting($previousReporting);
            if (is_string($previousDisplayErrors)) {
                ini_set('display_errors', $previousDisplayErrors);
            }
        }
    }

    /**
     * Pulisce eventuale output buffer prima di inviare payload binario.
     */
    private function prepareBinaryResponse(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}

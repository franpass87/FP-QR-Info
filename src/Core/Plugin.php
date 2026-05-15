<?php

declare(strict_types=1);

namespace FP\QrInfo\Core;

use FP\QrInfo\Admin\AdminMenu;
use FP\QrInfo\Admin\LandingCpt;
use FP\QrInfo\Admin\QrDownloadController;
use FP\QrInfo\Frontend\LandingRouter;

/**
 * Bootstrap principale del plugin FP QR Info.
 */
final class Plugin
{
    /**
     * Registra hook e servizi runtime.
     */
    public function register(): void
    {
        (new AdminMenu())->register();
        (new LandingCpt())->register();
        (new LandingRouter())->register();
        (new QrDownloadController())->register();
        add_action('admin_notices', [$this, 'maybeWarnOnSyncedPluginPath']);
    }

    /**
     * Hook di attivazione plugin.
     */
    public static function activate(): void
    {
        (new LandingRouter())->addRewriteRules();
        flush_rewrite_rules();
    }

    /**
     * Hook di disattivazione plugin.
     */
    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }

    /**
     * Mostra un avviso admin se il plugin risiede in una cartella sincronizzata cloud.
     * In questo progetto i parse error ciclici sono comparsi su path OneDrive/junction.
     */
    public function maybeWarnOnSyncedPluginPath(): void
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }

        $pluginPath = wp_normalize_path((string) realpath(FP_QR_INFO_DIR));
        if ($pluginPath === '' || !$this->isCloudSyncedPath($pluginPath)) {
            return;
        }

        $message = __(
            'FP QR Info: rilevato path plugin in cartella cloud sincronizzata (es. OneDrive). Questo setup puo causare corruzioni intermittenti dei file PHP e parse error al boot. Per stabilita, sposta il repository in un percorso locale non sincronizzato e ricollega il plugin.',
            'fp-qr-info'
        );

        echo '<div class="notice notice-warning"><p>' . esc_html($message) . '</p></div>';
    }

    /**
     * Verifica se il path include provider cloud noti.
     */
    private function isCloudSyncedPath(string $path): bool
    {
        $normalized = strtolower(str_replace('\\', '/', $path));
        $markers = ['/onedrive/', '/dropbox/', '/google drive/', '/icloud drive/'];
        foreach ($markers as $marker) {
            if (str_contains($normalized, $marker)) {
                return true;
            }
        }

        return false;
    }
}

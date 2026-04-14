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
}

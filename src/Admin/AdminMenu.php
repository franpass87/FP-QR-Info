<?php

declare(strict_types=1);

namespace FP\QrInfo\Admin;

/**
 * Menu admin e dashboard grafica FP per il plugin.
 */
final class AdminMenu
{
    /**
     * Slug pagina dashboard plugin.
     */
    public const MENU_SLUG = 'fp_qr_info_dashboard';

    /**
     * Registra menu, submenu ed enqueue CSS admin.
     */
    public function register(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * Registra menu principale con prefisso FP.
     */
    public function registerMenu(): void
    {
        add_menu_page(
            esc_html__('FP QR Info', 'fp-qr-info'),
            esc_html__('FP QR Info', 'fp-qr-info'),
            'edit_posts',
            self::MENU_SLUG,
            [$this, 'renderDashboard'],
            'dashicons-media-code',
            '56.18'
        );
    }

    /**
     * Carica CSS admin del design dashboard/CPT.
     *
     * @param string $hook Hook corrente.
     */
    public function enqueueAssets(string $hook): void
    {
        $isPluginDashboard = str_contains($hook, self::MENU_SLUG);
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $isQrCptScreen = $screen instanceof \WP_Screen && $screen->post_type === 'fp_qr_landing';

        if (!$isPluginDashboard && !$isQrCptScreen) {
            return;
        }

        wp_enqueue_style(
            'fp-qr-info-admin',
            FP_QR_INFO_URL . 'assets/css/admin.css',
            [],
            FP_QR_INFO_VERSION
        );
    }

    /**
     * Render dashboard admin in stile FP.
     */
    public function renderDashboard(): void
    {
        $listUrl = admin_url('edit.php?post_type=fp_qr_landing');
        $newUrl = admin_url('post-new.php?post_type=fp_qr_landing');
        ?>
        <div class="wrap fpqri-admin-page">
            <h1 class="screen-reader-text"><?php esc_html_e('FP QR Info', 'fp-qr-info'); ?></h1>

            <div class="fpqri-page-header">
                <div class="fpqri-page-header-content">
                    <h2 class="fpqri-page-header-title" aria-hidden="true">
                        <span class="dashicons dashicons-media-code"></span>
                        <?php esc_html_e('FP QR Info', 'fp-qr-info'); ?>
                    </h2>
                    <p><?php esc_html_e('Landing standalone IT/EN per etichette QR code, non indicizzabili solo sulle route del plugin.', 'fp-qr-info'); ?></p>
                </div>
                <span class="fpqri-page-header-badge">v<?php echo esc_html(FP_QR_INFO_VERSION); ?></span>
            </div>

            <div class="fpqri-card">
                <h3><?php esc_html_e('Operativita', 'fp-qr-info'); ?></h3>
                <p><?php esc_html_e('Crea e gestisci landing pubbliche con token, contenuti IT/EN e download QR in PNG/SVG.', 'fp-qr-info'); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo esc_url($newUrl); ?>"><?php esc_html_e('Crea nuova landing', 'fp-qr-info'); ?></a>
                    <a class="button" href="<?php echo esc_url($listUrl); ?>"><?php esc_html_e('Vai alla lista landing', 'fp-qr-info'); ?></a>
                </p>
            </div>

            <div class="fpqri-card">
                <h3><?php esc_html_e('Best practice QR', 'fp-qr-info'); ?></h3>
                <ul class="fpqri-list">
                    <li><?php esc_html_e('Mantieni token non banali e univoci.', 'fp-qr-info'); ?></li>
                    <li><?php esc_html_e('Usa il formato SVG per stampa ad alta qualita.', 'fp-qr-info'); ?></li>
                    <li><?php esc_html_e('Verifica il contenuto in ITA e ENG prima di stampare le etichette.', 'fp-qr-info'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}

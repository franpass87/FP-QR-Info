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
        add_action('admin_menu', [$this, 'normalizeSubmenuLabels'], 99);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_filter('admin_body_class', [$this, 'filterAdminBodyClass']);
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

        add_submenu_page(
            self::MENU_SLUG,
            esc_html__('Dashboard', 'fp-qr-info'),
            esc_html__('Dashboard', 'fp-qr-info'),
            'edit_posts',
            self::MENU_SLUG,
            [$this, 'renderDashboard']
        );
    }

    /**
     * Forza etichetta "Dashboard" sulla prima voce submenu del plugin.
     */
    public function normalizeSubmenuLabels(): void
    {
        global $submenu;
        if (!isset($submenu[self::MENU_SLUG]) || !is_array($submenu[self::MENU_SLUG])) {
            return;
        }

        foreach ($submenu[self::MENU_SLUG] as $index => $item) {
            if (!isset($item[2]) || $item[2] !== self::MENU_SLUG) {
                continue;
            }

            $submenu[self::MENU_SLUG][$index][0] = esc_html__('Dashboard', 'fp-qr-info');
            break;
        }
    }

    /**
     * Carica CSS admin del design dashboard/CPT.
     *
     * @param string $hook Hook corrente.
     */
    public function enqueueAssets(string $hook): void
    {
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash((string) $_GET['page'])) : '';
        $isPluginDashboard = str_contains($hook, self::MENU_SLUG) || $page === self::MENU_SLUG;
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
     * Aggiunge classe body sulle schermate del plugin (margini e skin coerenti).
     *
     * @param string $classes Classi esistenti (prefisso spazio lasciato a WP).
     * @return string Classi con suffisso plugin.
     */
    public function filterAdminBodyClass(string $classes): string
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $isDashboard = $screen instanceof \WP_Screen
            && ($screen->id === 'toplevel_page_' . self::MENU_SLUG || str_contains((string) $screen->id, self::MENU_SLUG));
        $isCpt = $screen instanceof \WP_Screen && $screen->post_type === 'fp_qr_landing';

        if ($isDashboard || $isCpt) {
            return $classes . ' fpqri-admin-shell';
        }

        return $classes;
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
                    <p class="fpqri-page-header-desc"><?php esc_html_e('Landing standalone IT/EN per etichette QR code, non indicizzabili solo sulle route del plugin.', 'fp-qr-info'); ?></p>
                </div>
                <span class="fpqri-page-header-badge">v<?php echo esc_html(FP_QR_INFO_VERSION); ?></span>
            </div>

            <div class="fpqri-status-bar" role="status">
                <span class="fpqri-status-pill is-active">
                    <span class="dot" aria-hidden="true"></span>
                    <?php esc_html_e('Plugin attivo', 'fp-qr-info'); ?>
                </span>
                <span class="fpqri-status-pill is-active">
                    <span class="dot" aria-hidden="true"></span>
                    <?php esc_html_e('Route pubblica /qr-info/{token}', 'fp-qr-info'); ?>
                </span>
            </div>

            <div class="fpqri-card">
                <div class="fpqri-card-header">
                    <div class="fpqri-card-header-left">
                        <span class="dashicons dashicons-plus-alt" aria-hidden="true"></span>
                        <h2><?php esc_html_e('OperativitÃƒÂ ', 'fp-qr-info'); ?></h2>
                    </div>
                    <span class="fpqri-badge fpqri-badge-success"><?php esc_html_e('Pronto', 'fp-qr-info'); ?></span>
                </div>
                <div class="fpqri-card-body">
                    <p class="description"><?php esc_html_e('Crea e gestisci landing pubbliche con token, contenuti IT/EN e download QR in PNG/SVG.', 'fp-qr-info'); ?></p>
                    <div class="fpqri-card-actions">
                        <a class="fpqri-btn fpqri-btn-primary fpqri-btn-lg" href="<?php echo esc_url($newUrl); ?>"><?php esc_html_e('Crea nuova landing', 'fp-qr-info'); ?></a>
                        <a class="fpqri-btn fpqri-btn-secondary fpqri-btn-lg" href="<?php echo esc_url($listUrl); ?>"><?php esc_html_e('Vai alla lista landing', 'fp-qr-info'); ?></a>
                    </div>
                </div>
            </div>

            <div class="fpqri-card">
                <div class="fpqri-card-header">
                    <div class="fpqri-card-header-left">
                        <span class="dashicons dashicons-info" aria-hidden="true"></span>
                        <h2><?php esc_html_e('Best practice QR', 'fp-qr-info'); ?></h2>
                    </div>
                    <span class="fpqri-badge fpqri-badge-neutral"><?php esc_html_e('Guida', 'fp-qr-info'); ?></span>
                </div>
                <div class="fpqri-card-body">
                    <ul class="fpqri-list">
                        <li><?php esc_html_e('Mantieni token non banali e univoci.', 'fp-qr-info'); ?></li>
                        <li><?php esc_html_e('Usa il formato SVG per stampa ad alta qualitÃƒÂ .', 'fp-qr-info'); ?></li>
                        <li><?php esc_html_e('Verifica il contenuto in ITA e ENG prima di stampare le etichette.', 'fp-qr-info'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}

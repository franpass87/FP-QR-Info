<?php

declare(strict_types=1);

namespace FP\QrInfo\Frontend;

use FP\QrInfo\Content\LandingLegalPresets;

/**
 * Router pubblico per landing standalone QR.
 */
final class LandingRouter
{
    /**
     * Registra hook di routing pubblico.
     */
    public function register(): void
    {
        add_action('init', [$this, 'addRewriteRules']);
        add_filter('query_vars', [$this, 'registerQueryVars']);
        add_action('template_redirect', [$this, 'handleRequest'], 1);
    }

    /**
     * Registra rewrite rule /qr-info/{token}.
     */
    public function addRewriteRules(): void
    {
        add_rewrite_rule(
            '^qr-info/([a-zA-Z0-9_-]+)/?$',
            'index.php?fp_qr_info_token=$matches[1]',
            'top'
        );
    }

    /**
     * Registra query var custom.
     *
     * @param array<int, string> $vars Query vars esistenti.
     * @return array<int, string>
     */
    public function registerQueryVars(array $vars): array
    {
        $vars[] = 'fp_qr_info_token';
        return $vars;
    }

    /**
     * Intercetta la request e renderizza landing standalone.
     */
    public function handleRequest(): void
    {
        $token = get_query_var('fp_qr_info_token');
        if (!is_string($token) || $token === '') {
            return;
        }

        $post = $this->findPostByToken($token);
        if (!$post instanceof \WP_Post || $post->post_status !== 'publish') {
            status_header(404);
            nocache_headers();
            header('X-Robots-Tag: noindex, nofollow, noarchive', true);
            echo esc_html__('Contenuto non disponibile.', 'fp-qr-info');
            exit;
        }

        status_header(200);
        nocache_headers();
        header('X-Robots-Tag: noindex, nofollow, noarchive', true);
        $this->renderStandalonePage($post);
        exit;
    }

    /**
     * Cerca una landing pubblicata per token.
     *
     * @param string $token Token URL.
     * @return \WP_Post|null
     */
    private function findPostByToken(string $token): ?\WP_Post
    {
        $query = new \WP_Query(
            [
                'post_type' => 'fp_qr_landing',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'meta_key' => 'fp_qr_info_token',
                'meta_value' => sanitize_key($token),
                'no_found_rows' => true,
            ]
        );

        if (!$query->have_posts()) {
            return null;
        }

        $post = $query->posts[0] ?? null;
        wp_reset_postdata();
        return $post instanceof \WP_Post ? $post : null;
    }

    /**
     * Render HTML standalone IT/EN.
     *
     * @param \WP_Post $post Landing post.
     */
    private function renderStandalonePage(\WP_Post $post): void
    {
        $title = get_the_title($post);
        $sectionHeadline = [
            'it' => __('INFORMAZIONI DI SMALTIMENTO, NUTRIZIONALI E INGREDIENTI', 'fp-qr-info'),
            'en' => __('DISPOSAL, NUTRITIONAL INFO AND INGREDIENTS', 'fp-qr-info'),
        ];
        $accentColor = $this->resolveAccentColor($post->ID);
        $useDisposalBlocks = $this->disposalBlocksInUse($post->ID);
        $sections = [
            'disposal' => [
                'it' => $useDisposalBlocks
                    ? $this->buildDisposalBlocksHtml($post->ID, 'it')
                    : $this->prepareSectionBody((string) get_post_meta($post->ID, 'fp_qr_info_disposal_it', true)),
                'en' => $useDisposalBlocks
                    ? $this->buildDisposalBlocksHtml($post->ID, 'en')
                    : $this->prepareSectionBody((string) get_post_meta($post->ID, 'fp_qr_info_disposal_en', true)),
                'label_it' => __('Etichetta ambientale / Imballaggi', 'fp-qr-info'),
                'label_en' => __('Environmental labelling / Packaging', 'fp-qr-info'),
            ],
            'nutrition' => [
                'it' => $this->prepareSectionBody($this->normalizeLegacyNutritionCopy((string) get_post_meta($post->ID, 'fp_qr_info_nutrition_it', true), 'it')),
                'en' => $this->prepareSectionBody($this->normalizeLegacyNutritionCopy((string) get_post_meta($post->ID, 'fp_qr_info_nutrition_en', true), 'en')),
                'label_it' => __('INFORMAZIONI NUTRIZIONALI', 'fp-qr-info'),
                'label_en' => __('NUTRITIONAL INFO', 'fp-qr-info'),
            ],
            'ingredients' => [
                'it' => $this->prepareSectionBody($this->normalizeLegacyIngredientsCopy((string) get_post_meta($post->ID, 'fp_qr_info_ingredients_it', true), 'it')),
                'en' => $this->prepareSectionBody($this->normalizeLegacyIngredientsCopy((string) get_post_meta($post->ID, 'fp_qr_info_ingredients_en', true), 'en')),
                'label_it' => __('INGREDIENTI', 'fp-qr-info'),
                'label_en' => __('INGREDIENTS', 'fp-qr-info'),
            ],
        ];

        $storyImageId = (int) get_post_meta($post->ID, 'fp_qr_info_story_image_id', true);
        $storyIt = (string) get_post_meta($post->ID, 'fp_qr_info_story_it', true);
        $storyEn = (string) get_post_meta($post->ID, 'fp_qr_info_story_en', true);
        $storyImageUrl = '';
        if ($storyImageId > 0) {
            $storyImageUrl = (string) wp_get_attachment_image_url($storyImageId, 'full');
        }
        $storyTitleIt = __('Storia ed etichetta', 'fp-qr-info');
        $storyTitleEn = __('Story & label', 'fp-qr-info');
        $storyHasImage = $storyImageUrl !== '';
        $storyHasText = $storyIt !== '' || $storyEn !== '';
        $storyShowHero = $storyHasImage;
        $storyShowBlock = !$storyHasImage && $storyHasText;

        $i18nPayload = [
            'sections' => [],
            'sectionHeadline' => $sectionHeadline,
            'story' => [
                'mode' => $storyShowHero ? 'hero' : ($storyShowBlock ? 'card' : 'none'),
                'title' => ['it' => $storyTitleIt, 'en' => $storyTitleEn],
                'body' => ['it' => $storyIt, 'en' => $storyEn],
            ],
        ];
        foreach ($sections as $key => $section) {
            $i18nPayload['sections'][] = [
                'id' => $key,
                'label' => ['it' => $section['label_it'], 'en' => $section['label_en']],
                'body' => ['it' => $section['it'], 'en' => $section['en']],
            ];
        }
        $i18nJson = wp_json_encode(
            $i18nPayload,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );
        if ($i18nJson === false) {
            $i18nJson = '{}';
        }
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="noindex, nofollow, noarchive">
            <title><?php echo esc_html($title); ?></title>
            <style>
                :root {
                    --fpqi-bg: #f7f8fb;
                    --fpqi-surface: #ffffff;
                    --fpqi-text: #1f2937;
                    --fpqi-muted: #4b5563;
                    --fpqi-primary: <?php echo esc_html($accentColor); ?>;
                    --fpqi-border: #e5e7eb;
                    --fpqi-radius: 14px;
                }
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                    background: var(--fpqi-bg);
                    color: var(--fpqi-text);
                    padding: 0 0 40px;
                }
                .fpqi-wrap { max-width: 720px; margin: 0 auto; padding: 24px 16px 0; }
                .fpqi-story-hero {
                    position: relative;
                    min-height: 100vh;
                    min-height: 100dvh;
                    width: 100%;
                    background: #ffffff;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                .fpqi-story-hero::after {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(to top, rgba(15, 23, 42, 0.03) 0%, rgba(15, 23, 42, 0.01) 45%, transparent 72%);
                    pointer-events: none;
                }
                .fpqi-story-hero-visual {
                    position: relative;
                    z-index: 1;
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 70px 20px 20px;
                }
                .fpqi-story-hero-image {
                    display: block;
                    width: min(92vw, 520px);
                    max-height: min(68vh, 760px);
                    object-fit: contain;
                    object-position: center center;
                    filter: drop-shadow(0 16px 30px rgba(0, 0, 0, 0.45));
                }
                .fpqi-story-hero-top {
                    position: relative;
                    z-index: 1;
                    max-width: 720px;
                    margin: 0 auto;
                    width: 100%;
                    padding: 18px 20px 0;
                }
                .fpqi-story-hero-top .fpqi-head {
                    margin-bottom: 0;
                    background: #ffffff;
                    border: 1px solid rgba(15, 23, 42, 0.12);
                    border-radius: 14px;
                    padding: 12px 14px;
                    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
                }
                .fpqi-story-hero-top .fpqi-title {
                    color: var(--fpqi-text);
                }
                .fpqi-story-hero-top .fpqi-lang {
                    border-color: var(--fpqi-border);
                    background: #ffffff;
                }
                .fpqi-story-hero-top .fpqi-lang button {
                    color: var(--fpqi-text);
                }
                .fpqi-story-hero-inner {
                    position: relative;
                    z-index: 1;
                    padding: 28px 20px 36px;
                    max-width: 720px;
                    margin: 0 auto;
                    width: 100%;
                    color: var(--fpqi-text);
                }
                .fpqi-story-hero-inner h2 {
                    margin: 0 0 10px;
                    font-size: 1.15rem;
                    font-weight: 700;
                    letter-spacing: 0.04em;
                    text-transform: uppercase;
                    color: var(--fpqi-primary);
                }
                .fpqi-story-hero-inner p {
                    margin: 0;
                    font-size: 1rem;
                    line-height: 1.6;
                    color: var(--fpqi-muted);
                    white-space: pre-wrap;
                }
                .fpqi-story-card {
                    background: linear-gradient(135deg, #faf5ff 0%, #ffffff 55%);
                    border: 1px solid var(--fpqi-border);
                    border-radius: var(--fpqi-radius);
                    padding: 18px 16px;
                    margin-bottom: 18px;
                }
                .fpqi-story-card h2 {
                    margin: 0 0 10px;
                    font-size: 1rem;
                    color: var(--fpqi-primary);
                    text-transform: uppercase;
                    letter-spacing: 0.03em;
                }
                .fpqi-story-card p {
                    margin: 0;
                    color: var(--fpqi-muted);
                    line-height: 1.55;
                    white-space: pre-wrap;
                }
                .fpqi-head {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 20px;
                }
                .fpqi-title { margin: 0; font-size: 1.35rem; }
                .fpqi-main-section-headline {
                    margin: 0 0 16px;
                    font-size: 1.15rem;
                    color: var(--fpqi-primary);
                    line-height: 1.45;
                    letter-spacing: 0.04em;
                    text-transform: uppercase;
                    font-weight: 700;
                }
                .fpqi-lang {
                    display: inline-flex;
                    align-items: center;
                    border: 1px solid var(--fpqi-border);
                    border-radius: 999px;
                    overflow: hidden;
                    background: #fff;
                    padding: 2px;
                    min-height: 42px;
                    flex-shrink: 0;
                }
                .fpqi-lang button {
                    appearance: none;
                    -webkit-appearance: none;
                    border: 0;
                    background: transparent;
                    color: var(--fpqi-text);
                    min-width: 64px;
                    min-height: 36px;
                    padding: 8px 14px;
                    margin: 0;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    font-size: 0.9rem;
                    font-weight: 700;
                    line-height: 1;
                    letter-spacing: 0.02em;
                    border-radius: 999px;
                    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
                }
                .fpqi-lang button[aria-pressed="true"] {
                    background: var(--fpqi-primary);
                    color: #fff;
                    box-shadow: 0 2px 8px rgba(91, 33, 182, 0.25);
                }
                .fpqi-lang button:focus-visible {
                    outline: 2px solid var(--fpqi-primary);
                    outline-offset: 2px;
                }
                .fpqi-card {
                    background: var(--fpqi-surface);
                    border: 1px solid var(--fpqi-border);
                    border-radius: var(--fpqi-radius);
                    padding: 16px;
                    margin-bottom: 14px;
                }
                .fpqi-card h2 {
                    margin: 0 0 10px;
                    font-size: 1rem;
                    color: var(--fpqi-primary);
                    text-transform: uppercase;
                    letter-spacing: 0.03em;
                }
                .fpqi-card p {
                    margin: 0;
                    color: var(--fpqi-muted);
                    line-height: 1.55;
                    white-space: pre-wrap;
                }
                .fpqi-section-body.fpqi-legal-html {
                    color: var(--fpqi-muted);
                    line-height: 1.55;
                    font-size: 0.95rem;
                }
                .fpqi-section-body.fpqi-legal-html p {
                    margin: 0 0 10px;
                    white-space: pre-wrap;
                }
                .fpqi-section-body.fpqi-legal-html p:last-child {
                    margin-bottom: 0;
                }
                .fpqi-section-body.fpqi-legal-html table.fpqi-nutrition-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0 4px;
                    background: #fff;
                }
                .fpqi-section-body.fpqi-legal-html table.fpqi-nutrition-table th,
                .fpqi-section-body.fpqi-legal-html table.fpqi-nutrition-table td {
                    border: 1px solid var(--fpqi-border);
                    padding: 8px 10px;
                    text-align: left;
                    vertical-align: top;
                }
                .fpqi-section-body.fpqi-legal-html table.fpqi-nutrition-table thead th {
                    background: #f3f4f6;
                    color: var(--fpqi-text);
                    font-weight: 700;
                }
                .fpqi-section-body.fpqi-legal-html table.fpqi-nutrition-table tbody th {
                    font-weight: 600;
                    color: var(--fpqi-text);
                    width: 58%;
                }
                .fpqi-preset-icons {
                    display: flex;
                    gap: 14px;
                    align-items: center;
                    margin: 0 0 14px;
                    flex-wrap: wrap;
                }
                .fpqi-recycle-char {
                    font-size: 2.85rem;
                    line-height: 1;
                    color: #166534;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 48px;
                    height: 48px;
                }
                .fpqi-legal-icon {
                    display: block;
                    flex-shrink: 0;
                }
                .fpqi-allergen-warn {
                    font-size: 1.2rem;
                    margin-right: 4px;
                }
                .fpqi-sr-only {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    padding: 0;
                    margin: -1px;
                    overflow: hidden;
                    clip: rect(0, 0, 0, 0);
                    white-space: nowrap;
                    border: 0;
                }
                .fpqi-legal-ref {
                    font-size: 0.82rem;
                    color: var(--fpqi-muted);
                    font-weight: 400;
                }
                .fpqi-legal-note {
                    font-size: 0.82rem;
                    margin: 10px 0 0;
                }
                .fpqi-packaging-grid {
                    display: grid;
                    gap: 12px;
                    grid-template-columns: 1fr;
                    margin-top: 4px;
                }
                @media (min-width: 640px) {
                    .fpqi-packaging-grid {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }
                .fpqi-pack-card {
                    border: 1px solid var(--fpqi-border);
                    border-radius: 12px;
                    padding: 14px 12px;
                    background: #fff;
                    min-height: 100%;
                }
                .fpqi-pack-card.fpqi-pack-cork {
                    border-color: #92400e;
                    box-shadow: 0 0 0 1px rgba(146, 64, 14, 0.25);
                }
                .fpqi-pack-card.fpqi-pack-bottle {
                    border-color: #15803d;
                    box-shadow: 0 0 0 1px rgba(21, 128, 61, 0.25);
                }
                .fpqi-pack-card.fpqi-pack-capsule {
                    border-color: #1d4ed8;
                    box-shadow: 0 0 0 1px rgba(29, 78, 216, 0.3);
                }
                .fpqi-pack-title {
                    margin: 0 0 10px;
                    font-size: 0.95rem;
                    font-weight: 700;
                    color: var(--fpqi-text);
                }
                .fpqi-pack-code {
                    margin-bottom: 10px;
                }
                .fpqi-pack-code-inner {
                    display: inline-block;
                    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
                    font-size: 0.8rem;
                    font-weight: 600;
                    padding: 6px 10px;
                    border-radius: 8px;
                    background: #f1f5f9;
                    border: 1px solid #cbd5e1;
                    color: #0f172a;
                }
                .fpqi-pack-body {
                    font-size: 0.9rem;
                    color: var(--fpqi-muted);
                    line-height: 1.5;
                    white-space: pre-line;
                }
                .fpqi-pack-body p {
                    margin: 0 0 6px;
                }
            </style>
        </head>
        <body>
        <?php if ($storyShowHero): ?>
            <section
                class="fpqi-story-hero"
                aria-label="<?php echo esc_attr($storyTitleIt); ?>"
            >
                <div class="fpqi-story-hero-top">
                    <header class="fpqi-head">
                        <div>
                            <h1 class="fpqi-title"><?php echo esc_html($title); ?></h1>
                        </div>
                        <div class="fpqi-lang" role="group" aria-label="<?php esc_attr_e('Selettore lingua', 'fp-qr-info'); ?>">
                            <button type="button" class="fpqi-lang-btn" data-lang="it" aria-pressed="true">ITA</button>
                            <button type="button" class="fpqi-lang-btn" data-lang="en" aria-pressed="false">ENG</button>
                        </div>
                    </header>
                </div>
                <div class="fpqi-story-hero-visual">
                    <img class="fpqi-story-hero-image" src="<?php echo esc_url($storyImageUrl); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>
                <div class="fpqi-story-hero-inner">
                    <h2 id="fpqi-story-title"><?php echo esc_html($storyTitleIt); ?></h2>
                    <p id="fpqi-story-body"><?php echo esc_html($storyIt); ?></p>
                </div>
            </section>
        <?php endif; ?>
        <div class="fpqi-wrap">
            <?php if ($storyShowBlock): ?>
                <section class="fpqi-story-card" aria-labelledby="fpqi-story-title">
                    <h2 id="fpqi-story-title"><?php echo esc_html($storyTitleIt); ?></h2>
                    <p id="fpqi-story-body"><?php echo esc_html($storyIt); ?></p>
                </section>
            <?php endif; ?>
            <h2 class="fpqi-main-section-headline" id="fpqi-main-section-title"><?php echo esc_html($sectionHeadline['it']); ?></h2>
            <?php if (!$storyShowHero): ?>
                <header class="fpqi-head">
                    <div>
                        <h1 class="fpqi-title"><?php echo esc_html($title); ?></h1>
                    </div>
                    <div class="fpqi-lang" role="group" aria-label="<?php esc_attr_e('Selettore lingua', 'fp-qr-info'); ?>">
                        <button type="button" class="fpqi-lang-btn" data-lang="it" aria-pressed="true">ITA</button>
                        <button type="button" class="fpqi-lang-btn" data-lang="en" aria-pressed="false">ENG</button>
                    </div>
                </header>
            <?php endif; ?>
            <?php foreach ($i18nPayload['sections'] as $idx => $sec): ?>
                <section class="fpqi-card" data-section-id="<?php echo esc_attr((string) $sec['id']); ?>">
                    <h2 class="fpqi-section-title"><?php echo esc_html((string) $sec['label']['it']); ?></h2>
                    <div class="fpqi-section-body fpqi-legal-html"><?php echo $sec['body']['it']; ?></div>
                </section>
            <?php endforeach; ?>
        </div>
        <script type="application/json" id="fpqi-landing-i18n"><?php echo $i18nJson; ?></script>
        <script>
            (function () {
                var raw = document.getElementById('fpqi-landing-i18n');
                var data = {};
                try {
                    data = JSON.parse(raw ? raw.textContent : '{}');
                } catch (e) {
                    data = {};
                }
                var currentLang = 'it';
                var buttons = document.querySelectorAll('.fpqi-lang-btn');
                function applyLang(lang) {
                    currentLang = lang;
                    buttons.forEach(function (btn) {
                        btn.setAttribute('aria-pressed', String(btn.getAttribute('data-lang') === lang));
                    });
                    var sections = data.sections || [];
                    sections.forEach(function (sec) {
                        var wrap = document.querySelector('[data-section-id="' + sec.id + '"]');
                        if (!wrap) {
                            return;
                        }
                        var titleEl = wrap.querySelector('.fpqi-section-title');
                        var bodyEl = wrap.querySelector('.fpqi-section-body');
                        if (titleEl && sec.label) {
                            titleEl.textContent = sec.label[lang] || '';
                        }
                        if (bodyEl && sec.body) {
                            bodyEl.innerHTML = sec.body[lang] || '';
                        }
                    });
                    var story = data.story || {};
                    var stTitle = document.getElementById('fpqi-story-title');
                    var stBody = document.getElementById('fpqi-story-body');
                    var sectionTitle = document.getElementById('fpqi-main-section-title');
                    if (stTitle && story.title) {
                        stTitle.textContent = story.title[lang] || '';
                    }
                    if (stBody && story.body) {
                        stBody.textContent = story.body[lang] || '';
                    }
                    if (sectionTitle && data.sectionHeadline) {
                        sectionTitle.textContent = data.sectionHeadline[lang] || '';
                    }
                }
                buttons.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        applyLang(btn.getAttribute('data-lang') || 'it');
                    });
                });
                applyLang(currentLang);
            })();
        </script>
        </body>
        </html>
        <?php
    }

    /**
     * Espande segnaposto icone e applica filtro HTML sicuro per i blocchi legali.
     */
    private function prepareSectionBody(string $raw): string
    {
        $expanded = LandingLegalPresets::expandPlaceholder($raw);

        return wp_kses_post($expanded);
    }

    /**
     * Verifica se è compilato almeno un campo dei blocchi smaltimento (Tappo, Bottiglia, Capsula).
     */
    private function disposalBlocksInUse(int $postId): bool
    {
        foreach (LandingLegalPresets::DISPOSAL_BLOCK_SLUGS as $slug) {
            $code = trim((string) get_post_meta($postId, 'fp_qr_info_disposal_block_' . $slug . '_code', true));
            $it = trim((string) get_post_meta($postId, 'fp_qr_info_disposal_block_' . $slug . '_it', true));
            $en = trim((string) get_post_meta($postId, 'fp_qr_info_disposal_block_' . $slug . '_en', true));
            if ($code !== '' || $it !== '' || $en !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Costruisce la griglia HTML dei blocchi imballaggio per la lingua richiesta.
     */
    private function buildDisposalBlocksHtml(int $postId, string $lang): string
    {
        $lang = $lang === 'en' ? 'en' : 'it';
        $parts = [];
        foreach (LandingLegalPresets::getDisposalBlockDefinitions() as $def) {
            $slug = (string) $def['slug'];
            $code = trim((string) get_post_meta($postId, 'fp_qr_info_disposal_block_' . $slug . '_code', true));
            $bodyRaw = (string) get_post_meta($postId, 'fp_qr_info_disposal_block_' . $slug . '_' . $lang, true);
            $bodyTrim = trim($bodyRaw);
            if ($code === '' && $bodyTrim === '') {
                continue;
            }
            $title = $lang === 'en' ? (string) $def['title_en'] : (string) $def['title_it'];
            $cardClass = 'fpqi-pack-card fpqi-pack-' . preg_replace('/[^a-z0-9_-]/', '', $slug);
            if ($slug === 'capsule') {
                $cardClass .= ' fpqi-pack-capsule';
            }
            $codeHtml = $code !== ''
                ? '<div class="fpqi-pack-code"><span class="fpqi-pack-code-inner">' . esc_html($code) . '</span></div>'
                : '';
            $bodyHtml = $bodyTrim !== ''
                ? '<div class="fpqi-pack-body">' . wp_kses_post(LandingLegalPresets::expandPlaceholder($bodyRaw)) . '</div>'
                : '';
            $parts[] = '<article class="' . esc_attr($cardClass) . '" role="listitem">'
                . '<h3 class="fpqi-pack-title">' . esc_html($title) . '</h3>'
                . $codeHtml
                . $bodyHtml
                . '</article>';
        }

        if ($parts === []) {
            return '';
        }

        return '<div class="fpqi-packaging-grid" role="list">' . implode('', $parts) . '</div>';
    }

    /**
     * Restituisce il colore accent valido della landing.
     */
    private function resolveAccentColor(int $postId): string
    {
        $raw = (string) get_post_meta($postId, 'fp_qr_info_accent_color', true);
        $sanitized = sanitize_hex_color($raw);

        return is_string($sanitized) && $sanitized !== '' ? $sanitized : '#5b21b6';
    }

    /**
     * Aggiorna etichette legacy "Esempio" in formulazione standard vino.
     *
     * @param string $html Blocco ingredienti salvato.
     * @param string $lang Lingua corrente (it|en).
     * @return string HTML normalizzato.
     */
    private function normalizeLegacyIngredientsCopy(string $html, string $lang): string
    {
        if ($html === '') {
            return $html;
        }

        if ($lang === 'en') {
            return str_replace(
                [
                    'Example (wine — adapt to the actual product)',
                    'The “contains sulphites” indication may be required on the physical label under wine-sector rules even when the full list is provided electronically: confirm mandatory on-pack wording with your sector advisor.',
                    'Where additives, technological aids or other ingredients are used, list them with the legally required designation (including E numbers where applicable) and in the order required by Regulation (EU) No 1169/2011.',
                ],
                [
                    'Wine — ingredients declaration',
                    '',
                    '',
                ],
                $html
            );
        }

        return str_replace(
            [
                'Esempio (vino — da adattare al prodotto reale)',
                'L’indicazione «contiene solfiti» può essere richiesta sull’etichetta fisica ai sensi della normativa sui vini anche quando l’elenco completo è fornito per via elettronica: verificare il testo obbligatorio sul recipiente con il consulente di settore.',
                'Ove siano utilizzati additivi, coadiuvanti tecnologici o altri ingredienti, inserirli con la denominazione legalmente prevista (inclusi i numeri E ove applicabili) e nell’ordine previsto dal regolamento (UE) n. 1169/2011.',
            ],
            [
                'Vino — dichiarazione ingredienti',
                '',
                '',
            ],
            $html
        );
    }

    /**
     * Normalizza testi nutrizionali legacy rimuovendo i segnaposto e inserendo valori vino.
     *
     * @param string $html Blocco nutrizionale salvato.
     * @param string $lang Lingua corrente (it|en).
     * @return string HTML normalizzato.
     */
    private function normalizeLegacyNutritionCopy(string $html, string $lang): string
    {
        if ($html === '') {
            return $html;
        }

        $replaced = str_replace(
            [
                '… kJ / … kcal',
                '… g',
                'Sostituire i segni «…» con i valori analitici del prodotto. Il valore energetico deve essere espresso in chilojoule (kJ) e in chilocalorie (kcal), con il kJ indicato per primo (art. 33, paragrafo 5, e Allegato XV). Le quantità di nutrienti si esprimono in grammi (g) per 100 ml (art. 32).',
                'Replace the ellipses with product analytical values. Energy value must be given in kilojoules (kJ) and kilocalories (kcal), with kJ first (Article 33(5) and Annex XV). Amounts of nutrients are expressed in grams (g) per 100 ml (Article 32).',
                'Valori medi per vino per 100 ml: energia 330 kJ / 79 kcal; grassi 0 g (di cui saturi 0 g); carboidrati 2,6 g (di cui zuccheri 0,6 g); proteine 0 g; sale 0,01 g.',
                'Average wine values per 100 ml: energy 330 kJ / 79 kcal; fat 0 g (of which saturates 0 g); carbohydrate 2.6 g (of which sugars 0.6 g); protein 0 g; salt 0.01 g.',
            ],
            [
                '330 kJ / 79 kcal',
                '0 g',
                '',
                '',
                '',
                '',
            ],
            $html
        );

        if ($lang === 'en') {
            return str_replace(
                [
                    '<tr><th scope="row">Carbohydrate</th><td>0 g</td></tr>',
                    '<tr><th scope="row">of which sugars</th><td>0 g</td></tr>',
                    '<tr><th scope="row">Salt</th><td>0 g</td></tr>',
                ],
                [
                    '<tr><th scope="row">Carbohydrate</th><td>2.6 g</td></tr>',
                    '<tr><th scope="row">of which sugars</th><td>0.6 g</td></tr>',
                    '<tr><th scope="row">Salt</th><td>0.01 g</td></tr>',
                ],
                $replaced
            );
        }

        return str_replace(
            [
                '<tr><th scope="row">Carboidrati</th><td>0 g</td></tr>',
                '<tr><th scope="row">di cui zuccheri</th><td>0 g</td></tr>',
                '<tr><th scope="row">Sale</th><td>0 g</td></tr>',
            ],
            [
                '<tr><th scope="row">Carboidrati</th><td>2,6 g</td></tr>',
                '<tr><th scope="row">di cui zuccheri</th><td>0,6 g</td></tr>',
                '<tr><th scope="row">Sale</th><td>0,01 g</td></tr>',
            ],
            $replaced
        );
    }
}

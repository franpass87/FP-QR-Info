<?php

declare(strict_types=1);

namespace FP\QrInfo\Frontend;

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
        $intro = __('INFORMAZIONI DI SMALTIMENTO NUTRIZIONALI E INGREDIENTI - DISPOSAL AND NUTRITIONAL INFO, INGREDIENTS', 'fp-qr-info');
        $sections = [
            'disposal' => [
                'it' => (string) get_post_meta($post->ID, 'fp_qr_info_disposal_it', true),
                'en' => (string) get_post_meta($post->ID, 'fp_qr_info_disposal_en', true),
                'label_it' => __('INFORMAZIONI DI SMALTIMENTO', 'fp-qr-info'),
                'label_en' => __('DISPOSAL INFO', 'fp-qr-info'),
            ],
            'nutrition' => [
                'it' => (string) get_post_meta($post->ID, 'fp_qr_info_nutrition_it', true),
                'en' => (string) get_post_meta($post->ID, 'fp_qr_info_nutrition_en', true),
                'label_it' => __('INFORMAZIONI NUTRIZIONALI', 'fp-qr-info'),
                'label_en' => __('NUTRITIONAL INFO', 'fp-qr-info'),
            ],
            'ingredients' => [
                'it' => (string) get_post_meta($post->ID, 'fp_qr_info_ingredients_it', true),
                'en' => (string) get_post_meta($post->ID, 'fp_qr_info_ingredients_en', true),
                'label_it' => __('INGREDIENTI', 'fp-qr-info'),
                'label_en' => __('INGREDIENTS', 'fp-qr-info'),
            ],
        ];
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
                    --fpqi-primary: #5b21b6;
                    --fpqi-border: #e5e7eb;
                    --fpqi-radius: 14px;
                }
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                    background: var(--fpqi-bg);
                    color: var(--fpqi-text);
                    padding: 24px 16px 40px;
                }
                .fpqi-wrap { max-width: 720px; margin: 0 auto; }
                .fpqi-head {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 20px;
                }
                .fpqi-title { margin: 0; font-size: 1.35rem; }
                .fpqi-intro {
                    margin: 8px 0 0;
                    font-size: 0.9rem;
                    color: var(--fpqi-muted);
                    line-height: 1.45;
                }
                .fpqi-lang {
                    border: 1px solid var(--fpqi-border);
                    border-radius: 999px;
                    overflow: hidden;
                    background: #fff;
                }
                .fpqi-lang button {
                    border: 0;
                    background: transparent;
                    padding: 8px 12px;
                    cursor: pointer;
                    font-weight: 600;
                }
                .fpqi-lang button[aria-pressed="true"] {
                    background: var(--fpqi-primary);
                    color: #fff;
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
            </style>
        </head>
        <body>
        <div class="fpqi-wrap">
            <header class="fpqi-head">
                <div>
                    <h1 class="fpqi-title"><?php echo esc_html($title); ?></h1>
                    <p class="fpqi-intro"><?php echo esc_html($intro); ?></p>
                </div>
                <div class="fpqi-lang" role="group" aria-label="<?php esc_attr_e('Selettore lingua', 'fp-qr-info'); ?>">
                    <button type="button" data-lang="it" aria-pressed="true">ITA</button>
                    <button type="button" data-lang="en" aria-pressed="false">ENG</button>
                </div>
            </header>
            <?php foreach ($sections as $section): ?>
                <section class="fpqi-card">
                    <h2 data-lang-it="<?php echo esc_attr($section['label_it']); ?>" data-lang-en="<?php echo esc_attr($section['label_en']); ?>">
                        <?php echo esc_html($section['label_it']); ?>
                    </h2>
                    <p data-lang-it="<?php echo esc_attr($section['it']); ?>" data-lang-en="<?php echo esc_attr($section['en']); ?>">
                        <?php echo esc_html($section['it']); ?>
                    </p>
                </section>
            <?php endforeach; ?>
        </div>
        <script>
            (function () {
                var currentLang = 'it';
                var buttons = document.querySelectorAll('[data-lang]');
                function applyLang(lang) {
                    currentLang = lang;
                    buttons.forEach(function (btn) {
                        btn.setAttribute('aria-pressed', String(btn.getAttribute('data-lang') === lang));
                    });
                    document.querySelectorAll('[data-lang-it]').forEach(function (el) {
                        var value = el.getAttribute('data-lang-' + lang);
                        if (typeof value === 'string') {
                            el.textContent = value;
                        }
                    });
                }
                buttons.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        applyLang(btn.getAttribute('data-lang'));
                    });
                });
                applyLang(currentLang);
            })();
        </script>
        </body>
        </html>
        <?php
    }
}

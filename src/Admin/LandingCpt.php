<?php

declare(strict_types=1);

namespace FP\QrInfo\Admin;

/**
 * Gestione CPT e metadati per landing QR.
 */
final class LandingCpt
{
    private const POST_TYPE = 'fp_qr_landing';
    private const NONCE_ACTION = 'fp_qr_info_save_meta';
    private const NONCE_NAME = 'fp_qr_info_nonce';
    private const META_TOKEN = 'fp_qr_info_token';

    /**
     * Registra hook admin del CPT.
     */
    public function register(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('add_meta_boxes', [$this, 'registerMetaBox']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'saveMeta']);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'addColumns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'renderColumn'], 10, 2);
    }

    /**
     * Registra il post type delle landing QR.
     */
    public function registerPostType(): void
    {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name' => esc_html__('QR Landing', 'fp-qr-info'),
                    'singular_name' => esc_html__('QR Landing', 'fp-qr-info'),
                    'add_new_item' => esc_html__('Aggiungi nuova QR Landing', 'fp-qr-info'),
                    'edit_item' => esc_html__('Modifica QR Landing', 'fp-qr-info'),
                    'menu_name' => esc_html__('FP QR Info', 'fp-qr-info'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => AdminMenu::MENU_SLUG,
                'show_in_rest' => false,
                'menu_icon' => 'dashicons-media-code',
                'supports' => ['title'],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'rewrite' => false,
            ]
        );
    }

    /**
     * Registra metabox principale per i contenuti IT/EN.
     */
    public function registerMetaBox(): void
    {
        add_meta_box(
            'fp_qr_info_meta',
            esc_html__('Contenuti Landing QR', 'fp-qr-info'),
            [$this, 'renderMetaBox'],
            self::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'fp_qr_info_actions',
            esc_html__('Azioni rapide', 'fp-qr-info'),
            [$this, 'renderActionsMetaBox'],
            self::POST_TYPE,
            'side',
            'high'
        );
    }

    /**
     * Render metabox con token e contenuti bilingua.
     *
     * @param \WP_Post $post Post corrente.
     */
    public function renderMetaBox(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $token = (string) get_post_meta($post->ID, self::META_TOKEN, true);
        if ($token === '') {
            $token = $this->generateUniqueToken();
        }

        $fields = $this->getFields();
        ?>
        <p>
            <label for="fp_qr_info_token"><strong><?php esc_html_e('Token URL', 'fp-qr-info'); ?></strong></label><br>
            <input type="text" id="fp_qr_info_token" name="fp_qr_info_token" class="regular-text" value="<?php echo esc_attr($token); ?>">
            <span class="description"><?php esc_html_e('Usato nella route /qr-info/{token}', 'fp-qr-info'); ?></span>
        </p>
        <p>
            <button type="button" class="button" id="fp-qri-regenerate-token"><?php esc_html_e('Rigenera token', 'fp-qr-info'); ?></button>
        </p>
        <?php
        foreach ($fields as $fieldKey => $label) {
            $itValue = (string) get_post_meta($post->ID, 'fp_qr_info_' . $fieldKey . '_it', true);
            $enValue = (string) get_post_meta($post->ID, 'fp_qr_info_' . $fieldKey . '_en', true);
            ?>
            <p>
                <strong><?php echo esc_html($label); ?></strong>
            </p>
            <p>
                <label for="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it"><?php esc_html_e('Italiano', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it" name="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it" rows="4" class="large-text"><?php echo esc_textarea($itValue); ?></textarea>
            </p>
            <p>
                <label for="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en"><?php esc_html_e('English', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en" name="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en" rows="4" class="large-text"><?php echo esc_textarea($enValue); ?></textarea>
            </p>
            <hr>
            <?php
        }
        ?>
        <script>
            (function () {
                var button = document.getElementById('fp-qri-regenerate-token');
                var input = document.getElementById('fp_qr_info_token');
                if (!button || !input) {
                    return;
                }
                button.addEventListener('click', function () {
                    var random = Math.random().toString(36).slice(2, 10) + Math.random().toString(36).slice(2, 10);
                    input.value = random.toLowerCase();
                });
            })();
        </script>
        <?php
    }

    /**
     * Render metabox laterale con URL e QR scaricabili.
     *
     * @param \WP_Post $post Post corrente.
     */
    public function renderActionsMetaBox(\WP_Post $post): void
    {
        $token = (string) get_post_meta($post->ID, self::META_TOKEN, true);
        if ($token === '') {
            echo '<p>' . esc_html__('Salva la landing per generare URL e QR code.', 'fp-qr-info') . '</p>';
            return;
        }

        $landingUrl = home_url('/qr-info/' . rawurlencode($token));
        $pngUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_download&format=png&post_id=' . $post->ID),
            'fp_qr_info_download_' . $post->ID
        );
        $svgUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_download&format=svg&post_id=' . $post->ID),
            'fp_qr_info_download_' . $post->ID
        );
        $previewUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_download&format=png&inline=1&post_id=' . $post->ID),
            'fp_qr_info_download_' . $post->ID
        );
        $printUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_print_label&post_id=' . $post->ID),
            'fp_qr_info_download_' . $post->ID
        );
        ?>
        <p>
            <strong><?php esc_html_e('URL pubblica', 'fp-qr-info'); ?></strong><br>
            <a href="<?php echo esc_url($landingUrl); ?>" target="_blank" rel="noopener"><?php esc_html_e('Apri landing', 'fp-qr-info'); ?></a>
        </p>
        <p>
            <input type="text" id="fp-qri-landing-url" class="widefat" readonly value="<?php echo esc_attr($landingUrl); ?>">
            <button type="button" class="button" id="fp-qri-copy-url"><?php esc_html_e('Copia URL', 'fp-qr-info'); ?></button>
            <span id="fp-qri-copy-feedback" style="display:none;margin-left:6px;"><?php esc_html_e('Copiato', 'fp-qr-info'); ?></span>
        </p>
        <p>
            <strong><?php esc_html_e('QR code', 'fp-qr-info'); ?></strong><br>
            <a class="button button-secondary" href="<?php echo esc_url($pngUrl); ?>"><?php esc_html_e('Scarica PNG', 'fp-qr-info'); ?></a>
            <a class="button button-secondary" href="<?php echo esc_url($svgUrl); ?>"><?php esc_html_e('Scarica SVG', 'fp-qr-info'); ?></a>
            <a class="button button-secondary" href="<?php echo esc_url($printUrl); ?>" target="_blank" rel="noopener"><?php esc_html_e('Stampa etichetta', 'fp-qr-info'); ?></a>
        </p>
        <p>
            <img src="<?php echo esc_url($previewUrl); ?>" alt="<?php esc_attr_e('Anteprima QR', 'fp-qr-info'); ?>" style="max-width:100%;height:auto;border:1px solid #dcdcde;padding:6px;background:#fff;">
        </p>
        <script>
            (function () {
                var button = document.getElementById('fp-qri-copy-url');
                var input = document.getElementById('fp-qri-landing-url');
                var feedback = document.getElementById('fp-qri-copy-feedback');
                if (!button || !input) {
                    return;
                }
                button.addEventListener('click', function () {
                    input.select();
                    input.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    if (feedback) {
                        feedback.style.display = 'inline';
                        setTimeout(function () {
                            feedback.style.display = 'none';
                        }, 1200);
                    }
                });
            })();
        </script>
        <?php
    }

    /**
     * Salva i metadati della landing.
     *
     * @param int $postId ID post da salvare.
     */
    public function saveMeta(int $postId): void
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce((string) $_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $token = isset($_POST['fp_qr_info_token']) ? sanitize_key(wp_unslash((string) $_POST['fp_qr_info_token'])) : '';
        if ($token === '') {
            $token = $this->generateUniqueToken();
        }
        $token = $this->ensureUniqueToken($token, $postId);
        update_post_meta($postId, self::META_TOKEN, $token);

        foreach (array_keys($this->getFields()) as $fieldKey) {
            $itValue = isset($_POST['fp_qr_info_' . $fieldKey . '_it'])
                ? sanitize_textarea_field(wp_unslash((string) $_POST['fp_qr_info_' . $fieldKey . '_it']))
                : '';
            $enValue = isset($_POST['fp_qr_info_' . $fieldKey . '_en'])
                ? sanitize_textarea_field(wp_unslash((string) $_POST['fp_qr_info_' . $fieldKey . '_en']))
                : '';

            update_post_meta($postId, 'fp_qr_info_' . $fieldKey . '_it', $itValue);
            update_post_meta($postId, 'fp_qr_info_' . $fieldKey . '_en', $enValue);
        }
    }

    /**
     * Aggiunge colonne utili nella lista CPT.
     *
     * @param array<string, string> $columns Colonne esistenti.
     * @return array<string, string>
     */
    public function addColumns(array $columns): array
    {
        $columns['fp_qr_info_token'] = esc_html__('Token', 'fp-qr-info');
        $columns['fp_qr_info_url'] = esc_html__('URL pubblica', 'fp-qr-info');
        return $columns;
    }

    /**
     * Render colonne custom nella lista.
     *
     * @param string $column Nome colonna.
     * @param int $postId ID post corrente.
     */
    public function renderColumn(string $column, int $postId): void
    {
        if ($column === 'fp_qr_info_token') {
            echo esc_html((string) get_post_meta($postId, self::META_TOKEN, true));
            return;
        }

        if ($column !== 'fp_qr_info_url') {
            return;
        }

        $token = (string) get_post_meta($postId, self::META_TOKEN, true);
        if ($token === '') {
            echo '&#8212;';
            return;
        }

        $url = home_url('/qr-info/' . rawurlencode($token));
        $pngUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_download&format=png&post_id=' . $postId),
            'fp_qr_info_download_' . $postId
        );
        $svgUrl = wp_nonce_url(
            admin_url('admin-post.php?action=fp_qr_info_download&format=svg&post_id=' . $postId),
            'fp_qr_info_download_' . $postId
        );

        printf(
            '<a href="%1$s" target="_blank" rel="noopener">%2$s</a><br><a href="%3$s">%4$s</a> | <a href="%5$s">%6$s</a>',
            esc_url($url),
            esc_html__('Apri', 'fp-qr-info'),
            esc_url($pngUrl),
            esc_html__('QR PNG', 'fp-qr-info'),
            esc_url($svgUrl),
            esc_html__('QR SVG', 'fp-qr-info')
        );
    }

    /**
     * Campi contenuto previsti in landing.
     *
     * @return array<string, string>
     */
    private function getFields(): array
    {
        return [
            'disposal' => __('Informazioni di smaltimento / Disposal info', 'fp-qr-info'),
            'nutrition' => __('Informazioni nutrizionali / Nutritional info', 'fp-qr-info'),
            'ingredients' => __('Ingredienti / Ingredients', 'fp-qr-info'),
        ];
    }

    /**
     * Genera un token casuale e univoco.
     */
    private function generateUniqueToken(): string
    {
        $token = wp_generate_password(16, false, false);
        return $this->ensureUniqueToken($token);
    }

    /**
     * Garantisce che il token non sia gia usato da altre landing.
     *
     * @param string $candidate Token richiesto.
     * @param int $excludePostId ID post da escludere (update).
     */
    private function ensureUniqueToken(string $candidate, int $excludePostId = 0): string
    {
        $candidate = sanitize_key($candidate);
        if ($candidate === '') {
            $candidate = wp_generate_password(16, false, false);
        }

        $token = $candidate;
        $tries = 0;

        while ($tries < 20) {
            if (!$this->tokenExists($token, $excludePostId)) {
                return $token;
            }
            $token = $candidate . '-' . wp_rand(10, 99);
            $tries++;
        }

        return wp_generate_password(20, false, false);
    }

    /**
     * Verifica esistenza token su altri post.
     *
     * @param string $token Token da verificare.
     * @param int $excludePostId ID da ignorare.
     */
    private function tokenExists(string $token, int $excludePostId = 0): bool
    {
        $query = new \WP_Query(
            [
                'post_type' => self::POST_TYPE,
                'post_status' => ['publish', 'draft', 'pending', 'private'],
                'posts_per_page' => 1,
                'post__not_in' => $excludePostId > 0 ? [$excludePostId] : [],
                'meta_key' => self::META_TOKEN,
                'meta_value' => $token,
                'fields' => 'ids',
                'no_found_rows' => true,
            ]
        );

        $exists = $query->have_posts();
        wp_reset_postdata();
        return $exists;
    }
}

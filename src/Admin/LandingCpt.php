<?php

declare(strict_types=1);

namespace FP\QrInfo\Admin;

use FP\QrInfo\Content\LandingLegalPresets;

/**
 * Gestione CPT e metadati per landing QR.
 */
final class LandingCpt
{
    private const POST_TYPE = 'fp_qr_landing';
    private const NONCE_ACTION = 'fp_qr_info_save_meta';
    private const NONCE_NAME = 'fp_qr_info_nonce';
    private const META_TOKEN = 'fp_qr_info_token';
    private const META_ACCENT_COLOR = 'fp_qr_info_accent_color';

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
        add_action('admin_enqueue_scripts', [$this, 'enqueueStoryAssets']);
        add_action('edit_form_after_title', [$this, 'renderEditorHeader']);
        add_action('all_admin_notices', [$this, 'renderListHeader']);
    }

    /**
     * Renderizza un header in stile FP nella schermata editor del CPT.
     *
     * @param \WP_Post $post Post corrente in modifica.
     */
    public function renderEditorHeader(\WP_Post $post): void
    {
        if ($post->post_type !== self::POST_TYPE) {
            return;
        }

        ?>
        <div class="fpqri-editor-header" role="region" aria-label="<?php echo esc_attr__('FP QR Info editor', 'fp-qr-info'); ?>">
            <div class="fpqri-editor-header-content">
                <h2 class="fpqri-editor-header-title">
                    <span class="dashicons dashicons-media-code" aria-hidden="true"></span>
                    <?php esc_html_e('FP QR Info', 'fp-qr-info'); ?>
                </h2>
                <p class="fpqri-editor-header-desc">
                    <?php esc_html_e('Compila i contenuti IT/EN e genera una landing QR pronta per etichetta e stampa.', 'fp-qr-info'); ?>
                </p>
            </div>
            <span class="fpqri-editor-header-badge">v<?php echo esc_html(FP_QR_INFO_VERSION); ?></span>
        </div>
        <?php
    }

    /**
     * Renderizza un header FP nella schermata lista del CPT.
     */
    public function renderListHeader(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen instanceof \WP_Screen || $screen->id !== 'edit-' . self::POST_TYPE) {
            return;
        }
        ?>
        <div class="fpqri-list-header" role="region" aria-label="<?php echo esc_attr__('FP QR Info lista landing', 'fp-qr-info'); ?>">
            <div class="fpqri-list-header-content">
                <h2 class="fpqri-list-header-title">
                    <span class="dashicons dashicons-media-code" aria-hidden="true"></span>
                    <?php esc_html_e('FP QR Info', 'fp-qr-info'); ?>
                </h2>
                <p class="fpqri-list-header-desc">
                    <?php esc_html_e('Gestisci token, URL pubbliche e download rapidi QR direttamente dalla lista.', 'fp-qr-info'); ?>
                </p>
            </div>
            <span class="fpqri-list-header-badge">v<?php echo esc_html(FP_QR_INFO_VERSION); ?></span>
        </div>
        <?php
    }

    /**
     * Enqueue media picker per immagine storia (solo schermata CPT).
     *
     * @param string $hook Suffisso hook schermata corrente.
     */
    public function enqueueStoryAssets(string $hook): void
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen instanceof \WP_Screen || $screen->post_type !== self::POST_TYPE) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'fp-qr-info-admin-story',
            FP_QR_INFO_URL . 'assets/js/admin-story-media.js',
            ['jquery'],
            FP_QR_INFO_VERSION,
            true
        );
        wp_localize_script(
            'fp-qr-info-admin-story',
            'FP_QRI_STORY_MEDIA',
            [
                'title' => esc_html__('Seleziona immagine bottiglia', 'fp-qr-info'),
                'button' => esc_html__('Usa questa immagine', 'fp-qr-info'),
            ]
        );

        wp_enqueue_script(
            'fp-qr-info-admin-legal-presets',
            FP_QR_INFO_URL . 'assets/js/admin-legal-presets.js',
            ['jquery'],
            FP_QR_INFO_VERSION,
            true
        );
        wp_localize_script(
            'fp-qr-info-admin-legal-presets',
            'FP_QRI_LANDING_PRESETS_CFG',
            [
                'presets' => LandingLegalPresets::getPresets(),
                'disposalBlockExamples' => LandingLegalPresets::getDisposalBlockExamples(),
                'disposalBlockSlugs' => LandingLegalPresets::DISPOSAL_BLOCK_SLUGS,
                'confirmOverwrite' => __(
                    'Questa sezione contiene già del testo. Sostituirlo con il modello normativo selezionato?',
                    'fp-qr-info'
                ),
                'confirmDisposalBlocks' => __(
                    'I campi dei blocchi smaltimento contengono già dati. Sostituirli con l’esempio?',
                    'fp-qr-info'
                ),
            ]
        );
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
        <?php
        $accentColor = (string) get_post_meta($post->ID, self::META_ACCENT_COLOR, true);
        $accentColor = sanitize_hex_color($accentColor) ?: '#5b21b6';
        ?>
        <p>
            <label for="fp_qr_info_accent_color"><strong><?php esc_html_e('Colore accent landing', 'fp-qr-info'); ?></strong></label><br>
            <input type="color" id="fp_qr_info_accent_color" name="fp_qr_info_accent_color" value="<?php echo esc_attr($accentColor); ?>">
            <span class="description"><?php esc_html_e('Usato per switch lingua, titoli sezione e accenti grafici frontend.', 'fp-qr-info'); ?></span>
        </p>
        <p>
            <button type="button" class="button" id="fp-qri-regenerate-token"><?php esc_html_e('Rigenera token', 'fp-qr-info'); ?></button>
        </p>
        <hr>
        <h3><?php esc_html_e('Storia ed etichetta', 'fp-qr-info'); ?></h3>
        <p class="description"><?php esc_html_e('Immagine bottiglia scontornata (consigliato PNG trasparente) e testo narrativo sul vino/etichetta. La landing mostra la bottiglia centrata senza taglio 16:9.', 'fp-qr-info'); ?></p>
        <?php
        $storyImageId = (int) get_post_meta($post->ID, 'fp_qr_info_story_image_id', true);
        $storyIt = (string) get_post_meta($post->ID, 'fp_qr_info_story_it', true);
        $storyEn = (string) get_post_meta($post->ID, 'fp_qr_info_story_en', true);
        $storyPreviewUrl = '';
        if ($storyImageId > 0) {
            $storyPreviewUrl = (string) wp_get_attachment_image_url($storyImageId, 'medium');
        }
        ?>
        <p>
            <label for="fp_qr_info_story_image_id"><strong><?php esc_html_e('Immagine bottiglia', 'fp-qr-info'); ?></strong></label><br>
            <input type="hidden" id="fp_qr_info_story_image_id" name="fp_qr_info_story_image_id" value="<?php echo esc_attr((string) $storyImageId); ?>">
            <button type="button" class="button" id="fp-qri-story-select-image"><?php esc_html_e('Seleziona dalla libreria', 'fp-qr-info'); ?></button>
            <button type="button" class="button" id="fp-qri-story-remove-image" style="<?php echo $storyImageId > 0 ? '' : 'display:none;'; ?>"><?php esc_html_e('Rimuovi immagine', 'fp-qr-info'); ?></button>
        </p>
        <div id="fp-qri-story-image-preview" style="max-width:320px;margin-bottom:12px;">
            <?php if ($storyPreviewUrl !== ''): ?>
                <img src="<?php echo esc_url($storyPreviewUrl); ?>" alt="" style="max-width:100%;height:auto;border-radius:8px;border:1px solid #dcdcde;">
            <?php endif; ?>
        </div>
        <p>
            <label for="fp_qr_info_story_it"><?php esc_html_e('Storia (Italiano)', 'fp-qr-info'); ?></label><br>
            <textarea id="fp_qr_info_story_it" name="fp_qr_info_story_it" rows="6" class="large-text"><?php echo esc_textarea($storyIt); ?></textarea>
        </p>
        <p>
            <label for="fp_qr_info_story_en"><?php esc_html_e('Story (English)', 'fp-qr-info'); ?></label><br>
            <textarea id="fp_qr_info_story_en" name="fp_qr_info_story_en" rows="6" class="large-text"><?php echo esc_textarea($storyEn); ?></textarea>
        </p>
        <hr>
        <p class="description" style="max-width:860px;">
            <?php esc_html_e(
                'Smaltimento: usa i tre blocchi (Tappo, Bottiglia, Capsula) con codice materiale e testi IT/EN; se restano vuoti, vale il blocco HTML unico sotto. Per nutrizionali e ingredienti puoi usare HTML sicuro; i pulsanti “Inserisci modello” aggiungono testi di partenza.',
                'fp-qr-info'
            ); ?>
        </p>
        <p class="description" style="max-width:860px;">
            <strong><?php esc_html_e('Avvertenza legale', 'fp-qr-info'); ?>:</strong>
            <?php esc_html_e(
                'I modelli sono solo ausili editoriali ispirati a prassi UE (es. Reg. 1169/2011, imballaggi). La conformità definitiva è responsabilità del produttore e va verificata con un consulente legale.',
                'fp-qr-info'
            ); ?>
        </p>
        <h3><?php esc_html_e('Etichetta ambientale — smaltimento imballaggi', 'fp-qr-info'); ?></h3>
        <p class="description"><?php esc_html_e('Codici es. FOR 51, GL 70, C/PVC 90 secondo la Decisione 97/129/CE (verificare sul vostro imballaggio reale).', 'fp-qr-info'); ?></p>
        <p>
            <button type="button" class="button button-secondary fp-qri-insert-preset" data-preset="disposal-blocks">
                <?php esc_html_e('Inserisci esempio: blocchi Tappo / Bottiglia / Capsula', 'fp-qr-info'); ?>
            </button>
        </p>
        <?php
        foreach (LandingLegalPresets::getDisposalBlockDefinitions() as $blockDef) {
            $slug = (string) $blockDef['slug'];
            $titleIt = (string) $blockDef['title_it'];
            $code = (string) get_post_meta($post->ID, 'fp_qr_info_disposal_block_' . $slug . '_code', true);
            $it = (string) get_post_meta($post->ID, 'fp_qr_info_disposal_block_' . $slug . '_it', true);
            $en = (string) get_post_meta($post->ID, 'fp_qr_info_disposal_block_' . $slug . '_en', true);
            ?>
            <fieldset style="border:1px solid #c3c4c7;padding:12px 14px;margin:0 0 14px;border-radius:6px;background:#fafafa;">
                <legend><strong><?php echo esc_html($titleIt); ?></strong></legend>
                <p>
                    <label for="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_code"><?php esc_html_e('Codice materiale / identificazione', 'fp-qr-info'); ?></label><br>
                    <input type="text" class="regular-text" id="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_code" name="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_code" value="<?php echo esc_attr($code); ?>" maxlength="64" autocomplete="off">
                </p>
                <p>
                    <label for="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_it"><?php esc_html_e('Testo (Italiano)', 'fp-qr-info'); ?></label><br>
                    <textarea id="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_it" name="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_it" rows="4" class="large-text"><?php echo esc_textarea($it); ?></textarea>
                </p>
                <p>
                    <label for="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_en"><?php esc_html_e('Text (English)', 'fp-qr-info'); ?></label><br>
                    <textarea id="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_en" name="fp_qr_info_disposal_block_<?php echo esc_attr($slug); ?>_en" rows="4" class="large-text"><?php echo esc_textarea($en); ?></textarea>
                </p>
            </fieldset>
            <?php
        }
        ?>
        <details style="margin:16px 0;padding:8px 0;border-top:1px solid #dcdcde;">
            <summary><?php esc_html_e('Smaltimento come HTML unico (retrocompatibilità)', 'fp-qr-info'); ?></summary>
            <p class="description"><?php esc_html_e('Usato solo se tutti e tre i blocchi sopra sono vuoti.', 'fp-qr-info'); ?></p>
            <?php
            $legacyIt = (string) get_post_meta($post->ID, 'fp_qr_info_disposal_it', true);
            $legacyEn = (string) get_post_meta($post->ID, 'fp_qr_info_disposal_en', true);
            ?>
            <p>
                <button type="button" class="button button-secondary fp-qri-insert-preset" data-preset="disposal">
                    <?php esc_html_e('Inserisci modello: testo normativo smaltimento (HTML)', 'fp-qr-info'); ?>
                </button>
            </p>
            <p>
                <label for="fp_qr_info_disposal_it"><?php esc_html_e('Italiano', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_disposal_it" name="fp_qr_info_disposal_it" rows="8" class="large-text code"><?php echo esc_textarea($legacyIt); ?></textarea>
            </p>
            <p>
                <label for="fp_qr_info_disposal_en"><?php esc_html_e('English', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_disposal_en" name="fp_qr_info_disposal_en" rows="8" class="large-text code"><?php echo esc_textarea($legacyEn); ?></textarea>
            </p>
        </details>
        <hr>
        <?php
        $presetButtonLabels = [
            'nutrition' => __('Inserisci modello: dichiarazione nutrizionale (tabella)', 'fp-qr-info'),
            'ingredients' => __('Inserisci modello: ingredienti / allergeni (vino)', 'fp-qr-info'),
        ];
        foreach ($fields as $fieldKey => $label) {
            $itValue = (string) get_post_meta($post->ID, 'fp_qr_info_' . $fieldKey . '_it', true);
            $enValue = (string) get_post_meta($post->ID, 'fp_qr_info_' . $fieldKey . '_en', true);
            $presetLabel = $presetButtonLabels[$fieldKey] ?? __('Inserisci modello normativo UE (vino)', 'fp-qr-info');
            ?>
            <p>
                <strong><?php echo esc_html($label); ?></strong>
            </p>
            <p>
                <button type="button" class="button button-secondary fp-qri-insert-preset" data-preset="<?php echo esc_attr($fieldKey); ?>">
                    <?php echo esc_html($presetLabel); ?>
                </button>
            </p>
            <p>
                <label for="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it"><?php esc_html_e('Italiano', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it" name="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_it" rows="12" class="large-text code"><?php echo esc_textarea($itValue); ?></textarea>
            </p>
            <p>
                <label for="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en"><?php esc_html_e('English', 'fp-qr-info'); ?></label><br>
                <textarea id="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en" name="fp_qr_info_<?php echo esc_attr($fieldKey); ?>_en" rows="12" class="large-text code"><?php echo esc_textarea($enValue); ?></textarea>
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
        $accentColor = isset($_POST['fp_qr_info_accent_color'])
            ? sanitize_hex_color(wp_unslash((string) $_POST['fp_qr_info_accent_color']))
            : null;
        update_post_meta($postId, self::META_ACCENT_COLOR, is_string($accentColor) && $accentColor !== '' ? $accentColor : '#5b21b6');

        $storyImageId = isset($_POST['fp_qr_info_story_image_id']) ? absint(wp_unslash((string) $_POST['fp_qr_info_story_image_id'])) : 0;
        if ($storyImageId > 0 && get_post($storyImageId) === null) {
            $storyImageId = 0;
        }
        if ($storyImageId > 0 && get_post_type($storyImageId) !== 'attachment') {
            $storyImageId = 0;
        }
        update_post_meta($postId, 'fp_qr_info_story_image_id', $storyImageId);

        $storyIt = isset($_POST['fp_qr_info_story_it'])
            ? sanitize_textarea_field(wp_unslash((string) $_POST['fp_qr_info_story_it']))
            : '';
        $storyEn = isset($_POST['fp_qr_info_story_en'])
            ? sanitize_textarea_field(wp_unslash((string) $_POST['fp_qr_info_story_en']))
            : '';
        update_post_meta($postId, 'fp_qr_info_story_it', $storyIt);
        update_post_meta($postId, 'fp_qr_info_story_en', $storyEn);

        foreach (LandingLegalPresets::DISPOSAL_BLOCK_SLUGS as $slug) {
            $codeKey = 'fp_qr_info_disposal_block_' . $slug . '_code';
            $itKey = 'fp_qr_info_disposal_block_' . $slug . '_it';
            $enKey = 'fp_qr_info_disposal_block_' . $slug . '_en';
            $codeVal = isset($_POST[$codeKey]) ? sanitize_text_field(wp_unslash((string) $_POST[$codeKey])) : '';
            $itVal = isset($_POST[$itKey]) ? wp_kses_post(wp_unslash((string) $_POST[$itKey])) : '';
            $enVal = isset($_POST[$enKey]) ? wp_kses_post(wp_unslash((string) $_POST[$enKey])) : '';
            update_post_meta($postId, $codeKey, $codeVal);
            update_post_meta($postId, $itKey, $itVal);
            update_post_meta($postId, $enKey, $enVal);
        }

        $legacyDisposalIt = isset($_POST['fp_qr_info_disposal_it'])
            ? wp_kses_post(wp_unslash((string) $_POST['fp_qr_info_disposal_it']))
            : '';
        $legacyDisposalEn = isset($_POST['fp_qr_info_disposal_en'])
            ? wp_kses_post(wp_unslash((string) $_POST['fp_qr_info_disposal_en']))
            : '';
        update_post_meta($postId, 'fp_qr_info_disposal_it', $legacyDisposalIt);
        update_post_meta($postId, 'fp_qr_info_disposal_en', $legacyDisposalEn);

        foreach (array_keys($this->getFields()) as $fieldKey) {
            $itValue = isset($_POST['fp_qr_info_' . $fieldKey . '_it'])
                ? wp_kses_post(wp_unslash((string) $_POST['fp_qr_info_' . $fieldKey . '_it']))
                : '';
            $enValue = isset($_POST['fp_qr_info_' . $fieldKey . '_en'])
                ? wp_kses_post(wp_unslash((string) $_POST['fp_qr_info_' . $fieldKey . '_en']))
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

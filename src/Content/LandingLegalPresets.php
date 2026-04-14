<?php

declare(strict_types=1);

namespace FP\QrInfo\Content;

/**
 * Modelli HTML indicativi per smaltimento, dichiarazione nutrizionale e ingredienti (prassi UE, vino).
 *
 * Testi e layout sono strumenti editoriali: la responsabilità del contenuto conforme resta sul produttore.
 */
final class LandingLegalPresets
{
    public const PLACEHOLDER = '{{FP_QR_INFO_ICONS_URL}}';

    /**
     * URL base delle icone statiche del plugin (SVG).
     */
    public static function iconsBaseUrl(): string
    {
        if (!defined('FP_QR_INFO_URL')) {
            return '';
        }

        return trailingslashit((string) FP_QR_INFO_URL . 'assets/icons/');
    }

    /**
     * Sostituisce il placeholder con l'URL reale delle icone.
     */
    public static function expandPlaceholder(string $html): string
    {
        return str_replace(self::PLACEHOLDER, esc_url(self::iconsBaseUrl()), $html);
    }

    /**
     * Preset per editor admin (IT/EN), con placeholder icone.
     *
     * @return array<string, array{it: string, en: string}>
     */
    public static function getPresets(): array
    {
        return [
            'disposal' => [
                'it' => self::disposalItHtml(),
                'en' => self::disposalEnHtml(),
            ],
            'nutrition' => [
                'it' => self::nutritionItHtml(),
                'en' => self::nutritionEnHtml(),
            ],
            'ingredients' => [
                'it' => self::ingredientsItHtml(),
                'en' => self::ingredientsEnHtml(),
            ],
        ];
    }

    private static function disposalItHtml(): string
    {
        $icons = self::iconRowHtml();

        return $icons
            . '<p><strong>' . esc_html__('Smaltimento degli imballaggi', 'fp-qr-info') . '</strong><br />'
            . esc_html__(
                'Imballaggi: conferire secondo le norme del proprio Comune sulla raccolta differenziata (vetro, carta/cartone, plastica, legno, metalli dove previsto).',
                'fp-qr-info'
            )
            . '</p>'
            . '<p>' . esc_html__(
                'Simboli: il simbolo del riciclaggio (Möbius) richiama l’obbligo di gestione responsabile dei rifiuti di imballaggio; le modalità concrete dipendono dagli impianti locali.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Modello indicativo (normativa UE su imballaggi e informazione ambientale). Verificare testi definitivi con il vostro consulente legale.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function disposalEnHtml(): string
    {
        $icons = self::iconRowHtml();

        return $icons
            . '<p><strong>' . esc_html__('Packaging disposal', 'fp-qr-info') . '</strong><br />'
            . esc_html__(
                'Dispose of packaging according to your local authority rules for separate collection (glass, paper/cardboard, plastic, wood, metals where applicable).',
                'fp-qr-info'
            )
            . '</p>'
            . '<p>' . esc_html__(
                'Symbols: the chasing-arrows (Möbius) mark reminds consumers that packaging waste should be managed responsibly; actual sorting rules depend on local infrastructure.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Indicative template (EU packaging / environmental information practice). Please validate final wording with your legal advisor.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function nutritionItHtml(): string
    {
        $head = '<p><strong>' . esc_html__('Dichiarazione nutrizionale', 'fp-qr-info') . '</strong> — '
            . esc_html__('per 100 ml (Reg. UE 1169/2011, Allegato XV — valori da compilare in laboratorio)', 'fp-qr-info')
            . '</p>';

        $table = '<table class="fpqi-nutrition-table"><thead><tr>'
            . '<th scope="col">' . esc_html__('Voce', 'fp-qr-info') . '</th>'
            . '<th scope="col">' . esc_html__('per 100 ml', 'fp-qr-info') . '</th>'
            . '</tr></thead><tbody>'
            . '<tr><th scope="row">' . esc_html__('Energia', 'fp-qr-info') . '</th><td>… kJ / … kcal</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Grassi', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('di cui acidi grassi saturi', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Carboidrati', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('di cui zuccheri', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Proteine', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Sale', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '</tbody></table>';

        $foot = '<p><em>' . esc_html__(
            'Sostituire i puntini con i valori analitici del lotto. Per bevande alcoliche valgono deroghe/quadri specifici: adeguare la tabella alle indicazioni del consulente di settore.',
            'fp-qr-info'
        ) . '</em></p>';

        return $head . $table . $foot;
    }

    private static function nutritionEnHtml(): string
    {
        $head = '<p><strong>' . esc_html__('Nutrition declaration', 'fp-qr-info') . '</strong> — '
            . esc_html__('per 100 ml (EU Reg. 1169/2011, Annex XV — values to be filled from lab analysis)', 'fp-qr-info')
            . '</p>';

        $table = '<table class="fpqi-nutrition-table"><thead><tr>'
            . '<th scope="col">' . esc_html__('Item', 'fp-qr-info') . '</th>'
            . '<th scope="col">' . esc_html__('per 100 ml', 'fp-qr-info') . '</th>'
            . '</tr></thead><tbody>'
            . '<tr><th scope="row">' . esc_html__('Energy', 'fp-qr-info') . '</th><td>… kJ / … kcal</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Fat', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('of which saturates', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Carbohydrate', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('of which sugars', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Protein', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Salt', 'fp-qr-info') . '</th><td>… g</td></tr>'
            . '</tbody></table>';

        $foot = '<p><em>' . esc_html__(
            'Replace the ellipses with batch analytical values. Alcoholic beverages may follow specific EU formats/exemptions—adjust with your regulatory advisor.',
            'fp-qr-info'
        ) . '</em></p>';

        return $head . $table . $foot;
    }

    private static function ingredientsItHtml(): string
    {
        return '<p><span class="fpqi-allergen-warn" role="img" aria-label="'
            . esc_attr__('Attenzione allergeni', 'fp-qr-info')
            . '">⚠</span> <strong>'
            . esc_html__('Allergeni', 'fp-qr-info')
            . '</strong>: '
            . esc_html__(
                'evidenziare in grassetto gli ingredienti e le sostanze che causano allergie o intolleranze (Reg. UE 1169/2011, art. 21).',
                'fp-qr-info'
            )
            . '</p>'
            . '<p><strong>' . esc_html__('Ingredienti (modello vino)', 'fp-qr-info') . '</strong><br />'
            . esc_html__('Uve.', 'fp-qr-info')
            . ' <strong>' . esc_html__('Contiene solfiti', 'fp-qr-info') . '</strong> '
            . esc_html__('(anidride solforosa).', 'fp-qr-info')
            . '</p>'
            . '<p>' . esc_html__(
                'Aggiungere eventuali coadiuvanti di chiarifica filtrati (es. albumina, caseina) solo se effettivamente impiegati e secondo normativa vigente.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Modello indicativo: sostituire con la lista reale del prodotto e con la consulenza del vostro ufficio conformità.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function ingredientsEnHtml(): string
    {
        return '<p><span class="fpqi-allergen-warn" role="img" aria-label="'
            . esc_attr__('Allergen notice', 'fp-qr-info')
            . '">⚠</span> <strong>'
            . esc_html__('Allergens', 'fp-qr-info')
            . '</strong>: '
            . esc_html__(
                'emphasise in bold ingredients and substances causing allergies or intolerances (EU Reg. 1169/2011, Art. 21).',
                'fp-qr-info'
            )
            . '</p>'
            . '<p><strong>' . esc_html__('Ingredients (wine template)', 'fp-qr-info') . '</strong><br />'
            . esc_html__('Grapes.', 'fp-qr-info')
            . ' <strong>' . esc_html__('Contains sulphites', 'fp-qr-info') . '</strong> '
            . esc_html__('(sulphur dioxide).', 'fp-qr-info')
            . '</p>'
            . '<p>' . esc_html__(
                'Add any filtration aids (e.g. albumin, casein) only if actually used and as required by applicable law.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Indicative template: replace with the real product list and your compliance officer’s review.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function iconRowHtml(): string
    {
        $base = esc_url(self::iconsBaseUrl());

        return '<div class="fpqi-preset-icons" role="presentation">'
            . '<span class="fpqi-recycle-char" role="img" aria-label="'
            . esc_attr__('Simbolo internazionale del riciclaggio (Unicode U+267B)', 'fp-qr-info')
            . '">&#9851;</span>'
            . '<img class="fpqi-legal-icon" src="' . $base . 'glass-bottle.svg" width="48" height="48" alt="" loading="lazy" decoding="async" />'
            . '</div>';
    }
}

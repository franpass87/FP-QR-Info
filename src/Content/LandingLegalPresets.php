<?php

declare(strict_types=1);

namespace FP\QrInfo\Content;

/**
 * Modelli HTML per smaltimento, dichiarazione nutrizionale e ingredienti, allineati ai riferimenti normativi UE citati nel testo.
 *
 * Riferimenti principali: Reg. (UE) n. 1169/2011 (FIC), in particolare artt. 18, 21 e 30–34 e Allegato XV;
 * per i prodotti vitivinicoli anche Reg. (UE) 2021/2117; per gli imballaggi Direttiva (UE) 2018/852 e Decisione 97/129/CE.
 * I testi restano strumenti editoriali: la validazione finale spetta al responsabile di messa a mercato.
 */
final class LandingLegalPresets
{
    public const PLACEHOLDER = '{{FP_QR_INFO_ICONS_URL}}';

    /** @var list<string> */
    public const DISPOSAL_BLOCK_SLUGS = ['cork', 'bottle', 'capsule'];

    /**
     * Definizione blocchi smaltimento (Tappo, Bottiglia, Capsula) per titoli bilingue.
     *
     * @return list<array{slug: string, title_it: string, title_en: string}>
     */
    public static function getDisposalBlockDefinitions(): array
    {
        return [
            [
                'slug' => 'cork',
                'title_it' => __('Tappo', 'fp-qr-info'),
                'title_en' => __('Cork (closure)', 'fp-qr-info'),
            ],
            [
                'slug' => 'bottle',
                'title_it' => __('Bottiglia', 'fp-qr-info'),
                'title_en' => __('Bottle', 'fp-qr-info'),
            ],
            [
                'slug' => 'capsule',
                'title_it' => __('Capsula', 'fp-qr-info'),
                'title_en' => __('Capsule / sleeve', 'fp-qr-info'),
            ],
        ];
    }

    /**
     * Valori di esempio per i blocchi (codici tipo Decisione 97/129/CE — verificare sul prodotto reale).
     *
     * @return array<string, array{code: string, it: string, en: string}>
     */
    public static function getDisposalBlockExamples(): array
    {
        return [
            'cork' => [
                'code' => 'FOR 51',
                'it' => "Sughero\n" . __('Raccolta differenziata dedicata', 'fp-qr-info'),
                'en' => "Cork\n" . __('Dedicated separate collection', 'fp-qr-info'),
            ],
            'bottle' => [
                'code' => 'GL 70',
                'it' => __('Vetro incolore', 'fp-qr-info') . "\n" . __('Raccolta vetro', 'fp-qr-info'),
                'en' => __('Clear glass', 'fp-qr-info') . "\n" . __('Glass collection', 'fp-qr-info'),
            ],
            'capsule' => [
                'code' => 'C/PVC 90',
                'it' => __('Capsule in PVC', 'fp-qr-info') . "\n" . __('Raccolta plastica', 'fp-qr-info'),
                'en' => __('PVC capsules', 'fp-qr-info') . "\n" . __('Plastic collection', 'fp-qr-info'),
            ],
        ];
    }

    /**
     * URL base delle icone statiche del plugin (SVG illustrativi, non sostituiscono marchi o codici obbligatori nazionali).
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
     * Preset per editor admin (IT/EN).
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
        $icons = self::iconRowHtml('it');

        return $icons
            . '<p><strong>' . esc_html__('Informazioni per lo smaltimento degli imballaggi', 'fp-qr-info') . '</strong></p>'
            . '<p>' . esc_html__(
                'Riferimenti: Direttiva (UE) 2018/852 del Parlamento europeo e del Consiglio, recante modifica della direttiva 94/62/CE relativa agli imballaggi e ai rifiuti di imballaggio, e normativa nazionale di attuazione per la gestione dei rifiuti e la raccolta differenziata.',
                'fp-qr-info'
            ) . '</p>'
            . '<p>' . esc_html__(
                'Identificazione della materia dell’imballaggio: la Decisione della Commissione 97/129/CE (come da revisioni successive) disciplina il sistema di numerazione/codifica volontario per l’identificazione del materiale (es. codici numerici associati alle materie plastiche, al vetro, ecc.). Verificare presso il proprio consulente quale combinazione di codici o marchi è richiesta o consentita sul mercato di destinazione.',
                'fp-qr-info'
            ) . '</p>'
            . '<p>' . esc_html__(
                'Simbolo Unicode U+267B (BLACK UNIVERSAL RECYCLING SYMBOL, ISO/IEC 10646): spesso usato come richiamo grafico alla raccolta; non sostituisce da solo obblighi nazionali di etichettatura ambientale o di riciclaggio.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Conferire gli imballaggi secondo le istruzioni del proprio ente locale di raccolta. Validare testi e pittogrammi con il consulente legale/ambientale.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function disposalEnHtml(): string
    {
        $icons = self::iconRowHtml('en');

        return $icons
            . '<p><strong>' . esc_html__('Information on packaging disposal', 'fp-qr-info') . '</strong></p>'
            . '<p>' . esc_html__(
                'References: Directive (EU) 2018/852 of the European Parliament and of the Council amending Directive 94/62/EC on packaging and packaging waste, and national implementing rules on waste management and separate collection.',
                'fp-qr-info'
            ) . '</p>'
            . '<p>' . esc_html__(
                'Packaging material identification: Commission Decision 97/129/EC (as amended) establishes the voluntary numbering/identification system for packaging materials (e.g. numeric codes for plastics, glass, etc.). Check with your advisor which codes or marks are mandatory or permitted on your target market.',
                'fp-qr-info'
            ) . '</p>'
            . '<p>' . esc_html__(
                'Unicode character U+267B (BLACK UNIVERSAL RECYCLING SYMBOL, ISO/IEC 10646) is often used as a consumer cue for recycling; it alone does not replace national environmental labelling or recycling obligations.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'Dispose of packaging according to your local waste authority instructions. Validate wording and pictograms with your legal/environmental advisor.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function nutritionItHtml(): string
    {
        $head = '<p><strong>' . esc_html__('Dichiarazione nutrizionale', 'fp-qr-info') . '</strong> — '
            . esc_html__('per 100 ml', 'fp-qr-info') . '<br />'
            . '<span class="fpqi-legal-ref">'
            . esc_html__(
                'Regolamento (UE) n. 1169/2011: articoli 30, 31, 32 e 34 e Allegato XV, parte B (ordine e unità di misura). Per i prodotti vitivinicoli si applicano anche le disposizioni del regolamento (UE) 2021/2117 in materia di informazioni obbligatorie, inclusa la presentazione per mezzi elettronici ove consentita.',
                'fp-qr-info'
            )
            . '</span></p>';

        $table = '<p class="fpqi-sr-only">' . esc_html__('Dichiarazione nutrizionale per 100 ml', 'fp-qr-info') . '</p>'
            . '<table class="fpqi-nutrition-table"><thead><tr>'
            . '<th scope="col">' . esc_html__('Dato nutrizionale', 'fp-qr-info') . '</th>'
            . '<th scope="col">' . esc_html__('per 100 ml', 'fp-qr-info') . '</th>'
            . '</tr></thead><tbody>'
            . '<tr><th scope="row">' . esc_html__('Valore energetico', 'fp-qr-info') . '</th><td>330 kJ / 79 kcal</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Grassi', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('di cui acidi grassi saturi', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Carboidrati', 'fp-qr-info') . '</th><td>2,6 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('di cui zuccheri', 'fp-qr-info') . '</th><td>0,6 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Proteine', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Sale', 'fp-qr-info') . '</th><td>0,01 g</td></tr>'
            . '</tbody></table>';

        $saltNote = '<p class="fpqi-legal-note"><em>' . esc_html__(
            'Nota (art. 30, paragrafo 5, del regolamento (UE) n. 1169/2011): ove il tenore di sale sia imputabile unicamente alla presenza di sodio naturalmente presente nell’alimento, può essere indicato in prossimità della dichiarazione nutrizionale che il tenore di sale è dovuto unicamente al sodio naturalmente presente.',
            'fp-qr-info'
        ) . '</em></p>';

        $foot = '<p><em>' . esc_html__(
            'Valori medi per vino per 100 ml: energia 330 kJ / 79 kcal; grassi 0 g (di cui saturi 0 g); carboidrati 2,6 g (di cui zuccheri 0,6 g); proteine 0 g; sale 0,01 g.',
            'fp-qr-info'
        ) . '</em></p>';

        return $head . $table . $saltNote . $foot;
    }

    private static function nutritionEnHtml(): string
    {
        $head = '<p><strong>' . esc_html__('Nutrition declaration', 'fp-qr-info') . '</strong> — '
            . esc_html__('per 100 ml', 'fp-qr-info') . '<br />'
            . '<span class="fpqi-legal-ref">'
            . esc_html__(
                'Regulation (EU) No 1169/2011: Articles 30, 31, 32 and 34 and Annex XV, Part B (order and units of measurement). For grapevine-based products, Regulation (EU) 2021/2117 also applies to mandatory particulars, including electronic presentation where permitted.',
                'fp-qr-info'
            )
            . '</span></p>';

        $table = '<p class="fpqi-sr-only">' . esc_html__('Nutrition declaration per 100 ml', 'fp-qr-info') . '</p>'
            . '<table class="fpqi-nutrition-table"><thead><tr>'
            . '<th scope="col">' . esc_html__('Nutrition information', 'fp-qr-info') . '</th>'
            . '<th scope="col">' . esc_html__('per 100 ml', 'fp-qr-info') . '</th>'
            . '</tr></thead><tbody>'
            . '<tr><th scope="row">' . esc_html__('Energy', 'fp-qr-info') . '</th><td>330 kJ / 79 kcal</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Fat', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('of which saturates', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Carbohydrate', 'fp-qr-info') . '</th><td>2.6 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('of which sugars', 'fp-qr-info') . '</th><td>0.6 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Protein', 'fp-qr-info') . '</th><td>0 g</td></tr>'
            . '<tr><th scope="row">' . esc_html__('Salt', 'fp-qr-info') . '</th><td>0.01 g</td></tr>'
            . '</tbody></table>';

        $saltNote = '<p class="fpqi-legal-note"><em>' . esc_html__(
            'Note (Article 30(5) of Regulation (EU) No 1169/2011): where the salt content is exclusively due to the presence of naturally occurring sodium, a statement may be indicated in close proximity to the nutrition declaration to the effect that the salt content is exclusively due to the presence of naturally occurring sodium.',
            'fp-qr-info'
        ) . '</em></p>';

        $foot = '<p><em>' . esc_html__(
            'Average wine values per 100 ml: energy 330 kJ / 79 kcal; fat 0 g (of which saturates 0 g); carbohydrate 2.6 g (of which sugars 0.6 g); protein 0 g; salt 0.01 g.',
            'fp-qr-info'
        ) . '</em></p>';

        return $head . $table . $saltNote . $foot;
    }

    private static function ingredientsItHtml(): string
    {
        return '<p><strong>' . esc_html__('Ingredienti', 'fp-qr-info') . '</strong> — '
            . '<span class="fpqi-legal-ref">'
            . esc_html__(
                'Regolamento (UE) n. 1169/2011: articoli 18, 20, 21 e 22 e allegati pertinenti; per l’elenco degli ingredienti dei prodotti vitivinicoli si applicano anche il regolamento (UE) 2021/2117 e il regolamento delegato (UE) 2023/1606 della Commissione, ove rilevanti.',
                'fp-qr-info'
            )
            . '</span></p>'
            . '<p>' . esc_html__(
                'Le sostanze o i prodotti elencati nell’allegato II che causano allergie o intolleranze devono essere evidenziati come richiesto dall’articolo 21, paragrafo 1, lettera a), del regolamento (UE) n. 1169/2011.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><strong>' . esc_html__('Vino — dichiarazione ingredienti', 'fp-qr-info') . '</strong><br />'
            . esc_html__('Ingredienti:', 'fp-qr-info') . ' '
            . esc_html__('uva', 'fp-qr-info') . '; '
            . esc_html__('conservatore:', 'fp-qr-info') . ' <strong>' . esc_html__('anidride solforosa', 'fp-qr-info') . '</strong> / <strong>'
            . esc_html__('solfiti', 'fp-qr-info') . '</strong>.</p>'
            . '<p>' . esc_html__(
                'Ove siano utilizzati additivi, coadiuvanti tecnologici o altri ingredienti, inserirli con la denominazione legalmente prevista (inclusi i numeri E ove applicabili) e nell’ordine previsto dal regolamento (UE) n. 1169/2011.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'L’indicazione «contiene solfiti» può essere richiesta sull’etichetta fisica ai sensi della normativa sui vini anche quando l’elenco completo è fornito per via elettronica: verificare il testo obbligatorio sul recipiente con il consulente di settore.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    private static function ingredientsEnHtml(): string
    {
        return '<p><strong>' . esc_html__('Ingredients', 'fp-qr-info') . '</strong> — '
            . '<span class="fpqi-legal-ref">'
            . esc_html__(
                'Regulation (EU) No 1169/2011: Articles 18, 20, 21 and 22 and relevant annexes; for the list of ingredients of grapevine-based products, Regulation (EU) 2021/2117 and Commission Delegated Regulation (EU) 2023/1606 also apply where relevant.',
                'fp-qr-info'
            )
            . '</span></p>'
            . '<p>' . esc_html__(
                'Substances or products listed in Annex II causing allergies or intolerances shall be emphasised as required by Article 21(1)(a) of Regulation (EU) No 1169/2011.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><strong>' . esc_html__('Wine — ingredients declaration', 'fp-qr-info') . '</strong><br />'
            . esc_html__('Ingredients:', 'fp-qr-info') . ' '
            . esc_html__('grapes', 'fp-qr-info') . '; '
            . esc_html__('preservative:', 'fp-qr-info') . ' <strong>' . esc_html__('sulphur dioxide', 'fp-qr-info') . '</strong> / <strong>'
            . esc_html__('sulphites', 'fp-qr-info') . '</strong>.</p>'
            . '<p>' . esc_html__(
                'Where additives, technological aids or other ingredients are used, list them with the legally required designation (including E numbers where applicable) and in the order required by Regulation (EU) No 1169/2011.',
                'fp-qr-info'
            ) . '</p>'
            . '<p><em>' . esc_html__(
                'The “contains sulphites” indication may be required on the physical label under wine-sector rules even when the full list is provided electronically: confirm mandatory on-pack wording with your sector advisor.',
                'fp-qr-info'
            ) . '</em></p>';
    }

    /**
     * @param string $lang Codice lingua per testi alternativi su immagini (it|en).
     */
    private static function iconRowHtml(string $lang): string
    {
        $base = esc_url(self::iconsBaseUrl());
        $glassAlt = $lang === 'en'
            ? esc_attr__('Illustrative glass packaging pictogram (not a mandatory Union-wide mark)', 'fp-qr-info')
            : esc_attr__('Pittogramma illustrativo dell’imballaggio in vetro (non è un marchio obbligatorio armonizzato a livello UE)', 'fp-qr-info');
        $recycleLabel = $lang === 'en'
            ? esc_attr__('Black Universal Recycling Symbol (Unicode U+267B, ISO/IEC 10646)', 'fp-qr-info')
            : esc_attr__('Simbolo universale del riciclaggio nero (Unicode U+267B, ISO/IEC 10646)', 'fp-qr-info');

        return '<div class="fpqi-preset-icons" role="group" aria-label="'
            . esc_attr__('Simboli informativi', 'fp-qr-info') . '">'
            . '<span class="fpqi-recycle-char" role="img" aria-label="' . $recycleLabel . '">&#9851;</span>'
            . '<img class="fpqi-legal-icon" src="' . $base . 'glass-bottle.svg" width="48" height="48" alt="'
            . $glassAlt . '" loading="lazy" decoding="async" />'
            . '</div>';
    }
}

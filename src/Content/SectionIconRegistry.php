<?php

declare(strict_types=1);

namespace FP\QrInfo\Content;

/**
 * Registry centralizzato di icone SVG selezionabili per le card della scheda prodotto
 * (Sentori e profumi, Abbinamenti, Note di servizio).
 *
 * Le icone sono SVG inline, viewBox 24x24, stile lineare (stroke=2), monocromatiche.
 * Usano `stroke="currentColor"` per ereditare il colore accent della landing.
 *
 * Le stringhe SVG sono statiche e curate: vengono emesse direttamente senza
 * passare da `wp_kses_post` (che strippa molti attributi SVG). NON inserire
 * mai input utente in questa classe.
 */
final class SectionIconRegistry
{
    public const SLUG_NONE = 'none';

    /**
     * Restituisce l'intero registry indicizzato per slug.
     *
     * @return array<string, array{label_it: string, label_en: string, emoji: string, svg: string}>
     */
    public static function all(): array
    {
        return [
            self::SLUG_NONE => [
                'label_it' => __('Nessuna icona', 'fp-qr-info'),
                'label_en' => __('No icon', 'fp-qr-info'),
                'emoji'    => '',
                'svg'      => '',
            ],
            'wine-glass' => [
                'label_it' => __('Bicchiere di vino', 'fp-qr-info'),
                'label_en' => __('Wine glass', 'fp-qr-info'),
                'emoji'    => '🍷',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M8 22h8"/><path d="M7 10h10"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/></svg>',
            ],
            'grape' => [
                'label_it' => __('Grappolo d\'uva', 'fp-qr-info'),
                'label_en' => __('Grapes', 'fp-qr-info'),
                'emoji'    => '🍇',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22 5V2l-5.89 5.89"/><circle cx="16.6" cy="15.89" r="3"/><circle cx="8.11" cy="7.4" r="3"/><circle cx="12.35" cy="11.65" r="3"/><circle cx="13.91" cy="5.85" r="3"/><circle cx="18.15" cy="10.09" r="3"/><circle cx="6.56" cy="13.2" r="3"/><circle cx="10.8" cy="17.44" r="3"/><circle cx="5" cy="19" r="3"/></svg>',
            ],
            'leaf' => [
                'label_it' => __('Foglia', 'fp-qr-info'),
                'label_en' => __('Leaf', 'fp-qr-info'),
                'emoji'    => '🌿',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19.8 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/></svg>',
            ],
            'flower' => [
                'label_it' => __('Fiore', 'fp-qr-info'),
                'label_en' => __('Flower', 'fp-qr-info'),
                'emoji'    => '🌸',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="6" r="3"/><circle cx="12" cy="18" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="12" r="3"/><circle cx="12" cy="12" r="2.5"/></svg>',
            ],
            'sparkles' => [
                'label_it' => __('Aroma / Profumo', 'fp-qr-info'),
                'label_en' => __('Aroma', 'fp-qr-info'),
                'emoji'    => '✨',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m12 3-1.9 5.8L4 10l5.8 1.9L12 18l1.9-5.8L20 10l-5.8-1.9z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>',
            ],
            'cutlery' => [
                'label_it' => __('Forchetta e coltello', 'fp-qr-info'),
                'label_en' => __('Cutlery', 'fp-qr-info'),
                'emoji'    => '🍴',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>',
            ],
            'chef-hat' => [
                'label_it' => __('Cappello da chef', 'fp-qr-info'),
                'label_en' => __('Chef hat', 'fp-qr-info'),
                'emoji'    => '👨‍🍳',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>',
            ],
            'cheese' => [
                'label_it' => __('Formaggio', 'fp-qr-info'),
                'label_en' => __('Cheese', 'fp-qr-info'),
                'emoji'    => '🧀',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M19 5 4 9v10h17V8z"/><path d="M19 5 5 9"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="14" r="1"/><circle cx="11" cy="16.5" r="0.7"/></svg>',
            ],
            'bread' => [
                'label_it' => __('Pane', 'fp-qr-info'),
                'label_en' => __('Bread', 'fp-qr-info'),
                'emoji'    => '🍞',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 11a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M8 11v8"/><path d="M12 11v8"/><path d="M16 11v8"/></svg>',
            ],
            'thermometer' => [
                'label_it' => __('Termometro', 'fp-qr-info'),
                'label_en' => __('Thermometer', 'fp-qr-info'),
                'emoji'    => '🌡️',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/></svg>',
            ],
            'snowflake' => [
                'label_it' => __('Servire freddo', 'fp-qr-info'),
                'label_en' => __('Cold service', 'fp-qr-info'),
                'emoji'    => '❄️',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>',
            ],
            'clock' => [
                'label_it' => __('Tempo / Orologio', 'fp-qr-info'),
                'label_en' => __('Time / Clock', 'fp-qr-info'),
                'emoji'    => '⏱️',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            ],
            'decanter' => [
                'label_it' => __('Decanter / Caraffa', 'fp-qr-info'),
                'label_en' => __('Decanter', 'fp-qr-info'),
                'emoji'    => '🍶',
                'svg'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 3h6v3l3 7v6a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3v-6l3-7V3Z"/><path d="M9 6h6"/></svg>',
            ],
        ];
    }

    /**
     * Restituisce l'SVG inline associato a uno slug, oppure stringa vuota.
     *
     * @param string $slug Slug icona dal registry.
     * @return string SVG inline (sicuro per echo diretto) oppure stringa vuota.
     */
    public static function getSvg(string $slug): string
    {
        $registry = self::all();
        if (!isset($registry[$slug])) {
            return '';
        }

        return (string) $registry[$slug]['svg'];
    }

    /**
     * Verifica se uno slug e presente nel registry (incluso 'none').
     *
     * @param string $slug Slug icona da validare.
     * @return bool True se conosciuto, false altrimenti.
     */
    public static function isValid(string $slug): bool
    {
        return isset(self::all()[$slug]);
    }

    /**
     * Restituisce le opzioni per un <select> admin: slug => "{emoji} {label IT}".
     *
     * Formato pensato per essere leggibile a colpo d'occhio nel dropdown WP standard,
     * dove l'emoji da' un hint visivo immediato del tipo di icona scelto.
     *
     * @return array<string, string>
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::all() as $slug => $data) {
            $emoji = (string) $data['emoji'];
            $label = (string) $data['label_it'];
            $options[$slug] = $emoji !== '' ? $emoji . ' ' . $label : $label;
        }

        return $options;
    }

    /**
     * Restituisce l'etichetta accessibile (IT) per uno slug, utile come `aria-label`
     * o `title` quando l'icona viene renderizzata sul frontend.
     *
     * @param string $slug Slug icona.
     * @return string Label IT, vuota se slug == 'none' o sconosciuto.
     */
    public static function getAccessibleLabel(string $slug): string
    {
        if ($slug === self::SLUG_NONE) {
            return '';
        }
        $registry = self::all();
        if (!isset($registry[$slug])) {
            return '';
        }

        return (string) $registry[$slug]['label_it'];
    }
}

<?php

declare(strict_types=1);

namespace FP\QrInfo\Admin;

/**
 * Contenuti della "Guida AI" per la generazione assistita dei testi
 * editoriali della landing QR (storia, scheda prodotto).
 *
 * Espone:
 * - linee guida generali (HTML sicuro, tono, lingue, claim vietati);
 * - prompt copiabili separati per ogni campo (Storia, Sentori, Abbinamenti, Servizio).
 *
 * Tutto il contenuto e' statico e localizzabile. Nessun input utente entra
 * in questa classe: i prompt sono hardcoded e curati. NON aggiungere mai
 * sostituzioni dinamiche di stringhe basate su `$_POST` / meta utente.
 *
 * Modello editoriale di base: VINO. Una nota nel prompt indica come
 * adattare ad altri prodotti (olio, conserve, cosmetici) sostituendo
 * "vino" con la categoria pertinente e i campi tecnici corrispondenti.
 */
final class AiGuide
{
    /**
     * Restituisce le linee guida generali (titolo + bullet points HTML).
     *
     * @return array{title: string, intro: string, items: list<string>}
     */
    public static function getGeneralGuidelines(): array
    {
        return [
            'title' => __('Linee guida generali', 'fp-qr-info'),
            'intro' => __('Le indicazioni qui sotto valgono per tutti i campi editoriali (storia + scheda prodotto). Per smaltimento, nutrizionali e ingredienti NON usare AI: sono campi normativi, usa i modelli precompilati nel metabox sotto.', 'fp-qr-info'),
            'items' => [
                __('<strong>Lingue</strong>: produci sempre <strong>IT + EN</strong> separate. Il plugin e bilingue e mostra entrambe le versioni con switch di lingua.', 'fp-qr-info'),
                __('<strong>HTML sicuro consentito</strong>: <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, <code>&lt;a href&gt;</code>, <code>&lt;br&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;&lt;li&gt;</code>, <code>&lt;ol&gt;</code>, <code>&lt;span style=&quot;...&quot;&gt;</code> con stili CSS sicuri. WordPress filtra automaticamente con <code>wp_kses_post</code>.', 'fp-qr-info'),
                __('<strong>HTML vietato</strong>: <code>&lt;script&gt;</code>, <code>&lt;iframe&gt;</code>, <code>&lt;form&gt;</code>, attributi <code>on*</code> (onclick, onload), <code>style</code> con <code>position:fixed/absolute</code> o <code>javascript:</code>, link a domini sospetti.', 'fp-qr-info'),
                __('<strong>Tono</strong>: descrittivo, sobrio, fattuale. Evita superlativi vuoti ("eccellente", "il migliore", "unico al mondo"). Privilegia dettagli verificabili.', 'fp-qr-info'),
                __('<strong>Claim salutistici/nutrizionali</strong>: VIETATI nei testi narrativi (Reg. UE 1924/2006). Niente "fa bene", "ricco di antiossidanti", "senza solfiti aggiunti" se non documentato.', 'fp-qr-info'),
                __('<strong>Lunghezze</strong>: i prompt indicano un range di parole per campo. Rispettalo per non rompere il layout della landing su mobile.', 'fp-qr-info'),
                __('<strong>Revisione editoriale</strong>: l\'AI puo allucinare dati (annate, denominazioni, premi). Verifica SEMPRE prima di pubblicare, soprattutto numeri, anni e claim tecnici.', 'fp-qr-info'),
                __('<strong>Adattabile ad altri prodotti</strong>: i prompt sono ottimizzati per il VINO. Per olio, conserve, cosmetici, ecc. sostituisci "vino" con la categoria e i campi tecnici corrispondenti (es. spremitura, conservazione, ingredienti chiave).', 'fp-qr-info'),
            ],
        ];
    }

    /**
     * Restituisce i prompt copiabili separati per ogni campo editoriale.
     *
     * @return list<array{id: string, title: string, target_field: string, description: string, prompt: string}>
     */
    public static function getFieldPrompts(): array
    {
        return [
            [
                'id'           => 'story',
                'title'        => __('Storia ed etichetta', 'fp-qr-info'),
                'target_field' => __('Campi: "Storia (Italiano)" + "Story (English)"', 'fp-qr-info'),
                'description'  => __('Narrativa breve sul vino, sull\'azienda o sul significato del nome/etichetta. NON e una scheda tecnica: serve a creare contesto, evocare territorio e identita.', 'fp-qr-info'),
                'prompt'       => self::buildStoryPrompt(),
            ],
            [
                'id'           => 'tasting',
                'title'        => __('Sentori e profumi', 'fp-qr-info'),
                'target_field' => __('Campi: "Sentori e profumi" IT/EN (sezione "Scheda prodotto")', 'fp-qr-info'),
                'description'  => __('Descrittivo olfattivo e gustativo. Note di degustazione coerenti con vitigno, annata e affinamento. Stile evocativo ma sobrio.', 'fp-qr-info'),
                'prompt'       => self::buildTastingPrompt(),
            ],
            [
                'id'           => 'pairings',
                'title'        => __('Abbinamenti', 'fp-qr-info'),
                'target_field' => __('Campi: "Abbinamenti" IT/EN (sezione "Scheda prodotto")', 'fp-qr-info'),
                'description'  => __('Suggerimenti di abbinamento gastronomico concreti (3-5 piatti specifici, non solo categorie generiche).', 'fp-qr-info'),
                'prompt'       => self::buildPairingsPrompt(),
            ],
            [
                'id'           => 'service',
                'title'        => __('Note di servizio', 'fp-qr-info'),
                'target_field' => __('Campi: "Note di servizio" IT/EN (sezione "Scheda prodotto")', 'fp-qr-info'),
                'description'  => __('Indicazioni pratiche di servizio: temperatura, calice, decantazione, tempo di apertura.', 'fp-qr-info'),
                'prompt'       => self::buildServicePrompt(),
            ],
        ];
    }

    /**
     * Prompt per la sezione "Storia ed etichetta".
     */
    private static function buildStoryPrompt(): string
    {
        return <<<'PROMPT'
Sei un copywriter editoriale specializzato in storytelling enologico.

COMPITO
Scrivi un breve testo narrativo (60-90 parole IT, 60-90 parole EN) per la sezione "Storia ed etichetta" della landing QR di un vino, da pubblicare su retro etichetta.

CONTESTO PRODOTTO (sostituisci i [...]):
- Nome del vino: [es. Capitolare]
- Cantina / produttore: [es. Rocca Bernarda]
- Denominazione: [es. Friuli Colli Orientali DOC]
- Vitigno principale: [es. Pignolo]
- Anno della prima annata o riferimento storico: [es. dal 2006]
- Significato del nome (se rilevante): [es. Capitolare = assemblea dei Cavalieri di Malta]
- Particolarita di territorio o tecnica: [es. resa bassissima, affinato in legno]

VINCOLI
- Lunghezza: 60-90 parole per lingua.
- Tono: narrativo ma sobrio. Niente superlativi vuoti ("eccellente", "il migliore", "unico al mondo").
- HTML sicuro consentito: <strong>, <em>, <br>, <p>, <a href>, <ul>, <li>. Vietati <script>, <iframe>, attributi on*, link sospetti.
- Niente claim salutistici o nutrizionali.
- Produci due testi distinti, IT e EN, NON una traduzione meccanica: adatta i riferimenti culturali quando necessario.

OUTPUT (formatta esattamente cosi)
=== ITALIANO (HTML) ===
[testo HTML IT]

=== INGLESE (HTML) ===
[texto HTML EN]
PROMPT;
    }

    /**
     * Prompt per la sezione "Sentori e profumi".
     */
    private static function buildTastingPrompt(): string
    {
        return <<<'PROMPT'
Sei un sommelier copywriter.

COMPITO
Scrivi un descrittivo olfattivo e gustativo (40-70 parole IT, 40-70 parole EN) per la sezione "Sentori e profumi" della landing QR di un vino.

CONTESTO PRODOTTO (sostituisci i [...]):
- Nome vino: [es. Capitolare]
- Vitigno: [es. Pignolo]
- Annata: [es. 2018]
- Affinamento: [es. 24 mesi in legno]
- Note olfattive principali (se note): [es. frutti rossi maturi, spezie, cacao, tabacco]
- Note gustative principali (se note): [es. tannino fitto, acidita viva, finale lungo]

VINCOLI
- Lunghezza: 40-70 parole per lingua.
- Stile: descrittivo, evocativo ma sobrio. Niente claim soggettivi tipo "il piu aromatico".
- HTML sicuro: <strong>, <em>, <br>, <p>, <ul>, <li>. Le note di degustazione possono andare in lista <ul><li> con <strong>Olfatto</strong> / <strong>Bocca</strong>.
- Coerente con il vitigno e l'affinamento dichiarati: non inventare descrittori incompatibili.
- Niente claim salutistici.
- Produci due testi distinti IT e EN, non una traduzione meccanica.

OUTPUT (formatta esattamente cosi)
=== ITALIANO (HTML) ===
[testo HTML IT]

=== INGLESE (HTML) ===
[texto HTML EN]
PROMPT;
    }

    /**
     * Prompt per la sezione "Abbinamenti".
     */
    private static function buildPairingsPrompt(): string
    {
        return <<<'PROMPT'
Sei un food pairing expert.

COMPITO
Scrivi una sezione "Abbinamenti" (40-70 parole IT, 40-70 parole EN) per la landing QR di un vino.

CONTESTO PRODOTTO (sostituisci i [...]):
- Nome vino: [es. Capitolare]
- Vitigno e tipologia: [es. Pignolo, rosso strutturato]
- Profilo gustativo: [es. tannico, sapido, lungo]
- Cucina di riferimento (se rilevante): [es. friulana, italiana del nord-est]
- Eventuali abbinamenti gia testati e validati: [opzionale]

VINCOLI
- Suggerisci 3-5 abbinamenti CONCRETI: piatti specifici (es. "stinco di maiale al forno con polenta"), non solo categorie generiche ("carne rossa").
- Lista preferibilmente in <ul><li>.
- Tono pratico, senza superlativi.
- HTML sicuro come sopra.
- Niente claim salutistici.
- Produci IT e EN distinti, adattando i piatti alla cultura gastronomica della lingua (per EN puoi mantenere alcuni nomi italiani in corsivo se sono iconici, es. <em>risotto</em>).

OUTPUT (formatta esattamente cosi)
=== ITALIANO (HTML) ===
[testo HTML IT]

=== INGLESE (HTML) ===
[texto HTML EN]
PROMPT;
    }

    /**
     * Prompt per la sezione "Note di servizio".
     */
    private static function buildServicePrompt(): string
    {
        return <<<'PROMPT'
Sei un sommelier che redige schede tecniche di servizio.

COMPITO
Scrivi le "Note di servizio" (30-50 parole IT, 30-50 parole EN) per la landing QR di un vino.

CONTESTO PRODOTTO (sostituisci i [...]):
- Vitigno: [es. Pignolo]
- Tipologia: [es. rosso strutturato]
- Affinamento: [es. 24 mesi in legno + 12 mesi bottiglia]
- Eta / annata corrente: [es. 2018]

INDICAZIONI DA INCLUDERE (3-4 voci, ordine consigliato)
1. <strong>Temperatura di servizio</strong>: range in C (es. 16-18 C).
2. <strong>Calice</strong>: tipo consigliato (es. ballon ampio da rosso strutturato).
3. <strong>Decantazione</strong>: si/no, durata (es. decanter consigliato 30-45 min prima del servizio).
4. <strong>Apertura anticipata</strong>: tempo prima del servizio (se rilevante).

VINCOLI
- Formato lista <ul><li> con la voce iniziale in <strong>.
- Numeri e simboli: usare unita chiare (C, h, min). Per EN usa F tra parentesi se utile.
- Tono pratico, no marketing, no claim soggettivi ("massima espressione").
- HTML sicuro come sopra.
- Niente claim salutistici.
- Produci IT e EN distinti.

OUTPUT (formatta esattamente cosi)
=== ITALIANO (HTML) ===
[testo HTML IT]

=== INGLESE (HTML) ===
[texto HTML EN]
PROMPT;
    }
}

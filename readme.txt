=== FP QR Info ===
Contributors: franpass87
Tags: qr code, landing page, bilingual, product labels
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 0.1.19
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin per creare landing page standalone (senza tema) raggiungibili da QR code in etichetta.

== Description ==

FP QR Info permette di creare pagine pubbliche con URL a token (`/qr-info/{token}`) pensate per etichette prodotto.

Ogni landing include contenuti ITA/ENG per:
- Informazioni di smaltimento
- Informazioni nutrizionali
- Ingredienti
- (Opzionale) Storia del vino e dell’etichetta con immagine bottiglia e testi dedicati

Le pagine generate sono pubbliche ma impostate come non indicizzabili (`noindex`) solo per le route gestite dal plugin.

Interfaccia admin con menu `FP QR Info` e dashboard grafica allineata allo stile FP.

== Installation ==

1. Carica la cartella plugin in `wp-content/plugins/FP-QR-Info`.
2. Esegui `composer install` nella cartella plugin (se necessario).
3. Attiva il plugin da WordPress.
4. Crea una voce in `FP QR Info -> QR Landing`.

== Changelog ==

= 0.1.19 =
* Fix QR preview/download in admin: risposta immagine resa stabile anche con warning `Deprecated` su PHP 8.4.

= 0.1.18 =
* Palette blocchi smaltimento: plastica/capsula blu, bottiglia verde, tappo invariato.

= 0.1.17 =
* Blocchi smaltimento landing: aggiunta palette colore per Tappo/Bottiglia/Capsula (non più solo capsula evidenziata).

= 0.1.16 =
* Nutrizione vino: rimosso paragrafo esteso ridondante sui valori medi (resta solo la tabella valori).

= 0.1.15 =
* Ingredienti vino: rimossa anche la frase operativa su additivi/coadiuvanti; pulizia automatica dei contenuti legacy.

= 0.1.14 =
* Ingredienti vino: rimossa nota consulenziale su "contiene solfiti" dai testi predefiniti e dai contenuti legacy renderizzati.

= 0.1.13 =
* Nutrizione vino: inseriti valori già compilati nel preset (senza testo "sostituire i segni ..."), con normalizzazione automatica dei contenuti legacy.

= 0.1.12 =
* Sezione ingredienti vino: eliminata dicitura "Esempio", ora testo standard "Vino — dichiarazione ingredienti" anche per contenuti legacy già salvati.

= 0.1.11 =
* Aggiunto color picker "Colore accent landing" per ogni QR Landing, applicato al frontend (switch lingua e accenti UI).

= 0.1.10 =
* Fix switch lingua ITA/ENG nella landing: stile segmented control robusto e stato attivo/focus più leggibile.

= 0.1.9 =
* Menu admin: aggiunta voce Dashboard visibile sotto FP QR Info per accesso esplicito alla dashboard plugin.

= 0.1.8 =
* Schermata lista QR Landing: aggiunto banner FP in alto e tabella resa visivamente coerente con il design system.

= 0.1.7 =
* Editor CPT: aggiunto header FP in alto, metabox e azioni rapide resi visivamente coerenti al design system FP.

= 0.1.6 =
* Dashboard e CPT: grafica admin allineata al design system FP (banner, card, badge, bottoni, token colore).

= 0.1.5 =
* Blocchi smaltimento Tappo/Bottiglia/Capsula con codici e testi bilingue; esempio precompilato; fallback HTML unico.

= 0.1.4 =
* Modelli precompilati aggiornati con riferimenti normativi UE puntuali (FIC 1169/2011, vino 2021/2117, imballaggi 2018/852 e 97/129/CE) e diciture tabella nutrizionale conformi all’Allegato XV.

= 0.1.3 =
* Modelli UE (vino) con pulsanti in admin, tabella nutrizionale, ingredienti/allergeni; HTML sicuro e icone (simbolo riciclaggio Unicode + SVG vetro).

= 0.1.2 =
* Storia ed etichetta: immagine hero a tutto schermo, testi bilingua; selettore lingua aggiornato via JSON.

= 0.1.1 =
* Prima release operativa con route standalone, contenuti IT/EN, dashboard admin FP, copy URL e stampa etichetta QR.

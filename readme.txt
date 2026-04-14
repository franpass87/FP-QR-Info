=== FP QR Info ===
Contributors: franpass87
Tags: qr code, landing page, bilingual, product labels
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin per creare landing page standalone (senza tema) raggiungibili da QR code in etichetta.

== Description ==

FP QR Info permette di creare pagine pubbliche con URL a token (`/qr-info/{token}`) pensate per etichette prodotto.

Ogni landing include contenuti ITA/ENG per:
- Informazioni di smaltimento
- Informazioni nutrizionali
- Ingredienti

Le pagine generate sono pubbliche ma impostate come non indicizzabili (`noindex`) solo per le route gestite dal plugin.

Interfaccia admin con menu `FP QR Info` e dashboard grafica allineata allo stile FP.

== Installation ==

1. Carica la cartella plugin in `wp-content/plugins/FP-QR-Info`.
2. Esegui `composer install` nella cartella plugin (se necessario).
3. Attiva il plugin da WordPress.
4. Crea una voce in `FP QR Info -> QR Landing`.

== Changelog ==

= 0.1.1 =
* Prima release operativa con route standalone, contenuti IT/EN, dashboard admin FP, copy URL e stampa etichetta QR.

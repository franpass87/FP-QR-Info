=== FP QR Info ===
Contributors: franpass87
Tags: qr code, landing page, bilingual, product labels
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 0.1.35
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

= 0.1.35 = (2026-04-26)
* Fixed: parse error dashboard — stringhe UTF-8 corrotte in `AdminMenu.php` (etichetta Operatività e testo qualità stampa SVG). Fixed: `QrDownloadController.php` — rimosso duplicato parziale dopo la `}` di chiusura classe che generava "Unmatched ')'".

= 0.1.34 = (2026-04-24)
* Fixed: parse error "Unmatched ')' " in `src/Admin/QrDownloadController.php` e "unexpected identifier 'in'" in `src/Admin/AdminMenu.php` causato dallo stesso pattern di corruzione "duplica-coda" gia' visto nella v0.1.34/0.1.35 (poi rollbackate). Tronco manuale dei due file alla reale chiusura della classe (AdminMenu a 186 righe, QrDownloadController a 408 righe). Senza questo fix il parse error impediva il boot di WordPress (Uncaught Error al load del plugin) e di conseguenza bloccava anche l'invio di email da altri plugin (es. reminder FP Experiences, notifiche FP Forms Accrediti).

= 0.1.33 =
* Spaziature verticali tra box hero uniformate (header, immagine, storia) con ritmo costante.

= 0.1.32 =
* Link footer rinominato in "Vai al sito web" (EN: "Visit website").

= 0.1.31 =
* Box immagine bottiglia reso coerente alle altre card (proporzioni più compatte, meno spazio verticale e stile uniforme).

= 0.1.30 =
* Aggiunto in fondo alla landing il link alla home del sito (testo bilingue con switch lingua).

= 0.1.29 =
* Immagine bottiglia in hero inserita in un box/card coerente con gli altri contenitori.

= 0.1.28 =
* Headline principale delle sezioni resa in un box/card, allineata visivamente agli altri contenitori.

= 0.1.27 =
* Sezione "Storia ed etichetta" visualizzata in un box/card come gli altri elementi della landing.

= 0.1.26 =
* Headline sezione informazioni allineata alla stessa grandezza tipografica di "Storia ed etichetta".

= 0.1.25 =
* Hero landing in tema chiaro: sfondo sezione immagine bianco, senza dominante blu scuro.

= 0.1.24 =
* Headline sezioni spostata sotto "Storia ed etichetta"; in testata alta restano nome vino + switch lingua.

= 0.1.23 =
* Hero landing: testata (titolo + switch lingua) su sfondo bianco/card chiara, non più dark.

= 0.1.22 =
* Headline sezione resa dinamica per lingua: IT solo italiano, EN solo inglese (nessuna riga mista).

= 0.1.21 =
* Hero landing ottimizzata per bottiglia scontornata (PNG trasparente): immagine centrata e non ritagliata come background 16:9.

= 0.1.20 =
* Hero landing: titolo e switch lingua spostati in testa (overlay alto) quando è presente immagine full-screen.

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

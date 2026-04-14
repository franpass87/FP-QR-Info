## [0.1.28] - 2026-04-14
### Changed
- Headline “INFORMAZIONI DI SMALTIMENTO, NUTRIZIONALI E INGREDIENTI” resa in box/card dedicato (sfondo, bordo, raggio e padding) per coerenza con gli altri blocchi.

## [0.1.27] - 2026-04-14
### Changed
- Blocco “Storia ed etichetta” reso una card/box come le altre sezioni, anche nella versione hero con immagine bottiglia.
- Uniformata la resa visiva della story section (container bianco con bordo e raggio coerenti al resto della landing).

## [0.1.26] - 2026-04-14
### Changed
- Headline sezione “INFORMAZIONI DI SMALTIMENTO, NUTRIZIONALI E INGREDIENTI” allineata alla stessa dimensione tipografica di “Storia ed etichetta”.

## [0.1.25] - 2026-04-14
### Changed
- Hero landing aggiornata a tema chiaro: sfondo sezione immagine ora bianco (rimossa dominante blu/scura).
- Testi blocco “Storia ed etichetta” nella hero riallineati a colori scuri per coerenza su sfondo chiaro.

## [0.1.24] - 2026-04-14
### Changed
- Headline “INFORMAZIONI DI SMALTIMENTO, NUTRIZIONALI E INGREDIENTI” riposizionata sotto “Storia ed etichetta”.
- Nella testata alta restano solo nome vino e switch lingua; la headline sezioni è separata e contestuale ai blocchi informativi.

## [0.1.23] - 2026-04-14
### Changed
- Hero landing: sezione di testa (titolo + switch lingua) resa su sfondo bianco con card/header chiaro, al posto dell’aspetto scuro/trasparente precedente.

## [0.1.22] - 2026-04-14
### Changed
- Testo introduttivo sezioni reso un vero titolo di sezione (headline) sotto il nome vino.
- Headline ora bilingue dinamica: in IT mostra solo italiano, in EN mostra solo inglese (niente stringa mista IT/EN).

## [0.1.21] - 2026-04-14
### Changed
- Hero landing con immagine storia riprogettata per bottiglia scontornata: visual centrale con `object-fit: contain` (niente crop 16:9 di background).
- Header/top controls invariati in testa, con area immagine separata per valorizzare asset PNG trasparente.
- Testo aiuto admin aggiornato: consigliata immagine bottiglia scontornata (PNG trasparente).

## [0.1.20] - 2026-04-14
### Changed
- Landing con hero immagine: titolo principale e switch lingua spostati in testa all’hero (overlay top) invece che sotto la hero.
- Layout senza hero invariato: titolo e switch restano nell’header standard sopra le sezioni contenuto.

## [0.1.19] - 2026-04-14
### Fixed
- Endpoint QR (`admin-post.php?action=fp_qr_info_download`) reso robusto su PHP 8.4: soppressione locale dei `Deprecated` della libreria QR durante la generazione PNG/SVG.
- Risposte binarie QR (preview inline + download) ora puliscono l'output buffer prima degli header, evitando immagini corrotte/rotte in admin.

## [0.1.18] - 2026-04-14
### Changed
- Palette blocchi smaltimento aggiornata: **Capsula/Plastica** in blu, **Bottiglia/Vetro** in verde, **Tappo** invariato.

## [0.1.17] - 2026-04-14
### Changed
- Blocchi smaltimento in landing: introdotta palette colore coerente per tutte le card (`Tappo`, `Bottiglia`, `Capsula`) invece dell’evidenziazione singola della sola capsula.

## [0.1.16] - 2026-04-14
### Changed
- Sezione nutrizionale vino semplificata: rimosso il paragrafo testuale ridondante “Valori medi per vino…” perché i dati sono già presenti in tabella.
- Compatibilità contenuti legacy: eliminazione automatica frontend anche della frase estesa ridondante (IT/EN) se presente in landing già salvate.

## [0.1.15] - 2026-04-14
### Changed
- Sezione ingredienti vino ulteriormente semplificata per produzione: rimossa la frase operativa su additivi/coadiuvanti/ordine elenco.
- Compatibilità contenuti legacy: rimozione automatica frontend anche della frase “Ove siano utilizzati additivi…” (e equivalente EN) se presente in landing già salvate.

## [0.1.14] - 2026-04-14
### Changed
- Sezione ingredienti vino resa più “production-ready”: rimossa la nota consulenziale su «contiene solfiti» dai preset IT/EN.
- Compatibilità contenuti legacy: in frontend viene eliminata automaticamente la vecchia frase consulenziale se presente in landing già salvate.

## [0.1.13] - 2026-04-14
### Changed
- Preset nutrizionale vino aggiornato con valori già compilati per 100 ml (330 kJ / 79 kcal; grassi 0 g; saturi 0 g; carboidrati 2,6 g; zuccheri 0,6 g; proteine 0 g; sale 0,01 g).
- Rimossa la dicitura “Sostituire i segni …” dai testi nutrizionali, sostituita con formulazione diretta sui valori medi vino.
- Compatibilità contenuti già salvati: normalizzazione automatica frontend dei vecchi placeholder “…” e del vecchio testo istruzione.

## [0.1.12] - 2026-04-14
### Changed
- Sezione ingredienti vino: rimossa la dicitura “Esempio …” nei modelli predefiniti e sostituita con formulazione standard “Vino — dichiarazione ingredienti”.
- Compatibilità contenuti già salvati: in frontend viene normalizzata automaticamente la vecchia stringa legacy “Esempio (vino — da adattare al prodotto reale)” / “Example (wine — adapt to the actual product)”.

## [0.1.11] - 2026-04-14
### Added
- Nuovo campo admin **Colore accent landing** per ogni `QR Landing`, con color picker e fallback predefinito.
### Changed
- Landing frontend `/qr-info/{token}` ora usa il colore accent salvato nei metadati per switch lingua, titoli sezione e altri accenti UI.

## [0.1.10] - 2026-04-14
### Fixed
- Switch lingua ITA/ENG della landing (`/qr-info/{token}`): layout reso stabile e consistente come segmented control, evitando il rendering schiacciato/impilato su alcuni browser/device.
- Migliorati stati visuali/accessibilità dello switch (dimensioni minime, focus visibile, stato attivo con contrasto migliore).

## [0.1.9] - 2026-04-14
### Changed
- Menu admin: aggiunta voce submenu esplicita **Dashboard** sotto `FP QR Info` per rendere visibile e raggiungibile la pagina dashboard.
- Allineata l’etichetta della voce dashboard nel submenu (`Dashboard`) per evitare ambiguità tra dashboard e lista `QR Landing`.

## [0.1.8] - 2026-04-14
### Changed
- Schermata lista `QR Landing` (`edit.php?post_type=fp_qr_landing`) allineata al design system FP con banner header dedicato (titolo, descrizione, badge versione).
- Rifinitura visuale tabella elenco landing (bordi/ombra/header righe) per coerenza con card e componenti admin FP.

## [0.1.7] - 2026-04-14
### Changed
- Editor del CPT `fp_qr_landing`: aggiunto header in stile FP sopra le metabox (banner gradiente, descrizione e badge versione) per rendere la schermata coerente con il design system anche fuori dalla dashboard.
- Restyling visivo della schermata editor: metabox con gerarchia card più evidente, azioni rapide con pulsante primario gradiente e dettagli UI più riconoscibili in stile FP.

## [0.1.6] - 2026-04-14
### Changed
- Admin dashboard e schermata CPT: UI allineata al **FP Admin UI Design System** (token `--fpdms-*`, banner gradiente standard FP Mail, card con header/body, badge, status pill, bottoni `fpqri-btn*`), classe body `fpqri-admin-shell`, enqueue CSS con fallback `$_GET['page']`.

## [0.1.5] - 2026-04-14
### Added
- Smaltimento a **blocchi** Tappo, Bottiglia e Capsula (codice materiale + testi IT/EN), griglia responsive sulla landing e pulsante «Inserisci esempio» (FOR 51, GL 70, C/PVC 90).
- Retrocompatibilità: se tutti i blocchi sono vuoti si usa l’HTML unico in `<details>`; titolo sezione «Etichetta ambientale / Imballaggi».

## [0.1.4] - 2026-04-14
### Changed
- Modelli legali allineati a riferimenti espliciti: Reg. (UE) 1169/2011 (FIC, artt. 18, 21, 30–34, Allegato XV), Reg. (UE) 2021/2117 (vino), reg. delegato (UE) 2023/1606, Direttiva (UE) 2018/852, Decisione 97/129/CE; tabella nutrizionale con diciture Allegato XV (IT/EN), nota sale art. 30(5), ordine energetico kJ prima dei kcal (art. 33).
- Smaltimento: testi corretti su codifica materiali e limite del simbolo Unicode riciclaggio; icone con `alt` bilingue; rimossi simboli non normativi (emoji) dagli ingredienti.

## [0.1.3] - 2026-04-14
### Added
- Modelli normativi UE (vino) per smaltimento, dichiarazione nutrizionale (tabella Allegato XV) e ingredienti/allergeni, inseribili da admin con pulsante dedicato.
- Icona vetro (SVG plugin) e simbolo riciclaggio Unicode U+267B nei modelli smaltimento; avvertenza legale in metabox.
### Changed
- Campi smaltimento/nutrizionali/ingredienti salvati con `wp_kses_post` e resi in landing come HTML sicuro (tabella, grassetto, immagini); selettore lingua aggiorna il markup via `innerHTML`.

## [0.1.2] - 2026-04-14
### Added
- Sezione "Storia ed etichetta" sulla landing: immagine bottiglia a tutto schermo (hero), testi IT/EN e media picker in admin.
- Cambio lingua basato su payload JSON (testi lunghi e newline senza limiti degli attributi `data-*`).

## [0.1.1] - 2026-04-14
### Added
- Prima versione del plugin `FP QR Info` con CPT dedicato per landing QR.
- Route standalone `qr-info/{token}` con rendering senza tema.
- Contenuti bilingua IT/EN per smaltimento, nutrizionali e ingredienti.
- Impostazione `noindex, nofollow, noarchive` solo sulle landing generate dal plugin.
- Download QR code in PNG/SVG dalla lista admin del CPT.
- Metabox laterale con anteprima QR e azioni rapide di download.
- Validazione token con controllo univocita tra tutte le landing.
- Menu admin dedicato `FP QR Info` (posizione area FP) con dashboard in stile grafico FP.
- Bottone copia URL e pagina stampa etichetta dal pannello landing.

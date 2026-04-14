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

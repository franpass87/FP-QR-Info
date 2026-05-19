## [0.6.0] - 2026-05-19

### Added

- **"Guida AI" per generare i testi della landing**: nuovo pulsante nell'header del CPT editor (`FP QR Info → modifica landing`) che apre un modale con linee guida editoriali e prompt copiabili separati per ogni campo.
- **`AiGuide`** (`src/Admin/AiGuide.php`): contenitore statico delle linee guida + 4 prompt template (Storia, Sentori e profumi, Abbinamenti, Note di servizio). Tutto localizzabile via `__()`.
- **Linee guida generali**: lingue IT+EN obbligatorie, HTML sicuro consentito/vietato (con tag specifici), tono editoriale, divieto claim salutistici (Reg. UE 1924/2006), revisione editoriale obbligatoria, adattabilità ad altri prodotti.
- **Prompt-template per VINO**: 4 prompt curati con CONTESTO PRODOTTO (campi da compilare), VINCOLI (lunghezza, tono, HTML, claim) e formato OUTPUT IT/EN. Ogni prompt ha bottone "Copia prompt" con feedback visivo.
- **Avvertenza esplicita**: smaltimento, nutrizionali e ingredienti **non** vanno generati con AI (sono campi normativi: usa i modelli precompilati esistenti).

### Changed

- **`LandingCpt::renderEditorHeader`**: aggiunto wrapper `.fpqri-editor-header-actions` che raggruppa nuovo pulsante "Guida AI" + badge versione esistente. Il CSS legacy `.fpqri-editor-header { display: flex; justify-content: space-between; }` continua a funzionare invariato.

### Notes

- Il modale è 100% client-side: HTML inline, CSS inline (scoped), JS inline (vanilla, no dipendenze). Z-index 100050 (sotto admin bar 99999 + buffer).
- A11y: `role="dialog"`, `aria-modal="true"`, `aria-labelledby`, focus management (apertura → primo close button, chiusura → bottone che ha aperto), chiusura via X / overlay click / `Escape`.
- Copy-to-clipboard: usa `navigator.clipboard.writeText` con fallback `document.execCommand('copy')` per browser legacy.
- Sicurezza: i prompt sono stringhe statiche curate; le linee guida con HTML inline passano da `wp_kses` con whitelist ridotta (`<strong>`, `<em>`, `<code>`, `<br>`); i prompt vanno in `<textarea readonly>` via `esc_textarea`.

## [0.5.0] - 2026-05-19

### Added

- **Icone SVG per le card della scheda prodotto**: ogni sotto-blocco (Sentori, Abbinamenti, Servizio) può ora avere un'iconcina SVG accanto al titolo, scelta da un set curato di 13 icone in stile lineare (`stroke="currentColor"`, ereditano l'accent color della landing).
- **`SectionIconRegistry`** (`src/Content/SectionIconRegistry.php`): registry centralizzato delle icone disponibili (slug, label IT/EN, emoji preview, SVG inline). Set: `wine-glass`, `grape`, `leaf`, `flower`, `sparkles`, `cutlery`, `chef-hat`, `cheese`, `bread`, `thermometer`, `snowflake`, `clock`, `decanter`, più `none` (default).
- **UI admin**: dropdown `<select>` "Icona accanto al titolo" in ogni fieldset prodotto, con preview SVG live che si aggiorna al cambio (no salvataggio richiesto).
- **3 nuove meta**: `fp_qr_info_tasting_icon`, `fp_qr_info_pairings_icon`, `fp_qr_info_service_icon` (sanitizzate via `sanitize_key` + whitelist registry, default `none`).

### Changed

- **Markup card prodotto**: il titolo `<h2 class="fpqi-section-title">` ora avvolge il testo in `<span class="fpqi-section-title-text">` quando ha un'icona, così il JS di switch lingua aggiorna solo il testo senza sovrascrivere l'SVG. Le card senza icona restano invariate.
- **JS `applyLang` per `productSections`**: ora cerca prima `.fpqi-section-title-text`, poi fallback a `.fpqi-section-title`. Sezioni legali invariate.
- **CSS**: nuove regole `.fpqi-section-icon` (22x22, currentColor, inline-flex) e `.fpqi-section-title--with-icon` (gap 8px tra icona e testo).

### Notes

- L'output SVG inline NON passa da `wp_kses_post` perché le stringhe sono statiche e curate nel registry. Non inserire mai input utente in `SectionIconRegistry`.
- Le sezioni legali (smaltimento, nutrizionali, ingredienti) e la storia non hanno (ancora) supporto icone: scelta perimetrale richiesta dall'utente.

## [0.4.0] - 2026-05-19

### Added

- **Nuova sezione "Scheda prodotto"** subito sotto la storia, con 3 sotto-blocchi indipendenti, bilingue (IT/EN) e con HTML sicuro (`wp_kses_post`):
  - **Sentori e profumi** (Aromas & tasting notes)
  - **Abbinamenti** (Food pairings)
  - **Note di servizio** (Serving notes)
- **Toggle dedicati** per ciascun sotto-blocco (`fp_qr_info_enable_tasting`, `fp_qr_info_enable_pairings`, `fp_qr_info_enable_service`). Default: **OFF**.
- **Headline divider** "SCHEDA PRODOTTO" / "PRODUCT SHEET" mostrata solo se almeno uno dei 3 sotto-blocchi è attivo.
- **Switch lingua client-side** esteso per gestire `productSections` + `productHeadline` (selettore `data-product-section-id`, id `fpqi-product-section-title`).
- **9 nuove meta key**: `fp_qr_info_enable_{tasting,pairings,service}`, `fp_qr_info_{tasting,pairings,service}_{it,en}`.

### Changed

- **`LandingRouter::isLegalSectionEnabled` → `isSectionToggleEnabled`**: rinominato (privato, callsite tutti interni) per coerenza semantica, ora usato anche per i toggle della scheda prodotto.
- **Markup landing**: ordine = `titolo+lang → storia → SCHEDA PRODOTTO (se attiva) → INFORMAZIONI legali (se attive) → footer`.

## [0.3.1] - 2026-05-18

### Fixed

- **Ordine di rendering landing in modalità card** (nessuna immagine bottiglia caricata): il blocco "Storia ed etichetta" veniva mostrato PRIMA del titolo del vino + switch lingua. Ora la sequenza è coerente con la modalità hero: **titolo vino + switch lingua → storia → eventuali sezioni legali**. La modalità hero (con immagine) è invariata: titolo + lang in alto, immagine al centro, storia sotto.

## [0.3.0] - 2026-05-18

### Changed

- **Default sezioni legali → OFF**: i toggle `Mostra sezione smaltimento`, `Mostra sezione nutrizionale` e `Mostra sezione ingredienti` partono ora **disattivati** per le nuove landing. Per attivarli è necessario flaggarli esplicitamente nel metabox CPT.
- **Comportamento `isSectionEnabled` (admin) e `isLegalSectionEnabled` (frontend)**: ora restituiscono `false` quando il meta è vuoto (prima `true` per retrocompatibilità).

### Added

- **Migrazione one-shot `maybeMigrateLegalDefaults`** (gated da opzione `fp_qr_info_legal_defaults_migrated_v1`): al primo `init` dopo l'aggiornamento, scrive esplicitamente `'1'` su tutte le landing pre-esistenti che non hanno ancora un valore salvato per i 3 toggle. Le landing già pubblicate mantengono così il comportamento attuale (sezioni visibili). Il flag impedisce ri-esecuzioni successive.

## [0.2.0] - 2026-05-18

### Added

- **HTML sicuro nella sezione "Storia ed etichetta"**: i campi `Storia (Italiano)` e `Story (English)` accettano ora HTML sicuro (`wp_kses_post`) coerente con le altre sezioni della landing (smaltimento, nutrizionali, ingredienti). Tag consentiti tipici: `<strong>`, `<em>`, `<a>`, `<br>`, `<p>`, `<ul>`, `<ol>`, `<li>`.
- **Stili dedicati per body story**: nuovo selettore `.fpqi-story-body` con regole per paragrafi, link e liste in entrambe le modalità (hero con immagine + card senza immagine).

### Changed

- **Salvataggio meta `fp_qr_info_story_it/_en`**: passa da `sanitize_textarea_field` (testo puro) a `wp_kses_post` (HTML filtrato). I contenuti già salvati come testo puro restano renderizzati identici grazie a `white-space: pre-wrap`.
- **Render frontend body story** (`LandingRouter.php`): da `<p>` con `esc_html` a `<div class="fpqi-story-body">` con `wp_kses_post`, sia in modalità hero (con immagine bottiglia) che card. Il titolo "Storia ed etichetta" resta `esc_html` (stringa fissa traducibile).
- **Switch lingua client-side**: il body story ora usa `innerHTML` (HTML già sanitizzato lato server prima del JSON encoding con `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT`); il titolo resta `textContent`.
- **Description metabox CPT**: aggiornata per indicare all'editor i tag HTML sicuri consentiti.

## [0.1.41] - 2026-05-15

### Fixed

- **Hardening download QR PNG/SVG**: sanitizzato il token usato nel filename di download e aggiunti header `X-Content-Type-Options: nosniff` e `Cache-Control` per rendere piu robusta la consegna binaria durante stampa/scarico etichette.

## [0.1.40] - 2026-05-15

### Added

- **Sezioni legali opzionali per landing**: aggiunti toggle in admin per abilitare/disabilitare in modo indipendente i blocchi `Smaltimento`, `Nutrizionali` e `Ingredienti` per ogni `QR Landing`.

### Changed

- **Frontend `/qr-info/{token}`**: le sezioni legali vengono renderizzate solo se abilitate; default retrocompatibile su landing esistenti (se toggle non ancora salvati, le sezioni restano attive).

## [0.1.39] - 2026-05-15

### Added

- **Guardrail permanenti anti-regressione parse error**: introdotto script `scripts/verify-integrity.php` che esegue lint PHP su tutti i file plugin (escluso `vendor`) e controlli mirati di integrita su `AdminMenu.php` e `QrDownloadController.php` per intercettare il pattern "duplica-coda".
- **CI automatica**: aggiunto workflow GitHub `.github/workflows/integrity-check.yml` che lancia il controllo integrita ad ogni push/pull request.

### Changed

- **Runtime warning di stabilita**: il plugin mostra un avviso in admin quando rileva che gira da percorso cloud sincronizzato (es. OneDrive/junction), scenario in cui si sono verificati i parse error ciclici al boot.

## [0.1.38] - 2026-05-02

### Fixed

- **`AdminMenu.php`** e **`QrDownloadController.php`**: rimossa di nuovo la **coda duplicata parziale** dopo la `}` di chiusura della classe (stesso pattern già documentato in 0.1.34–0.1.37), che causava **parse error** al boot (`unexpected identifier "in"` / `Unmatched ')'`) e **HTTP 500** su tutto WordPress in locale.

## [0.1.37] - 2026-04-30

### Fixed

- **`QrDownloadController.php`**: ripristinata la chiusura corretta della classe (rimosso duplicato/corruzione introdotta per errore nello stesso push della 0.1.36).

## [0.1.36] - 2026-04-30

### Fixed

- **`AdminMenu.php`**: rimosso di nuovo un **frammento duplicato parziale** dopo la `}` di chiusura della classe (stesso pattern «duplica-coda»: testo spezzato `to in ITA…` fuori stringa), che causava **parse error** e **errore critico** WordPress al boot del plugin.

## [0.1.35] - 2026-04-26

### Fixed

- **`AdminMenu.php`**: stringhe della dashboard con sequenze UTF-8 corrotte (`Operatività`, `qualità`) producevano ancora parse error **"unexpected identifier 'in'"** (PHP interpretava male il sorgente). Sostituite con testo UTF-8 valido.
- **`QrDownloadController.php`**: rimosso un **frammento duplicato parziale** dopo la chiusura reale della classe (`is_string($previousDisplayErrors)) {` … seconda copia di `prepareBinaryResponse`) che causava **"Unmatched ')'"** al parse.

## [0.1.34] - 2026-04-24

### Fixed

- **Parse error di boot bloccante in `src/Admin/AdminMenu.php` (riga 187) e `src/Admin/QrDownloadController.php` (riga 409)** causato dallo stesso pattern di corruzione "duplica-coda" già riscontrato nelle 0.1.34/0.1.35 pre-rollback: un tool esterno al repo ha appeso in fondo ai due file una copia parziale delle ultime righe, tagliando a metà le prime e spezzando la sintassi ("unexpected identifier 'in'" e "Unmatched ')' "). Senza fix, `require` del plugin generava `Uncaught Error` al boot di WordPress: tutto il sito andava in "errore critico" (bianco), bloccando in cascata l'invio di email da altri plugin (es. reminder QR di FP Experiences, notifiche FP Forms Accrediti). Troncati manualmente i due file al terminatore reale delle classi.
- Sconsigliato qualunque tool di sync/auto-format che produce questo pattern finché la causa upstream non è identificata.

## [0.1.33] - 2026-04-14
### Changed
- Spaziatura verticale tra i box hero uniformata: rimossi i comportamenti `100vh/space-between` che creavano gap irregolari.
- Header, box immagine e box “Storia ed etichetta” ora seguono ritmo costante con padding coerenti tra i blocchi.

## [0.1.32] - 2026-04-14
### Changed
- Etichetta footer link aggiornata da “Torna alla home” a formulazione più neutra “Vai al sito web” (EN: “Visit website”).

## [0.1.31] - 2026-04-14
### Changed
- Box immagine bottiglia riallineato allo stile delle altre card: dimensioni più compatte, layout centrato a larghezza contenuto, rimosso effetto eccessivo di card “fuori scala”.
- Ridotta l’impronta visiva della hero image area (meno spazio verticale e ombra più leggera) per coerenza con i blocchi informativi.

## [0.1.30] - 2026-04-14
### Added
- Footer in fondo alla landing con link alla home del sito.
### Changed
- Etichetta link home localizzata e sincronizzata con switch lingua (IT/EN).

## [0.1.29] - 2026-04-14
### Changed
- Immagine bottiglia nella hero inserita in un box/card dedicato (sfondo, bordo, raggio, ombra) per coerenza con gli altri contenitori della landing.

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

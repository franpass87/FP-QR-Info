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

# FP-QR-Info

Plugin WordPress per creare landing page standalone (senza tema) dedicate ai QR code su etichetta prodotto.

## Funzionalita

- CPT admin dedicato: `QR Landing`.
- URL pubblica con token: `https://example.com/qr-info/{token}`.
- Rendering standalone con HTML/CSS inline (bypass tema).
- `noindex, nofollow, noarchive` applicato solo alle landing del plugin.
- Token con controllo univocita (no collisioni tra landing).
- Contenuti bilingua IT/EN:
  - Informazioni di smaltimento / Disposal info
  - Informazioni nutrizionali / Nutritional info
  - Ingredienti / Ingredients
- Opzionale: storia del vino e dell’etichetta con immagine bottiglia a tutto schermo (hero) e testi IT/EN; senza immagine resta un blocco testuale in evidenza.
- Modelli precompilati con riferimenti normativi UE citati nel testo (FIC 1169/2011, vino 2021/2117, imballaggi 2018/852 e Decisione 97/129/CE), tabella nutrizionale in ordine Allegato XV e nota sale (art. 30(5)); pulsanti in admin; simbolo riciclaggio Unicode U+267B e icona vetro illustrativa in SVG.
- Preset nutrizionale vino con valori medi già compilati per 100 ml (kJ/kcal, grassi, saturi, carboidrati, zuccheri, proteine, sale).
- Smaltimento a tre blocchi (Tappo, Bottiglia, Capsula) con codice materiale e testi IT/EN, griglia su mobile/desktop; esempio FOR 51 / GL 70 / C/PVC 90; HTML unico opzionale se i blocchi sono vuoti.
- Colore accent personalizzabile per singola landing (color picker in admin) applicato al frontend su switch lingua e dettagli grafici.
- Download QR code PNG/SVG dalla schermata admin.
- Anteprima QR nella sidebar della singola landing.
- Dashboard admin `FP QR Info` con grafica allineata al design system FP.
- Bottone copia URL e stampa etichetta pronta da admin.

## Requisiti

- WordPress 6.0+
- PHP 8.0+

## Installazione sviluppo

1. Clona il repository.
2. Esegui:

```bash
composer install
```

3. Copia/collega la cartella plugin in `wp-content/plugins/FP-QR-Info`.
4. Attiva il plugin in WordPress.

## Autore

**Francesco Passeri**
- Sito: [francescopasseri.com](https://francescopasseri.com)
- Email: [info@francescopasseri.com](mailto:info@francescopasseri.com)
- GitHub: [github.com/franpass87](https://github.com/franpass87)
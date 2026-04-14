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
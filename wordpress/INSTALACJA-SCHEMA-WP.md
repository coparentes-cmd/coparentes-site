# Schema JSON-LD na WordPress (coparentes.ai)

Wtyczka MU wstawia wizytówkę schema.org (`Organization`, `WebSite`, `WebApplication`) do `<head>`.  
**Nie zmienia copy, layoutu ani treści stron.**

## 1. Wgraj plik (File Manager)

1. Hostinger → **coparentes.ai** → **Tools** → **File Manager**
2. Wejdź do `public_html/wp-content/mu-plugins/`
3. **New file** lub Upload: `coparentes-schema.php`
4. Docelowa ścieżka:
   ```
   public_html/wp-content/mu-plugins/coparentes-schema.php
   ```

## 2. Wyczyść cache

LiteSpeed Cache → **Purge All**

## 3. Test

1. Otwórz https://coparentes.ai/ (źródło strony / View Source)
2. Szukaj `application/ld+json` oraz `"@type":"Organization"`
3. Strona ładuje się normalnie (bez błędów)

## Rollback

Usuń `wp-content/mu-plugins/coparentes-schema.php` i wyczyść cache.

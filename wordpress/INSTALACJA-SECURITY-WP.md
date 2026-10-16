# Security hardening na WordPress (coparentes.ai)

Wtyczka MU: nagłówki HTTP + ukrycie publicznych użytkowników.  
**Nie zmienia copy, layoutu ani treści stron.**

## 1. Wgraj plik (File Manager)

1. Hostinger → **coparentes.ai** → **Tools** → **File Manager**
2. Wejdź do `public_html/wp-content/`
3. Jeśli nie ma folderu `mu-plugins`, utwórz go (**New folder** → `mu-plugins`)
4. Wejdź do `mu-plugins`
5. **Upload** pliku z repozytorium:
   ```
   wordpress/coparentes-security.php
   ```
   docelowo:
   ```
   public_html/wp-content/mu-plugins/coparentes-security.php
   ```

Wtyczki MU ładują się automatycznie — bez aktywacji w panelu WP.

## 2. Wyczyść cache

Hostinger / LiteSpeed Cache → **Purge All** (albo WP Admin → LiteSpeed Cache → Purge All).

## 3. Szybki test (po wgraniu)

W terminalu lub przeglądarce:

| Sprawdzenie | Oczekiwany wynik |
|-------------|------------------|
| `https://coparentes.ai/` ładuje się | strona OK, bez błędów |
| Nagłówki odpowiedzi | są `Strict-Transport-Security`, `X-Content-Type-Options` |
| `https://coparentes.ai/wp-json/wp/v2/users` | brak publicznej listy użytkowników |
| `https://coparentes.ai/?author=1` | przekierowanie na stronę główną |
| WP Admin / edycja stron | działa normalnie |

## Co robi wtyczka

- Dodaje: HSTS, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`
- Usuwa tag `generator` (wersja WP) i stara się usunąć `X-Powered-By`
- Blokuje publiczne `/wp-json/wp/v2/users` oraz archiwa `?author=` / `/author/...`

## Rollback

Usuń plik `wp-content/mu-plugins/coparentes-security.php` i wyczyść cache.

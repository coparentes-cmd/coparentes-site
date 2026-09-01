# Microsoft Clarity na WordPress (coparentes.ai)

## 1. Wgraj wtyczkę

W Hostingerze (File Manager lub FTP):

1. Wejdź do `public_html/wp-content/`
2. Jeśli nie ma folderu `mu-plugins`, utwórz go
3. Skopiuj plik `wordpress/coparentes-clarity.php` z tego repozytorium do:
   ```
   public_html/wp-content/mu-plugins/coparentes-clarity.php
   ```

Wtyczki MU ładują się automatycznie — nie trzeba ich aktywować w panelu WP.

## 2. Utwórz projekt w Clarity

1. Wejdź na https://clarity.microsoft.com/
2. **Add new project** → domena `coparentes.ai`
3. Skopiuj **Project ID**

## 3. Wpisz Project ID w WordPress

1. Zaloguj się do `https://coparentes.ai/wp-admin`
2. **Ustawienia → Microsoft Clarity**
3. Wklej Project ID → **Zapisz**

## 4. Wyczyść cache

Hostinger → LiteSpeed Cache → **Purge All**

## 5. Sprawdź działanie

1. Otwórz stronę w trybie incognito
2. Zaakceptuj cookies analityczne (lub „Akceptuj wszystkie”)
3. W panelu Clarity: **Settings → Setup** → status **Active** (może potrwać kilka minut)

## RODO

Skrypt ładuje się dopiero po zgodzie na cookies analityczne (`data-cookie-category="analytics"`), zgodnie z banerem cookies motywu Coparentes.

## Uwaga o hybrydowej stronie

Clarity z tej wtyczki działa na stronach renderowanych przez WordPress. Stare statyczne pliki (`/blog/index.html`, `/en/index.html` itd.) nie są objęte, dopóki leżą na serwerze obok WP.

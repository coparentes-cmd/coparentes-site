# Coparentes — motyw WordPress (opcja A, wygląd 1:1)

Statyczna strona HTML **zostaje w repozytorium** (katalog główny).  
Motyw WP leży w `wordpress/coparentes/` — te same CSS, JS, grafiki i treści.

## Co jest w środku

- Landing PL 1:1 + landings EN/DE/ES/FR/ZH
- Blog (wpisy, okładki, kategorie, filtry)
- Polityka / regulamin (PL + tłumaczenia)
- Formularz Kontakt (REST → `wp_mail`)
- Komentarze bloga (moderacja w WP)
- MailerLite, cookies, assety bez zmian

## Instalacja (Hostinger) — Twoje kroki

1. Zainstaluj WordPress w hPanel.
2. Pobierz zip motywu: `wordpress/coparentes-theme.zip` (z tej gałęzi / PR).
3. W WP: **Wygląd → Motywy → Dodaj nowy → Wgraj motyw → Aktywuj**.
4. Po aktywacji motyw **sam wgrywa treści** (strona główna, blog, artykuły, prawne, języki).
5. **Ustawienia → Bezpośrednie odnośniki** → ustaw „Nazwa wpisu” → Zapisz.
6. **Ustawienia → Ogólne** → opcjonalnie pole **Coparentes — e-mail kontaktowy** (`kontakt@coparentes.ai`).
7. Sprawdź:
   - `/` landing
   - `/blog/` lista
   - artykuł + komentarz
   - stopka → Kontakt
   - `/en/` itd.

## Ponowny seed

**Narzędzia → Coparentes seed** — doda brakujące treści (nie nadpisuje istniejących slugów).

## Moderacja komentarzy

**Komentarze** w panelu WP (jak wcześniej `api/admin.php`).

## Edycja tekstów (cała strona)

1. **Strona główna (landing PL):** **Strony → Start** → zmień teksty → **Aktualizuj**
2. **Landingi EN/DE/ES/FR/ZH:** **Strony → en / de / …**
3. **Blog:** **Wpisy**
4. **Polityka / regulamin:** **Strony**

Landing jest w klasycznym edytorze (cały HTML strony). Zmieniaj napisy między znacznikami; nie usuwaj klas CSS ani `{{THEME_URI}}` przy obrazkach.

Po aktualizacji motywu do 1.1.0 wejdź raz w panel admina — treści landingu zsynchronizują się do stron automatycznie.  
Albo: **Narzędzia → Coparentes seed → Wgraj ponownie teksty landingu do stron**.

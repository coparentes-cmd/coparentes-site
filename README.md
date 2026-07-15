# coparentes – Landing Page

> Spokojne rodzicielstwo po rozstaniu. Aplikacja mobilna dla rodziców.

## 🎯 Cel projektu

Landing page aplikacji mobilnej **coparentes** — pomagającej rodzicom po rozstaniu ogarniać wspólne rodzicielstwo: komunikację, planowanie i rozliczenia, bez konfliktów.

---

## ✅ Zrealizowane sekcje

| # | Sekcja | Status |
|---|--------|--------|
| 1 | **Hero** – nagłówek, CTA, mockupy telefonów | ✅ |
| 2 | **Emotional** – empatyczny blok tekstowy | ✅ |
| 3 | **Features** – 3 kolumny: Rozmowy, Plan, Finanse | ✅ |
| 4 | **Komunikacja** – porównanie wiadomości (ostra vs spokojna) | ✅ |
| 5 | **Plan dnia** – interaktywny UI kalendarza | ✅ |
| 6 | **Finanse** – mockup rozliczeń z wydatkami | ✅ |
| 7 | **Dziecko** – sekcja o spokoju dziecka | ✅ |
| 8 | **Delikatne AI** – wsparcie przy pisaniu | ✅ |
| 9 | **Opinie** – 3 karty z recenzjami | ✅ |
| 10 | **App Preview** – Dla mamy / taty / dziecka | ✅ |
| 11 | **Follow-up** – następne kroki po rozmowach, terminach i zadaniach | ✅ |
| 12 | **Final CTA** – pobierz aplikację | ✅ |
| – | **Navbar** – logo + linki + mobile toggle | ✅ |
| – | **Footer** – linki, social, prawne | ✅ |

---

## 🗂 Struktura plików

```
index.html          ← główna strona (single-page)
css/
  style.css         ← pełny design system
js/
  main.js           ← JavaScript: animacje, interakcje
README.md
```

---

## 🎨 Design System

| Token | Wartość |
|-------|---------|
| Primary (green) | `#5FBF8F` |
| Secondary (blue) | `#6FA8DC` |
| Background | `#FFFFFF` |
| Text | `#1A1A1A` |
| Subtext | `#6B6B6B` |
| Border radius | `20px / 28px` |
| Font | Inter (Google Fonts) |

---

## ⚙️ Funkcje JavaScript

- ✅ Navbar scroll effect (glassmorphism po scroll)
- ✅ Mobile hamburger menu z animacją
- ✅ Fade-in animacje (IntersectionObserver)
- ✅ Staggered wejście kart (0.1s delay każda)
- ✅ Smooth scroll z offsetem navbaru
- ✅ Aktywny link w nawigacji
- ✅ Float animation mockupów telefonów
- ✅ Pulsowanie serduszek (sekcja dziecka)
- ✅ Counter animacja (saldo finansów)
- ✅ Hover parallax kart recenzji
- ✅ Hover animacje ikon feature
- ✅ Ripple effect na przyciskach
- ✅ Scroll progress bar (top of page)

---

## 📱 Responsywność

- **Desktop** (>1024px): pełny layout 2-kolumnowy
- **Tablet** (768–1024px): 2-kolumnowe gridy, ukryte boczne telefony
- **Mobile** (<768px): stack layout, hamburger menu, uproszczone mockupy

---

## 🚀 Entry Points

| Ścieżka | Opis |
|---------|------|
| `/` (index.html) | Główna strona landing page |
| `#features` | Funkcje aplikacji |
| `#how-it-works` | Jak działa Coparentes |
| `#follow-up` | Następne kroki po rozmowach, terminach i zadaniach |
| `#audience` | Dla kogo jest Coparentes |
| `#testimonials` | Opinie rodziców |
| `#download` | Final CTA / download |

---

## 🌍 Wersje językowe

| Język | Ścieżka | Status |
|-------|---------|--------|
| 🇵🇱 Polski | `/index.html` | ✅ |
| 🇬🇧 English | `/en/index.html` | ✅ |
| 🇩🇪 Deutsch | `/de/index.html` | ✅ |
| 🇪🇸 Español | `/es/index.html` | ✅ |
| 🇫🇷 Français | `/fr/index.html` | ✅ |
| 🇨🇳 中文 | `/zh/index.html` | ✅ |

Każda wersja posiada pełny przełącznik języka (language switcher) w navbarze.
Język chiński używa czcionki **Noto Sans SC** (Google Fonts).

---

## 📌 Planowane rozszerzenia

- [ ] Wersja EN (angielska)
- [ ] A/B test bardziej emocjonalnego hero copy
- [ ] Animowany onboarding demo (video/GIF)
- [ ] Blog / artykuły o co-parentingu
- [ ] Formularz pre-launch (zbieranie emaili)
- [ ] Analytics (Plausible / Google)
- [ ] Integracja prawdziwych linków App Store / Google Play
- [ ] Strona /polityka-prywatnosci
- [ ] Strona /regulamin

---

## 🛠 Tech Stack

- HTML5 semantyczny
- CSS3 (custom properties, grid, flexbox, animations)
- JavaScript ES6+ (vanilla, bez frameworków)
- Google Fonts (Inter)
- Font Awesome 6 (ikony)
- Bez zewnętrznych dependencies JS

---

*Zrobione z ❤️ dla spokojnych rodzin.*


## Assets version
This package includes an `assets/` directory with external SVG files for logos, icons, decorative graphics and avatars, so you can replace visuals without editing HTML.

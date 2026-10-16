<?php
/**
 * Plugin Name: Coparentes Schema JSON-LD
 * Description: Wstawia wizytówkę schema.org (Organization, WebSite, WebApplication) do &lt;head&gt;. Nie zmienia copy ani layoutu.
 * Version: 1.0.0
 * Author: Coparentes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Print JSON-LD graph in the document head (front-end only).
 */
add_action('wp_head', static function () {
    if (is_admin()) {
        return;
    }

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => 'https://coparentes.ai/#organizacja',
            'name' => 'Coparentes',
            'url' => 'https://coparentes.ai/',
            'description' => 'Jesteśmy zespołem, który od lat towarzyszy rodzinom w mediacjach, finansach i organizacji codzienności. Pomysłodawczynią Coparentes jest doświadczona mediatorka rodzinna, która wie, że prawdziwa praca zaczyna się dopiero po rozstaniu. Tworzymy narzędzie, które pomaga dwóm domom spokojnie współpracować dla dobra dziecka. Chcemy wesprzeć 100 tysięcy rodziców w nowej rzeczywistości.',
            'slogan' => 'Spokojne rodzicielstwo po rozstaniu',
            'knowsAbout' => [
                'współrodzicielstwo',
                'mediacje rodzinne',
                'opieka naprzemienna',
                'organizacja życia po rozwodzie',
            ],
            'sameAs' => [
                'https://www.facebook.com/profile.php?id=61589611806475',
                'https://www.linkedin.com/company/coparentes-app/',
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => 'https://coparentes.ai/#strona',
            'url' => 'https://coparentes.ai/',
            'name' => 'Coparentes',
            'inLanguage' => ['pl', 'en', 'de', 'es', 'fr', 'zh'],
            'publisher' => ['@id' => 'https://coparentes.ai/#organizacja'],
        ],
        [
            '@type' => 'WebApplication',
            '@id' => 'https://coparentes.ai/#aplikacja',
            'name' => 'Coparentes',
            'url' => 'https://getcoparentes.app/',
            'applicationCategory' => 'LifestyleApplication',
            'operatingSystem' => 'Przeglądarka internetowa',
            'description' => 'Coparentes to aplikacja dla rodziców po rozstaniu, którzy chcą spokojnie i z szacunkiem organizować życie dziecka w dwóch domach. Wspólny kalendarz opieki, komunikator z wiadomościami, których nie da się edytować ani usunąć, przejrzysty podział wydatków i dokumenty dziecka w jednym miejscu. Dziecko ma własne konto z planem tygodnia i czatem z obojgiem rodziców, dzięki czemu nie musi już pośredniczyć w rozmowach dorosłych.',
            'featureList' => [
                'Współdzielony kalendarz z podziałem opieki',
                'Bezpieczny komunikator z wątkami',
                'Rozliczanie wydatków pozaalimentacyjnych',
                'Dokumenty i najważniejsze informacje o dziecku',
                'Konto dla dziecka z planem tygodnia',
                'Zadania i przypomnienia po ustaleniach',
            ],
            'audience' => [
                '@type' => 'Audience',
                'audienceType' => 'Rodzice po rozwodzie lub rozstaniu, rodziny patchworkowe',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'PLN',
                'description' => 'Bezpłatna wersja testowa (beta)',
            ],
            'publisher' => ['@id' => 'https://coparentes.ai/#organizacja'],
        ],
    ];

    $payload = [
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ];

    $json = wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return;
    }

    echo "\n<script type=\"application/ld+json\">" . $json . "</script>\n";
}, 20);

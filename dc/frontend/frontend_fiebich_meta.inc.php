<?php
/*
 * Open-Graph-Tags und strukturierte Daten fuer die Naturheilpraxis Fiebich.
 *
 * Aus dem Briefing:
 *   "Sind die Open-Graph-Tags (og:image, og:title, og:description) im
 *    Head-Bereich der Website korrekt eingerichtet? Welches Vorschaubild wird
 *    beim Teilen der Website auf Social Media angezeigt?"
 *   "Fuer eine lokale Praxis lohnt sich ein LocalBusiness/MedicalBusiness-
 *    Schema (Name, Adresse, Oeffnungszeiten, Telefonnummer)."
 *
 * Bisher gab es beides nicht. Ohne Open Graph sucht sich Facebook beim Teilen
 * irgendein Bild von der Seite - haeufig ein Zufallsfund in halber Groesse.
 *
 * Die Angaben unten stammen aus dem Impressum und der Kontaktseite. Bewusst
 * nicht enthalten: openingHours. Die Praxis arbeitet als Bestellpraxis nach
 * Vereinbarung, feste Sprechzeiten stehen nirgends auf der Seite - erfundene
 * Zeiten in strukturierten Daten waeren schlimmer als gar keine.
 *
 * Eingebunden aus dc/frontend/frontend_fiebich.php, nach create_meta_tags().
 */

$og_titel = $GLOBALS['computed_site_title'] ?? '';
$og_beschreibung = $GLOBALS['computed_meta_description'] ?? '';

$og_host = $_SERVER['SERVER_NAME'] ?? '';
$og_schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$og_pfad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$og_url = $og_schema . '://' . $og_host . $og_pfad;

// Vorschaubild beim Teilen. Das Logo ist mit 1294x440 das einzige Bild im
// Bestand, das dafuer taugt. Empfohlen sind 1200x630 - ein eigens dafuer
// angelegtes Bild waere besser und ist als Aufgabe vermerkt.
$og_bild = $og_schema . '://' . $og_host . '/userdata/images/Logo%202024_1.jpeg';

$og_seitenname = 'Naturheilpraxis Christian Fiebich';

$e = function ($wert) {
    return htmlspecialchars((string)$wert, ENT_QUOTES, 'UTF-8');
};
?>
<meta property="og:type" content="website" />
<meta property="og:locale" content="de_DE" />
<meta property="og:site_name" content="<?= $e($og_seitenname) ?>" />
<meta property="og:title" content="<?= $e($og_titel) ?>" />
<?php if ($og_beschreibung !== '') { ?>
<meta property="og:description" content="<?= $e(html_entity_decode($og_beschreibung, ENT_QUOTES, 'UTF-8')) ?>" />
<?php } ?>
<meta property="og:url" content="<?= $e($og_url) ?>" />
<meta property="og:image" content="<?= $e($og_bild) ?>" />
<meta property="og:image:width" content="1294" />
<meta property="og:image:height" content="440" />
<meta property="og:image:alt" content="Logo der Naturheilpraxis Christian Fiebich" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= $e($og_titel) ?>" />
<?php if ($og_beschreibung !== '') { ?>
<meta name="twitter:description" content="<?= $e(html_entity_decode($og_beschreibung, ENT_QUOTES, 'UTF-8')) ?>" />
<?php } ?>
<meta name="twitter:image" content="<?= $e($og_bild) ?>" />
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'MedicalBusiness',
    'name' => 'Naturheilpraxis Christian Fiebich',
    'alternateName' => 'Heilpraktiker Christian Fiebich',
    'description' => 'Naturheilpraxis in Kulmbach mit klassischer Homöopathie, '
        . 'manuellen Therapieverfahren, HRV-Analyse und individueller Gesundheitsberatung.',
    'url' => $og_schema . '://' . $og_host . '/',
    'logo' => $og_bild,
    'image' => $og_bild,
    'telephone' => '+49 9221 4079882',
    'email' => 'mail@heilpraktiker-fiebich.de',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Rebenstraße 67',
        'postalCode' => '95326',
        'addressLocality' => 'Kulmbach',
        'addressRegion' => 'Bayern',
        'addressCountry' => 'DE',
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => 50.115717,
        'longitude' => 11.428552,
    ],
    'sameAs' => [
        'https://www.facebook.com/HPFiebich',
        'https://www.instagram.com/heilpraktiker_fiebich',
    ],
    'availableService' => [
        ['@type' => 'MedicalTherapy', 'name' => 'Klassische Homöopathie'],
        ['@type' => 'MedicalTherapy', 'name' => 'Manuelle Therapieverfahren'],
        ['@type' => 'MedicalTest', 'name' => 'HRV-Analyse (Herzratenvariabilität)'],
        ['@type' => 'MedicalTherapy', 'name' => 'Individuelle Gesundheitsberatung'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

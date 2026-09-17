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
 * Gepflegt wird beides in der Redaktion an der Sprache (main_language.og_image
 * und main_language.structured_data, siehe db-patches/2026-09-17-02). Dort
 * stehen schon Browsertitel, Meta-Beschreibung und Meta-Suchwoerter - eine
 * Adressaenderung braucht damit kein Deployment mehr. Die Werte im Code
 * bleiben als Rueckfall stehen: Ist das Feld leer oder enthaelt es kein
 * gueltiges JSON, wird der Stand von hier ausgegeben. Eine kaputte Eingabe
 * darf nicht dazu fuehren, dass Google gar keine Daten mehr bekommt.
 *
 * Eingebunden aus dc/frontend/frontend_fiebich.php, nach create_meta_tags().
 */

$og_titel = $GLOBALS['computed_site_title'] ?? '';
$og_beschreibung = $GLOBALS['computed_meta_description'] ?? '';

$og_host = $_SERVER['SERVER_NAME'] ?? '';
$og_schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$og_pfad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$og_url = $og_schema . '://' . $og_host . $og_pfad;

// Vorschaubild beim Teilen.
//
// Der Pfad kommt aus der Sprache; ohne Pflege bleibt es beim Logo. Empfohlen
// sind 1200x630 - das Logo hat 1294x440 und ist damit nur die Notloesung.
$og_bild_pfad = trim((string)($GLOBALS['language']['og_image'] ?? ''));
if ($og_bild_pfad === '') {
    $og_bild_pfad = '/userdata/images/og-image.jpg';
}
if (strpos($og_bild_pfad, '/') !== 0) {
    $og_bild_pfad = '/' . $og_bild_pfad;
}

// Die Abmessungen werden aus der Datei gelesen statt fest eingetragen. Sonst
// stuenden nach dem ersten Bildwechsel in der Redaktion falsche Werte im
// Quelltext - und Facebook baut seine Vorschau genau darauf.
$og_bild_breite = 1294;
$og_bild_hoehe = 440;
$og_bild_datei = rtrim(dirname(dirname(__DIR__)), '/\\') . rawurldecode($og_bild_pfad);
if (is_file($og_bild_datei)) {
    $og_masse = @getimagesize($og_bild_datei);
    if (is_array($og_masse) && !empty($og_masse[0]) && !empty($og_masse[1])) {
        $og_bild_breite = (int)$og_masse[0];
        $og_bild_hoehe = (int)$og_masse[1];
    }
}

// Leerzeichen und Umlaute im Dateinamen muessen in der URL kodiert sein, die
// Schraegstriche aber nicht.
$og_bild = $og_schema . '://' . $og_host . '/'
    . implode('/', array_map('rawurlencode', explode('/', ltrim($og_bild_pfad, '/'))));

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
<meta property="og:image:width" content="<?= (int)$og_bild_breite ?>" />
<meta property="og:image:height" content="<?= (int)$og_bild_hoehe ?>" />
<meta property="og:image:alt" content="<?= $e($og_seitenname) ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= $e($og_titel) ?>" />
<?php if ($og_beschreibung !== '') { ?>
<meta name="twitter:description" content="<?= $e(html_entity_decode($og_beschreibung, ENT_QUOTES, 'UTF-8')) ?>" />
<?php } ?>
<meta name="twitter:image" content="<?= $e($og_bild) ?>" />
<?php
// Strukturierte Daten
//
// Erste Wahl ist das Feld an der Sprache. Es wird vor der Ausgabe geprueft:
// Nur was sich als JSON lesen laesst und ein Objekt ergibt, geht raus. Sonst
// greift der fest hinterlegte Stand darunter - lieber die alten Daten als
// gar keine, und ein Syntaxfehler in der Redaktion darf die Seite nicht mit
// kaputtem JSON-LD ausliefern.
//
// Ausgegeben wird der Text der Redaktion nicht woertlich, sondern neu
// kodiert. Das normalisiert die Formatierung und schliesst aus, dass ein
// </script> im Feld das Skript-Tag vorzeitig beendet.
$strukturierte_daten = null;
$sd_gepflegt = trim((string)($GLOBALS['language']['structured_data'] ?? ''));
if ($sd_gepflegt !== '') {
    $sd_gelesen = json_decode($sd_gepflegt, true);
    if (is_array($sd_gelesen) && $sd_gelesen !== []) {
        $strukturierte_daten = $sd_gelesen;
    } else {
        error_log(
            'frontend_fiebich_meta: structured_data der Sprache '
            . (int)($GLOBALS['language']['id'] ?? 0) . ' ist kein gueltiges JSON ('
            . json_last_error_msg() . '), Rueckfall auf die Angaben im Code.'
        );
    }
}

if ($strukturierte_daten === null) {
    $strukturierte_daten = [
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
];
}
?>
<script type="application/ld+json">
<?= json_encode($strukturierte_daten, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG) ?>
</script>

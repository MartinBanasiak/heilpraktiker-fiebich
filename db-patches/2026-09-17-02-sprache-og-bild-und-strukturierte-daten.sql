-- ============================================================================
-- Vorschaubild und strukturierte Daten an der Sprache pflegbar machen
-- Datum: 2026-09-17
--
-- Bisher standen beide fest in dc/frontend/frontend_fiebich_meta.inc.php. Eine
-- Adressaenderung oder ein neues Teilen-Bild brauchte damit einen Entwickler
-- und ein Deployment. Beides gehoert in die Redaktion - und zwar an die
-- Sprache, weil genau dort schon Browsertitel, Meta-Beschreibung und
-- Meta-Suchwoerter gepflegt werden. Eine zweite Sprache brauchte sonst eine
-- zweite Codeaenderung.
--
--   og_image         Pfad zum Vorschaubild fuer Facebook, Instagram und Co.
--                    Im Backend ueber "Durchsuchen" aus der Dateiverwaltung
--                    waehlbar. Leer = das Logo, wie bisher.
--   structured_data  Das JSON-LD der Praxis. Leer oder fehlerhaft = der
--                    bisherige fest hinterlegte Stand; das Frontend prueft
--                    das Feld vor der Ausgabe und faellt sonst zurueck.
--
-- Die Startwerte unten entsprechen exakt dem, was bisher im Code stand. Nach
-- dem Einspielen aendert sich an der Ausgabe also nichts - nur der Ort, an dem
-- es gepflegt wird.
--
-- ADD COLUMN IF NOT EXISTS ist MariaDB-eigen. Der Stack laeuft auf MariaDB
-- 10.11; auf MySQL muesste die Pruefung ueber information_schema laufen.
-- ============================================================================

ALTER TABLE main_language
    ADD COLUMN IF NOT EXISTS og_image VARCHAR(255) NOT NULL DEFAULT '' AFTER meta_keywords,
    ADD COLUMN IF NOT EXISTS structured_data MEDIUMTEXT NULL AFTER og_image;

-- Startwerte fuer Deutsch (Sprache 53). Nur setzen, solange nichts gepflegt
-- ist - ein erneuter Lauf ueberschreibt keine Redaktionsaenderung.
UPDATE main_language
   SET og_image = '/userdata/images/Logo 2024_1.jpeg'
 WHERE id = 53 AND og_image = '';

UPDATE main_language
   SET structured_data = '{
    "@context": "https://schema.org",
    "@type": "MedicalBusiness",
    "name": "Naturheilpraxis Christian Fiebich",
    "alternateName": "Heilpraktiker Christian Fiebich",
    "description": "Naturheilpraxis in Kulmbach mit klassischer Homöopathie, manuellen Therapieverfahren, HRV-Analyse und individueller Gesundheitsberatung.",
    "url": "https://www.heilpraktiker-fiebich.de/",
    "telephone": "+49 9221 4079882",
    "email": "mail@heilpraktiker-fiebich.de",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "Rebenstraße 67",
        "postalCode": "95326",
        "addressLocality": "Kulmbach",
        "addressRegion": "Bayern",
        "addressCountry": "DE"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 50.115717,
        "longitude": 11.428552
    },
    "sameAs": [
        "https://www.facebook.com/HPFiebich",
        "https://www.instagram.com/heilpraktiker_fiebich"
    ],
    "availableService": [
        {
            "@type": "MedicalTherapy",
            "name": "Klassische Homöopathie"
        },
        {
            "@type": "MedicalTherapy",
            "name": "Manuelle Therapieverfahren"
        },
        {
            "@type": "MedicalTest",
            "name": "HRV-Analyse (Herzratenvariabilität)"
        },
        {
            "@type": "MedicalTherapy",
            "name": "Individuelle Gesundheitsberatung"
        }
    ]
}'
 WHERE id = 53 AND (structured_data IS NULL OR structured_data = '');

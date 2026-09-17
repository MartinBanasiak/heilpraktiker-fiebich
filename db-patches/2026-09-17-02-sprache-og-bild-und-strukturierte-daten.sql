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
-- Zur Schreibweise: Die erste Fassung benutzte ADD COLUMN IF NOT EXISTS. Das
-- ist MariaDB-eigen - auf dem Server lief es auf einen Syntaxfehler. Die
-- Pruefung laeuft deshalb ueber information_schema und ein vorbereitetes
-- Statement. Das versteht MySQL ab 5.7 genauso wie jedes MariaDB.
-- ============================================================================

-- og_image
SET @vorhanden = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'main_language'
       AND COLUMN_NAME = 'og_image'
);
SET @befehl = IF(
    @vorhanden = 0,
    "ALTER TABLE main_language ADD COLUMN og_image VARCHAR(255) NOT NULL DEFAULT '' AFTER meta_keywords",
    "DO 0"
);
PREPARE anlegen FROM @befehl;
EXECUTE anlegen;
DEALLOCATE PREPARE anlegen;

-- structured_data
SET @vorhanden = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'main_language'
       AND COLUMN_NAME = 'structured_data'
);
SET @befehl = IF(
    @vorhanden = 0,
    "ALTER TABLE main_language ADD COLUMN structured_data MEDIUMTEXT NULL AFTER og_image",
    "DO 0"
);
PREPARE anlegen FROM @befehl;
EXECUTE anlegen;
DEALLOCATE PREPARE anlegen;

-- Startwerte fuer Deutsch. Nur setzen, solange nichts gepflegt ist - ein
-- erneuter Lauf ueberschreibt keine Redaktionsaenderung.
--
-- Angesprochen ueber den Sprachcode, nicht ueber die ID: Eine falsch geratene
-- ID wuerde hier keinen Fehler werfen, sondern einfach keine Zeile treffen.
-- Die Spalten waeren dann da und leer, das Frontend faellt auf die Angaben im
-- Code zurueck - und niemand merkt, dass der Patch nur halb gewirkt hat.
UPDATE main_language
   SET og_image = '/userdata/images/Logo 2024_1.jpeg'
 WHERE code = 'de' AND og_image = '';

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
 WHERE code = 'de' AND (structured_data IS NULL OR structured_data = '');

-- ============================================================================
-- Kachel "Beratung / Coaching" auf die Menü-Bezeichnung angleichen
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage weitere fragen.pdf"
--        "Startseite: 'Beratung / Coaching' noch umbenennen. Im Hauptmenü
--         heißt es bereits korrekt 'Individuelle Gesundheitsberatung', auf der
--         Startseite steht bei der Kachel aber noch 'Beratung / Coaching'."
--
-- Geändert werden:
--   - die Überschrift der Startseiten-Kachel
--   - der Alt-Text des Kachelbildes, der noch "Immuntraining" lautete - ein
--     drittes, wieder anderes Wort für dieselbe Sache
--   - die absolute Verlinkung auf die Live-Domain innerhalb der Kachel. Solche
--     Links führen aus jeder Testumgebung heraus direkt auf die echte Seite.
--   - Titel und Untertitel der zugehörigen Seite
--   - die Bezeichnung des Inhaltsblocks, damit er im Backend auffindbar bleibt
--
-- NICHT geändert: main_collection.description der Collection 273 bleibt
-- "Gesundheitsberatung". Aus diesem Feld erzeugt get_collection_rewrite() den
-- URL-Slug. Eine Umbenennung würde die Adresse zu
-- individuelle-gesundheitsberatung-273 ändern und damit genau den Fehler
-- wiederholen, der die Links ursprünglich zerschossen hat. Wenn die Detailseite
-- auch im Titel "Individuell" tragen soll, führt der Weg über
-- main_collection.subtitle - der wirkt auf den Seitentitel, nicht auf die URL.
-- ============================================================================

UPDATE textcontent_header
   SET content = REPLACE(content,
                         '>Beratung / Coaching</h3>',
                         '>Individuelle Gesundheitsberatung</h3>')
 WHERE id = 769;

UPDATE textcontent_header
   SET content = REPLACE(content,
                         'alt="Immuntraining"',
                         'alt="Individuelle Gesundheitsberatung"')
 WHERE id = 769;

UPDATE textcontent_header
   SET content = REPLACE(content,
                         'https://www.heilpraktiker-fiebich.de/de/behandlungsmethoden/gesundheitsberatung-273/',
                         '/de/behandlungsmethoden/gesundheitsberatung-273/')
 WHERE id = 769;

UPDATE textcontent_header
   SET description = 'Startseite: Kachel Individuelle Gesundheitsberatung'
 WHERE id = 769
   AND description = 'Startseite: Kachel Beratung / Coaching';

UPDATE main_page
   SET title = 'Individuelle Gesundheitsberatung',
       subtitle = 'Individuelle Gesundheitsberatung'
 WHERE id = 354
   AND title = 'Beratung / Coaching';

-- ============================================================================
-- Seitentitel entdoppeln
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage weitere fragen.pdf"
--        "Der aktuelle Seitentitel der Startseite erscheint als
--         'Naturheilpraxis Fiebich | Naturheilpraxis Heilpraktiker Kulmbach'.
--         Das ist etwas doppelt. Ich würde eher verwenden:
--         Naturheilpraxis Christian Fiebich | Heilpraktiker in Kulmbach"
--
-- Der Titel entsteht aus zwei Teilen: dem Untertitel der Seite
-- (main_page.subtitle) und dem Zusatz aus der Sprache
-- (main_language.site_title_name), verbunden mit " | ".
--
-- Beim Prüfen ist neben der Startseite ein zweiter, deutlich schwererer Fall
-- aufgefallen. Die FAQ-Seite trug:
--
--   "Häufige Fragen | Naturheilpraxis Christian Fiebich Kulmbach |
--    Naturheilpraxis Heilpraktiker Kulmbach"
--
-- 105 Zeichen, dreifach gebrandet. Google zeigt rund 60 Zeichen - vom
-- eigentlichen Thema blieb in den Suchergebnissen nichts übrig. Der
-- Praxisname steckt bereits im Zusatz und muss nicht im Untertitel stehen.
--
-- Danach:
--   Startseite  Naturheilpraxis Christian Fiebich | Heilpraktiker in Kulmbach
--   FAQ         Häufige Fragen | Heilpraktiker in Kulmbach
--   Über mich   Über mich | Heilpraktiker in Kulmbach
-- ============================================================================

-- Zusatz für alle Seitentitel
UPDATE main_language
   SET site_title_name = 'Heilpraktiker in Kulmbach'
 WHERE id = 53
   AND site_title_name = 'Naturheilpraxis Heilpraktiker Kulmbach';

-- Startseite
UPDATE main_page
   SET subtitle = 'Naturheilpraxis Christian Fiebich'
 WHERE id = 338
   AND subtitle = 'Naturheilpraxis Fiebich';

-- FAQ
UPDATE main_page
   SET subtitle = 'Häufige Fragen'
 WHERE id = 357
   AND subtitle = 'Häufige Fragen | Naturheilpraxis Christian Fiebich Kulmbach';

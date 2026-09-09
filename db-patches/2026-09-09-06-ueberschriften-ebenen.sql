-- ============================================================================
-- Überschriftenebenen korrigieren
-- Datum: 2026-09-09
--
-- Nicht aus dem Briefing, sondern aus der Barrierefreiheits-Prüfung:
--
--   Startseite   h1 -> h3    (h2 fehlt)
--   FAQ          h1 -> h3    (h2 fehlt), dazu eine leere Überschrift
--
-- Screenreader bieten eine Überschriftenliste zum Navigieren an. Übersprungene
-- Ebenen lassen dort Lücken entstehen und suggerieren eine Verschachtelung,
-- die es nicht gibt (WCAG 1.3.1). Die leere Überschrift auf der FAQ-Seite -
-- ein <h3> mit nichts als einem geschützten Leerzeichen - taucht als
-- namenloser Eintrag darin auf.
--
-- Wichtig für die Optik: Die Ebene wechselt, das Aussehen nicht. Das
-- Stylesheet stellt für jede Überschriftengröße eine gleichnamige Klasse
-- bereit (.h1 bis .h6). Aus <h3> wird deshalb <h2 class="h3"> - semantisch
-- eine Ebene höher, visuell unverändert. Genau dafür gibt es diese Klassen.
--
-- Danach: h1 -> h2 auf beiden Seiten, ohne Sprünge.
-- ============================================================================

-- --- Startseite: Kacheln und Textblöcke -------------------------------------

UPDATE textcontent_header
   SET content = REPLACE(REPLACE(content,
                 '<h3 style="text-align: center;margin-bottom:0;">',
                 '<h2 class="h3" style="text-align: center;margin-bottom:0;">'),
                 '</h3>', '</h2>')
 WHERE id IN (766, 767, 768, 769);

UPDATE textcontent_header
   SET content = REPLACE(REPLACE(content, '<h3>', '<h2 class="h3">'), '</h3>', '</h2>')
 WHERE id IN (757, 760, 762);

-- --- FAQ --------------------------------------------------------------------
-- Erst die leere Überschrift entfernen, dann die Ebene anheben.

UPDATE textcontent_header
   SET content = REPLACE(content, '<h3>&nbsp;</h3>', '')
 WHERE id = 876;

UPDATE textcontent_header
   SET content = REPLACE(REPLACE(content, '<h3>', '<h2 class="h3">'), '</h3>', '</h2>')
 WHERE id = 876;

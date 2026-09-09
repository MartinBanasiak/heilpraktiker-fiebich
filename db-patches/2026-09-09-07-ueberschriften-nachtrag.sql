-- ============================================================================
-- Überschriften: Nachtrag für Diagnostik und Über mich
-- Datum: 2026-09-09
-- Ergänzt Patch 2026-09-09-06 um zwei Seiten, die bei der ersten Prüfung
-- nicht dabei waren.
--
-- Diagnostik: Die Seite trug zwei h1 - einmal aus der Seitenüberschrift,
--   einmal aus dem Inhalt ("Diagnostik in meiner Praxis:"). Eine Seite hat
--   genau eine h1; alles Weitere ordnet sich darunter ein. Danach folgen die
--   bestehenden h3 ohne Sprung auf die neue h2.
--
-- Über mich: eine leere Überschrift, wie schon auf der FAQ-Seite. Ein <h3>
--   mit nichts als einem geschützten Leerzeichen taucht in der
--   Überschriftenliste von Screenreadern als namenloser Eintrag auf.
--
-- Bei der Diagnostik-Seite wechselt nur die Ebene, nicht die Optik: die
-- Klasse .h1 hält die bisherige Darstellung.
-- ============================================================================

UPDATE textcontent_header
   SET content = REPLACE(content,
                 '<h1>Diagnostik in meiner Praxis:</h1>',
                 '<h2 class="h1">Diagnostik in meiner Praxis:</h2>')
 WHERE id = 855;

UPDATE textcontent_header
   SET content = REPLACE(content, '<h3>&nbsp;</h3>', '')
 WHERE id = 788;

-- ============================================================================
-- Logo-Link auf die Startseite, tote Weiterleitungskette HRV entfernen
-- Datum: 2026-10-12
-- Bezug: Search-Console-Analyse (Indexierung Startseite / Duplikate)
--
-- 1. Logo
--    Das Logo im Kopf verlinkt auf http://www.heilpraktiker-fiebich.de - ohne
--    https und ohne Schrägstrich. Jeder Klick und jeder Crawler-Besuch nimmt
--    damit einen 301-Umweg, und Google findet auf jeder Seite einen Link auf
--    eine Adresse, die nicht die kanonische ist. Relativ auf / gesetzt, damit
--    der Link auch in Testumgebungen auf der eigenen Domain bleibt (siehe
--    Patch 2026-09-09-04).
--
-- 2. Weiterleitung /de/behandlungsmethoden/hrv-messung-278/
--    Die Regel aus Patch 2026-09-09-02 zeigt auf hrv-analyse-278. Die
--    Kollektion 278 heißt inzwischen anders, die Zieladresse lieferte einen
--    404 - die Weiterleitung endete also auf einer Fehlerseite.
--    Seit dem Code-Commit zu diesem Patch leitet das Frontend jede
--    Detailseiten-Adresse mit veraltetem Slug oder falschem Menüpunkt selbst
--    per 301 auf die gültige Adresse um (get_collection_canonical_path()).
--    Die Regel wird damit nicht mehr gebraucht und würde nur einen zweiten
--    Sprung erzeugen. Deaktiviert statt gelöscht, damit sie im Backend
--    nachvollziehbar bleibt.
--
--    WICHTIG: Erst einspielen, wenn der Code-Stand live ist. Ohne ihn liefert
--    die alte Adresse nach diesem Patch einen 404.
--
-- Idempotent: beide Änderungen greifen nur, solange der alte Stand vorliegt.
-- ============================================================================

UPDATE textcontent_header
   SET content = REPLACE(content,
                         'href="http://www.heilpraktiker-fiebich.de"',
                         'href="/"')
 WHERE id = 747
   AND content LIKE '%href="http://www.heilpraktiker-fiebich.de"%';

UPDATE main_rewrite_rules
   SET active = 0
 WHERE TRIM(old_url) = '/de/behandlungsmethoden/hrv-messung-278/'
   AND new_url = '/de/behandlungsmethoden/hrv-analyse-278/'
   AND active = 1;

-- ============================================================================
-- 301-Weiterleitungen für die alten URLs
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage weitere fragen.pdf"
--        "Alte URLs/Seiten sauber weiterleiten oder entfernen. Google findet
--         noch die alte Seite 'Beratung und Coaching' [...]. Diese alte URL
--         sollte nicht parallel indexierbar bleiben."
--
-- Die Links im Inhalt sind mit Patch 2026-09-08-01 bereits korrigiert. Die
-- alten Adressen stehen aber weiterhin im Google-Index und in Lesezeichen -
-- ohne Weiterleitung landen alle dort auf der 404-Seite.
--
-- Die Tabelle vergleicht exakt (kein Muster), jede Adresse braucht also eine
-- eigene Zeile. Der Abgleich in checkForActiveRedirects() erfolgt mit und
-- ohne abschließenden Schrägstrich, beide Schreibweisen sind damit erfasst.
--
-- Vollständigkeit: Diese Liste deckt ab, was sich aus Datenbank und Inhalt
-- belegen lässt. Welche Adressen Google darüber hinaus kennt, zeigt nur die
-- Search Console - der Abgleich dort steht noch aus.
--
-- Idempotent: Die Regeln werden nur angelegt, wenn old_url noch fehlt.
-- ============================================================================

INSERT INTO main_rewrite_rules (old_url, new_url, rewrite_code, active)
SELECT * FROM (
    SELECT '/de/behandlungsmethoden/beratung-coaching-273/' AS o,
           '/de/behandlungsmethoden/gesundheitsberatung-273/' AS n, '301' AS c, 1 AS a
    UNION ALL SELECT '/de/behandlungsmethoden/immuntraining-273/',
                     '/de/behandlungsmethoden/gesundheitsberatung-273/', '301', 1
    UNION ALL SELECT '/de/behandlungsmethoden/hrv-messung-278/',
                     '/de/behandlungsmethoden/hrv-analyse-278/', '301', 1
    UNION ALL SELECT '/de/links/',            '/de/info/links/',   '301', 1
    UNION ALL SELECT '/natur/de/',            '/de/',              '301', 1
    UNION ALL SELECT '/natur/de/kontakt/',    '/de/kontakt/',      '301', 1
    UNION ALL SELECT '/natur/de/downloads/',  '/de/downloads/',    '301', 1
    UNION ALL SELECT '/natur/de/gebuehren2/', '/de/gebuehren/',    '301', 1
    UNION ALL SELECT '/natur/de/gebuehren/',  '/de/gebuehren/',    '301', 1
    UNION ALL SELECT '/natur/de/behandlungsmethoden/',
                     '/de/behandlungsmethoden/', '301', 1
    UNION ALL SELECT '/natur/de/behandlungsmethoden/hrv-analyse/',
                     '/de/behandlungsmethoden/hrv-analyse-278/', '301', 1
    UNION ALL SELECT '/natur/de/behandlungsmethoden/Gesundheitsberatung/',
                     '/de/behandlungsmethoden/gesundheitsberatung-273/', '301', 1
    UNION ALL SELECT '/natur/de/FAQ/',        '/de/FAQ/',          '301', 1
    UNION ALL SELECT '/natur/de/diagnostik/', '/de/diagnostik/',   '301', 1
    UNION ALL SELECT '/natur/de/ueber-mich/', '/de/ueber-mich/',   '301', 1
) AS neu
WHERE NOT EXISTS (
    SELECT 1 FROM main_rewrite_rules r WHERE TRIM(r.old_url) = neu.o
);

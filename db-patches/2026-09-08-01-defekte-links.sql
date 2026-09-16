-- ============================================================================
-- Defekte interne Links reparieren
-- Datum: 2026-09-08
-- Bezug: Briefing "Homepage.pdf", Punkte 2 und 6
--        ("Klassische Homoeopathie und Manuelle Therapieverfahren lassen sich
--          anklicken, HRV und Individuelle Gesundheitsberatung nicht" /
--         "Auf meiner Diagnostikseite kriege ich es nicht hin - zur HRV zu
--          verlinken, es kommt 404 Page not found")
--
-- Ursachen (drei verschiedene):
--
-- 1. Veraltete Slugs in main_navigation.forward_url
--    Die Detailseiten sind Collections. Ihre URL wird aus dem aktuellen Titel
--    plus Collection-ID gebildet. Die Collections 273 und 278 wurden umbenannt
--    ("Beratung/Coaching" -> "Gesundheitsberatung", "HRV-Messung" ->
--    "HRV-Analyse"), die Weiterleitungen im Menue zeigen aber noch auf die
--    alten Slugs. Betrifft das globale Menue, also jede Seite.
--
--      Collection 176 "Klassische Homoeopathie"  -> klassische-homoeopathie-176   OK
--      Collection 177 "Manuelle Therapieverfahren" -> manuelle-therapieverfahren-177 OK
--      Collection 273 "Gesundheitsberatung"      -> erwartet gesundheitsberatung-273,
--                                                   verlinkt war beratung-coaching-273
--      Collection 278 "HRV-Analyse"              -> erwartet hrv-analyse-278,
--                                                   verlinkt war hrv-messung-278
--
-- 2. Alte Langform-URLs im Redaktionsinhalt (/natur/de/... statt /de/...)
--    main_site.is_unique_site = 1, deshalb erzeugt und prueft das CMS kurze
--    URLs ohne Site-Code. Die alten /natur/de/-Links laufen ins 404.
--
-- 3. Falsche Ziele im Inhalt: /de/links/ (liegt unter /de/info/links/) und
--    /de/gebuehren2/ (diese Navigation ist inaktiv, aktiv ist /de/gebuehren/).
--
-- Geprueft am lokalen Stand des Dumps vom 2026-09-08. Idempotent - ein
-- zweiter Durchlauf aendert nichts mehr.
-- ============================================================================

-- --- 1. Menue-Weiterleitungen auf die aktuellen Slugs -----------------------

UPDATE main_navigation
   SET forward_url = '/de/behandlungsmethoden/gesundheitsberatung-273/'
 WHERE code = 'Gesundheitsberatung'
   AND forward_url = '/de/behandlungsmethoden/beratung-coaching-273/';

UPDATE main_navigation
   SET forward_url = '/de/behandlungsmethoden/hrv-analyse-278/'
 WHERE code = 'hrv-analyse'
   AND forward_url = '/de/behandlungsmethoden/hrv-messung-278/';

-- --- 2. Redaktionsinhalte ---------------------------------------------------
-- Reihenfolge beachten: gebuehren2 zuerst, sonst wuerde daraus /de/gebuehren2/
-- (inaktiv). Danach die generische Langform, zum Schluss /de/links/.

UPDATE textcontent_header
   SET content = REPLACE(content, '/natur/de/gebuehren2/', '/de/gebuehren/')
 WHERE content LIKE '%/natur/de/gebuehren2/%';

UPDATE textcontent_header
   SET content = REPLACE(content, '/natur/de/', '/de/')
 WHERE content LIKE '%/natur/de/%';

UPDATE textcontent_header
   SET content = REPLACE(content, 'href="/de/links/"', 'href="/de/info/links/"')
 WHERE content LIKE '%href="/de/links/"%';

UPDATE textcontent_header
   SET content = REPLACE(content,
                         '/de/behandlungsmethoden/beratung-coaching-273/',
                         '/de/behandlungsmethoden/gesundheitsberatung-273/')
 WHERE content LIKE '%/de/behandlungsmethoden/beratung-coaching-273/%';

UPDATE textcontent_header
   SET content = REPLACE(content,
                         '/de/behandlungsmethoden/hrv-messung-278/',
                         '/de/behandlungsmethoden/hrv-analyse-278/')
 WHERE content LIKE '%/de/behandlungsmethoden/hrv-messung-278/%';

-- --- 3. Klickziel der Startseiten-Kachel ------------------------------------
-- Die Kachel "Beratung / Coaching" zeigte auf /de/behandlungsmethoden/
-- immuntraining-273/ - diesen Slug hat die Collection nie gehabt.

UPDATE main_page_link
   SET main_page_group_link = '/de/behandlungsmethoden/gesundheitsberatung-273/'
 WHERE main_page_group_link IN ('/de/behandlungsmethoden/immuntraining-273/',
                                '/de/behandlungsmethoden/beratung-coaching-273/');

UPDATE main_page_link
   SET main_page_group_link = '/de/behandlungsmethoden/hrv-analyse-278/'
 WHERE main_page_group_link = '/de/behandlungsmethoden/hrv-messung-278/';

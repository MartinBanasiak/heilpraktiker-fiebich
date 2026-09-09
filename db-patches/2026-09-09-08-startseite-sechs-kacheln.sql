-- ============================================================================
-- Startseite: aus vier Kacheln werden sechs
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage.pdf", Punkt 4
--        "auf der Startseite haben wir hier 4 buttons - ich würde jedoch hier
--         alles Ablegen, werden dann 2 Reihen - Behandlungsmethoden allg.
--         Info, Homöopathie, Manuelle, HRV, Individuelle Gesundheitsberatung
--         [...] also aus 4 werden 6 Bilder - auch hier bitte prüfen dass am
--         Ende die Links funktionieren"
--
-- Reihenfolge wie im Briefing genannt, Diagnostik bleibt als sechste Kachel
-- am Ende:
--
--   1  Behandlung allg. Informationen  -> /de/behandlungsmethoden/
--   2  Klassische Homöopathie          -> klassische-homoeopathie-176
--   3  Manuelle Therapieverfahren      -> manuelle-therapieverfahren-177
--   4  HRV-Analyse                     -> hrv-analyse-278
--   5  Individuelle Gesundheitsberatung-> gesundheitsberatung-273
--   6  Diagnostik                      -> /de/diagnostik/
--
-- Aufteilung: bisher vier Spalten (col-md-3). Sechs Kacheln in vier Spalten
-- ergäben 4 + 2 - das Briefing möchte zwei gleiche Reihen. Deshalb drei
-- Spalten. Die dafür nötige Layout-Klasse gab es noch nicht: die vorhandene
-- "Inhalt 1/3" ist col-md-4 ohne col-sm-6, auf Tablets stünde dann nur eine
-- Kachel je Reihe. Die neue Klasse ergänzt col-sm-6, also 1 / 2 / 3 Kacheln
-- je Reihe von Handy bis Desktop.
--
-- Alle Bilder wechseln zugleich von PNG auf die SVG-Symbole.
--
-- Idempotent: Neue Zeilen werden nur angelegt, wenn sie fehlen.
-- ============================================================================

-- --- 1. Layout-Klasse für drei Spalten --------------------------------------

INSERT INTO main_layout_class (main_layout_id, code, name, sorting)
SELECT 3, 'col-xs-12 xs-margin col-sm-6 col-md-4', 'Inhalt 1/3 (3 Spalten)', 4
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM main_layout_class WHERE code = 'col-xs-12 xs-margin col-sm-6 col-md-4'
    ) AS vorhanden
 );

SET @klasse_drittel := (SELECT id FROM main_layout_class
                         WHERE code = 'col-xs-12 xs-margin col-sm-6 col-md-4' LIMIT 1);
SET @klasse_abstand := 81;   -- marginBottom--xlarge, wie bei den bisherigen Kacheln

-- --- 2. Bestehende Kacheln auf die SVG-Symbole umstellen --------------------

UPDATE textcontent_header SET content = REPLACE(content,
       '/userdata/images/Hom%C3%B6opathie.png',
       '/layout/frontend/fiebich/dist/icons/homoeopathie.svg') WHERE id = 766;

UPDATE textcontent_header SET content = REPLACE(content,
       '/userdata/images/Diagnostik.png',
       '/layout/frontend/fiebich/dist/icons/diagnostik.svg') WHERE id = 767;

UPDATE textcontent_header SET content = REPLACE(content,
       '/userdata/images/Manuelle.png',
       '/layout/frontend/fiebich/dist/icons/manuelle-therapieverfahren.svg') WHERE id = 768;

UPDATE textcontent_header SET content = REPLACE(content,
       '/userdata/images/Beratung.png',
       '/layout/frontend/fiebich/dist/icons/gesundheitsberatung.svg') WHERE id = 769;

-- --- 3. Inhalt der beiden neuen Kacheln -------------------------------------

INSERT INTO textcontent_header
       (main_language_id, description, type, content, modified_date, modified_user,
        collection_header, all_languages)
SELECT 53, 'Startseite: Kachel Behandlung allg. Informationen', 0,
       CONCAT(
         '<div style="text-align: center;"><img alt="" src="/layout/frontend/fiebich/dist/icons/behandlungsmethoden.svg" style="width: 200px; height: 200px;" /><br />\n',
         '&nbsp;</div>\n\n',
         '<h2 class="h3" style="text-align: center;margin-bottom:0;">Behandlung<br />\n',
         'allg. Informationen</h2>\n\n',
         '<div style="text-align: center;"><a href="/de/behandlungsmethoden/">mehr erfahren</a></div>\n'
       ),
       UNIX_TIMESTAMP(), 43, 0, 0
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM textcontent_header
         WHERE description = 'Startseite: Kachel Behandlung allg. Informationen'
    ) AS vorhanden
 );

INSERT INTO textcontent_header
       (main_language_id, description, type, content, modified_date, modified_user,
        collection_header, all_languages)
SELECT 53, 'Startseite: Kachel HRV-Analyse', 0,
       CONCAT(
         '<div style="text-align: center;"><img alt="" src="/layout/frontend/fiebich/dist/icons/hrv-analyse.svg" style="width: 200px; height: 200px;" /><br />\n',
         '&nbsp;</div>\n\n',
         '<h2 class="h3" style="text-align: center;margin-bottom:0;">HRV-Analyse<br />\n',
         '(Herzratenvariabilität)</h2>\n\n',
         '<div style="text-align: center;"><a href="/de/behandlungsmethoden/hrv-analyse-278/">mehr erfahren</a></div>\n'
       ),
       UNIX_TIMESTAMP(), 43, 0, 0
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM textcontent_header WHERE description = 'Startseite: Kachel HRV-Analyse'
    ) AS vorhanden
 );

SET @tc_allg := (SELECT id FROM textcontent_header
                  WHERE description = 'Startseite: Kachel Behandlung allg. Informationen' LIMIT 1);
SET @tc_hrv  := (SELECT id FROM textcontent_header
                  WHERE description = 'Startseite: Kachel HRV-Analyse' LIMIT 1);

-- --- 4. Kachel-Rahmen für die beiden neuen Einträge -------------------------
-- Aufbau wie bei den bestehenden: ein Gruppen-Element mit Linkziel, darunter
-- ein Verweis auf den Inhaltsbaustein. Elternelement ist die Reihe 766.

INSERT INTO main_page_link
       (main_page_id, main_sitepart_id, main_sitepart_header_id, main_page_group,
        main_page_group_code, main_page_group_name, main_page_group_link,
        main_page_link_parent_id, modified_user, modified_date, active,
        layout_area_id, layout_class_id, background_image_path, sorting)
SELECT 338, 0, 0, 1, 'groupcode', '1/3', '/de/behandlungsmethoden/',
       766, 43, UNIX_TIMESTAMP(), 1, 179, 0, '', 1
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM main_page_link
         WHERE main_page_link_parent_id = 766
           AND main_page_group_link = '/de/behandlungsmethoden/'
    ) AS vorhanden
 );

SET @kachel_allg := (SELECT id FROM main_page_link
                      WHERE main_page_link_parent_id = 766
                        AND main_page_group_link = '/de/behandlungsmethoden/' LIMIT 1);

INSERT INTO main_page_link
       (main_page_id, main_sitepart_id, main_sitepart_header_id, main_page_group,
        main_page_group_code, main_page_group_name, main_page_group_link,
        main_page_link_parent_id, modified_user, modified_date, active,
        layout_area_id, layout_class_id, background_image_path, sorting)
SELECT 338, 1, @tc_allg, 0, '', '', '', @kachel_allg, 43, UNIX_TIMESTAMP(), 1, 179, 0, '', 1
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM main_page_link WHERE main_page_link_parent_id = @kachel_allg
    ) AS vorhanden
 );

INSERT INTO main_page_link
       (main_page_id, main_sitepart_id, main_sitepart_header_id, main_page_group,
        main_page_group_code, main_page_group_name, main_page_group_link,
        main_page_link_parent_id, modified_user, modified_date, active,
        layout_area_id, layout_class_id, background_image_path, sorting)
SELECT 338, 0, 0, 1, 'groupcode', '1/3', '/de/behandlungsmethoden/hrv-analyse-278/',
       766, 43, UNIX_TIMESTAMP(), 1, 179, 0, '', 4
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM main_page_link
         WHERE main_page_link_parent_id = 766
           AND main_page_group_link = '/de/behandlungsmethoden/hrv-analyse-278/'
    ) AS vorhanden
 );

SET @kachel_hrv := (SELECT id FROM main_page_link
                     WHERE main_page_link_parent_id = 766
                       AND main_page_group_link = '/de/behandlungsmethoden/hrv-analyse-278/' LIMIT 1);

INSERT INTO main_page_link
       (main_page_id, main_sitepart_id, main_sitepart_header_id, main_page_group,
        main_page_group_code, main_page_group_name, main_page_group_link,
        main_page_link_parent_id, modified_user, modified_date, active,
        layout_area_id, layout_class_id, background_image_path, sorting)
SELECT 338, 1, @tc_hrv, 0, '', '', '', @kachel_hrv, 43, UNIX_TIMESTAMP(), 1, 179, 0, '', 1
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT id FROM main_page_link WHERE main_page_link_parent_id = @kachel_hrv
    ) AS vorhanden
 );

-- --- 5. Reihenfolge ---------------------------------------------------------

UPDATE main_page_link SET sorting = 1 WHERE id = @kachel_allg;
UPDATE main_page_link SET sorting = 2 WHERE main_page_link_parent_id = 766
   AND main_page_group_link = '/de/behandlungsmethoden/klassische-homoeopathie-176/';
UPDATE main_page_link SET sorting = 3 WHERE main_page_link_parent_id = 766
   AND main_page_group_link = '/de/behandlungsmethoden/manuelle-therapieverfahren-177/';
UPDATE main_page_link SET sorting = 4 WHERE id = @kachel_hrv;
UPDATE main_page_link SET sorting = 5 WHERE main_page_link_parent_id = 766
   AND main_page_group_link = '/de/behandlungsmethoden/gesundheitsberatung-273/';
UPDATE main_page_link SET sorting = 6 WHERE main_page_link_parent_id = 766
   AND main_page_group_link = '/de/diagnostik/';

-- --- 6. Drei Spalten statt vier ---------------------------------------------

UPDATE main_page_link SET main_page_group_name = '1/3'
 WHERE main_page_link_parent_id = 766 AND main_page_group = 1;

DELETE FROM main_page_link_layout_class_link
 WHERE main_layout_class_id = 58
   AND main_page_link_id IN (SELECT id FROM (
        SELECT id FROM main_page_link WHERE main_page_link_parent_id = 766 AND main_page_group = 1
   ) AS kacheln);

INSERT INTO main_page_link_layout_class_link (main_page_link_id, main_layout_class_id)
SELECT k.id, @klasse_drittel FROM (
    SELECT id FROM main_page_link WHERE main_page_link_parent_id = 766 AND main_page_group = 1
) AS k
WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT main_page_link_id FROM main_page_link_layout_class_link
         WHERE main_layout_class_id = @klasse_drittel
    ) AS gesetzt WHERE gesetzt.main_page_link_id = k.id
);

INSERT INTO main_page_link_layout_class_link (main_page_link_id, main_layout_class_id)
SELECT k.id, @klasse_abstand FROM (
    SELECT id FROM main_page_link WHERE main_page_link_parent_id = 766 AND main_page_group = 1
) AS k
WHERE NOT EXISTS (
    SELECT 1 FROM (
        SELECT main_page_link_id FROM main_page_link_layout_class_link
         WHERE main_layout_class_id = 81
    ) AS gesetzt WHERE gesetzt.main_page_link_id = k.id
);

-- ============================================================================
-- Menüeintrag "Behandlung allg. Informationen"
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage.pdf", Punkt 1
--        "Ich habe bei Behandlungsmethoden erst allgemeine Informationen, ich
--         fände es gut, wenn dies im Menü auch auftaucht, also links auch
--         'Behandlung allg. Informationen' steht"
--
-- Die Übersichtsseite unter /de/behandlungsmethoden/ war im Untermenü bisher
-- nicht aufgeführt. Erreichbar war sie nur über den übergeordneten Menüpunkt
-- selbst - wer im aufgeklappten Untermenü sucht, findet sie nicht.
--
-- Der neue Eintrag steht an erster Stelle (sorting 0) und leitet auf die
-- Übersichtsseite. Dasselbe Muster wie bei den vier bestehenden Einträgen:
-- forward_type 5 mit Ziel-URL.
--
-- Idempotent: legt nichts an, wenn der Code bereits existiert.
-- ============================================================================

INSERT INTO main_navigation (
    main_site_id, main_language_id, parent_id, sorting, level,
    code, menu_name, title_name,
    meta_keywords, meta_description,
    active, modified_date,
    forward_navigation_id, forward, forward_type, forward_page_id,
    forward_url, forward_shop_category, hidden, is_landing_page
)
SELECT 45, 53, 761, 0, 2,
       'allg-informationen', 'Behandlung allg. Informationen', 'Behandlung allg. Informationen',
       '', '',
       1, NOW(),
       0, 0, 5, 0,
       '/de/behandlungsmethoden/', 0, 0, 0
  FROM DUAL
 WHERE NOT EXISTS (
    SELECT 1 FROM (SELECT id FROM main_navigation WHERE code = 'allg-informationen' AND main_site_id = 45) AS vorhanden
 );

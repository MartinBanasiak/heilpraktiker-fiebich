-- ============================================================================
-- Textalternativen für Bilder ergänzen
-- Datum: 2026-09-09
-- Bezug: Briefing "Homepage weitere fragen.pdf", Abschnitt "Alt-Texte prüfen"
--
-- Zwei unterschiedlich schwere Fälle:
--
-- 1. Social-Media-Links (WCAG 2.4.4, schwerwiegend)
--    Der Link enthält ausschließlich ein Bild mit alt="". Damit hat der Link
--    überhaupt keinen zugänglichen Namen - ein Screenreader liest an dieser
--    Stelle bestenfalls den Dateinamen vor, oft gar nichts. Betrifft Kopf-
--    und Fußzeile und damit jede Seite.
--
-- 2. Zertifizierungs-Siegel (WCAG 1.1.1)
--    Fünf Siegel transportieren ihre Aussage rein als Bildtext. Ohne
--    Alternative sind die Qualifikationen für Screenreader unsichtbar - und
--    für Suchmaschinen ebenfalls. Die Texte geben wieder, was auf dem
--    jeweiligen Siegel steht.
--
-- Bewusst NICHT geändert: Die Kachelbilder auf der Startseite
-- (Diagnostik.png, Homöopathie.png, Manuelle.png, Startseite/kontakt.jpg,
-- Startseite/links.jpg) behalten alt="". Sie stehen direkt neben einer
-- Überschrift, die dasselbe aussagt. Ein Alt-Text würde die Information
-- doppelt vorlesen - leeres alt ist hier die richtige Auszeichnung.
--
-- Ohne WHERE: REPLACE lässt Zeilen ohne Treffer unverändert, und die Tabelle
-- ist klein. Mehrfaches Ausführen ändert nichts mehr.
-- ============================================================================

-- --- 1. Social-Media-Links ---------------------------------------------------

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/Facebook.png"',
    'alt="Naturheilpraxis Fiebich auf Facebook" src="/userdata/images/Facebook.png"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/Facebook%20blau.png"',
    'alt="Naturheilpraxis Fiebich auf Facebook" src="/userdata/images/Facebook%20blau.png"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/Instagram.png"',
    'alt="Naturheilpraxis Fiebich auf Instagram" src="/userdata/images/Instagram.png"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/instagram%20orange.png"',
    'alt="Naturheilpraxis Fiebich auf Instagram" src="/userdata/images/instagram%20orange.png"');

-- --- 2. Zertifizierungs-Siegel ----------------------------------------------

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/Immuntrainer%202.jpeg"',
    'alt="Siegel: zertifizierter Immuntrainer der Deutschen Akademie für Homöopathie und Naturheilverfahren" src="/userdata/images/Immuntrainer%202.jpeg"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/PhotoRoom_20231218_234240.jpeg"',
    'alt="Siegel: von proventika zertifizierter Stress-Burnout-Trainer (IAH), Institut für angewandte Hirnforschung und Neurowissenschaften" src="/userdata/images/PhotoRoom_20231218_234240.jpeg"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/PhotoRoom_20231218_234745.jpg"',
    'alt="Siegel: zertifizierter Detox-Trainer der Deutschen Akademie für Homöopathie und Naturheilverfahren" src="/userdata/images/PhotoRoom_20231218_234745.jpg"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/PhotoRoom_20231218_235230.jpg"',
    'alt="Siegel: zertifizierter Experte Darmgesundheit der Deutschen Akademie für Homöopathie und Naturheilverfahren" src="/userdata/images/PhotoRoom_20231218_235230.jpg"');

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/PhotoRoom_20231218_235358.jpg"',
    'alt="Siegel: Zertifizierung Sport-Health-Balance-Trainer der Deutschen Akademie für Homöopathie und Naturheilverfahren" src="/userdata/images/PhotoRoom_20231218_235358.jpg"');

-- --- 3. Abbildung auf der HRV-Seite -----------------------------------------
-- Zeigt eine EKG-Kurve mit markierten R-Zacken und den Abständen zwischen
-- zwei Herzschlägen - genau das, worum es bei der HRV-Messung geht.

UPDATE textcontent_header SET content = REPLACE(
    content,
    'alt="" src="/userdata/images/IMG_3128.jpg"',
    'alt="EKG-Kurve mit markierten R-Zacken; die Abstände zwischen zwei Herzschlägen liegen zwischen 712 und 832 Millisekunden" src="/userdata/images/IMG_3128.jpg"');

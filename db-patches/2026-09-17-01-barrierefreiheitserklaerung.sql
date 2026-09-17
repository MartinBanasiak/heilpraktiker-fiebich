-- ============================================================================
-- Seite "Erklärung zur Barrierefreiheit"
-- Datum: 2026-09-17
-- Bezug: Anforderung aus dem Relaunch, Vorbild https://www.dc.ag/de/barrierefreiheit
--
-- Die Seite liegt unter /de/info/barrierefreiheit/ und folgt exakt dem Aufbau
-- von Impressum (Seite 344) und Datenschutz (Seite 343): eine Überschriftszeile
-- im Bannerbereich (layout_area 180) mit demselben Motiv "rechtliches.jpg" und
-- darunter eine weiße Box (layout_area 179, main_page_group_code box--white)
-- mit dem Fließtext.
--
-- Inhaltliche Einordnung, die in den Text eingeflossen ist:
-- Die Praxis fällt nach heutigem Stand nicht unter das BFSG - es gibt keinen
-- elektronischen Geschäftsverkehr (kein Shop, keine Online-Buchung), und als
-- Kleinstunternehmen wäre sie bei Dienstleistungen ohnehin ausgenommen. Die
-- Erklärung ist deshalb bewusst als freiwillige Erklärung formuliert und nennt
-- KEINE Durchsetzungsstelle. Kommt später eine Online-Terminbuchung dazu,
-- ändert sich die Rechtslage und der Abschnitt gehört ergänzt - der fertige
-- Textbaustein dafür liegt in internal/barrierefreiheit-durchsetzungsstelle.html.
--
-- nofollow = 1, noindex = 0: gleiche Einstellung wie Impressum und Datenschutz.
--
-- Idempotent: legt nichts an, wenn der Navigationscode bereits existiert.
-- ============================================================================

-- Gibt es die Seite schon? Der Wert steuert jeden folgenden Schritt.
-- Ohne diese Variable wuerde LAST_INSERT_ID() nach einem uebersprungenen
-- INSERT den Wert des vorherigen Aufrufs liefern - der Patch haenge die
-- Inhalte dann an eine fremde Seite.
SET @vorhanden = (SELECT COUNT(*) FROM main_navigation WHERE code = 'barrierefreiheit' AND main_site_id = 45);

-- Seite -----------------------------------------------------------------------
INSERT INTO main_page (
    title, subtitle, meta_keywords, meta_description,
    main_language_id, active, modified_date, modified_user,
    validity_from, validity_to, noindex, nofollow, is_shopping_world, is_template
)
SELECT 'Barrierefreiheit',
       'Erklärung zur Barrierefreiheit',
       '',
       'Erklärung zur Barrierefreiheit der Naturheilpraxis Christian Fiebich: Stand der Umsetzung, bekannte Einschränkungen und Kontakt für Rückmeldungen.',
       53, 1, UNIX_TIMESTAMP(), 43,
       NULL, NULL, 0, 1, 0, 0
  FROM DUAL WHERE @vorhanden = 0;

SET @seite = IF(@vorhanden = 0, LAST_INSERT_ID(), 0);

-- Überschrift -----------------------------------------------------------------
INSERT INTO textcontent_header (main_language_id, description, type, content, modified_date, modified_user, collection_header, all_languages)
SELECT 53, 'Barrierefreiheit: Überschrift', 0,
       '<h1 style="text-align: center;">Erklärung zur Barrierefreiheit</h1>',
       UNIX_TIMESTAMP(), 43, 0, 0
  FROM DUAL WHERE @seite > 0;

SET @ueberschrift = IF(@seite > 0, LAST_INSERT_ID(), 0);

-- Fließtext -------------------------------------------------------------------
INSERT INTO textcontent_header (main_language_id, description, type, content, modified_date, modified_user, collection_header, all_languages)
SELECT 53, 'Barrierefreiheit', 0,
'<p>Mir ist wichtig, dass meine Internetseite von allen Besucherinnen und Besuchern genutzt werden kann &ndash; auch von Menschen, die schlecht sehen, keine Maus bedienen k&ouml;nnen oder auf Vorlesesoftware angewiesen sind. Diese Erkl&auml;rung gebe ich freiwillig ab.</p>

<p>Sie gilt f&uuml;r den Internetauftritt unter <a href="https://www.heilpraktiker-fiebich.de">www.heilpraktiker-fiebich.de</a>.</p>

<h2>Stand der Umsetzung</h2>

<p>Die Seite wurde im September 2026 &uuml;berarbeitet und orientiert sich an den Web Content Accessibility Guidelines (WCAG) 2.1 in der Stufe AA. Sie ist mit diesen Anforderungen <strong>weitgehend vereinbar</strong>. Umgesetzt wurden unter anderem:</p>

<ul>
	<li>Die gesamte Seite l&auml;sst sich ohne Maus allein mit der Tastatur bedienen &ndash; einschlie&szlig;lich Hauptmen&uuml;, Untermen&uuml;s und der Kacheln auf der Startseite.</li>
	<li>Die Stelle, an der man sich beim Bedienen mit der Tastatur gerade befindet, ist deutlich sichtbar markiert.</li>
	<li>Eine Sprungmarke f&uuml;hrt beim ersten Tastendruck direkt zum Inhalt, ohne dass man sich durch das Men&uuml; arbeiten muss.</li>
	<li>Die Seite l&auml;sst sich auf dem Mobilger&auml;t frei vergr&ouml;&szlig;ern.</li>
	<li>Die Seitenbereiche sind f&uuml;r Vorlesesoftware als solche ausgezeichnet, die Sprache der Seite ist hinterlegt.</li>
	<li>&Uuml;berschriften sind in einer durchg&auml;ngigen Gliederung aufgebaut, ohne &uuml;bersprungene Ebenen.</li>
	<li>Bilder mit Aussagekraft &ndash; etwa die Zertifikate und die Abbildung zur HRV-Messung &ndash; haben eine Textalternative.</li>
</ul>

<h2>Nicht oder noch nicht barrierefreie Inhalte</h2>

<p><strong>Farbkontraste</strong><br />
Das Orange der Praxisfarbe erreicht in Schrift und Rahmen nicht den geforderten Kontrastwert. Betroffen sind Verweise, Schaltfl&auml;chen und die Pfadanzeige. Eine dunklere Variante der Farbe ist in Vorbereitung.</p>

<p><strong>Inhalte anderer Anbieter</strong><br />
Die Karte auf der Kontaktseite (Google Maps) und die Anzeige der Bewertungen (Trustindex) stammen von externen Anbietern. Auf deren Barrierefreiheit habe ich keinen Einfluss. Beide Inhalte werden erst nach Ihrer Zustimmung geladen; alle Angaben zur Anfahrt stehen zus&auml;tzlich als Text auf der Kontaktseite, so dass die Karte f&uuml;r die Nutzung der Seite nicht erforderlich ist.</p>

<p><strong>PDF-Dateien</strong><br />
Die Formulare und Merkbl&auml;tter im Bereich Downloads sind nicht barrierefrei aufbereitet. Wenn Sie ein Dokument in einer anderen Form ben&ouml;tigen, melden Sie sich bitte &ndash; ich stelle Ihnen den Inhalt dann auf einem anderen Weg zur Verf&uuml;gung.</p>

<h2>Erstellung dieser Erkl&auml;rung</h2>

<p>Diese Erkl&auml;rung wurde am 17.09.2026 erstellt. Grundlage ist eine Pr&uuml;fung aller Seiten durch die beauftragte Agentur, zuletzt gepr&uuml;ft am 17.09.2026.</p>

<h2>Ihre R&uuml;ckmeldung</h2>

<p>Sie sind auf eine Barriere gesto&szlig;en oder ben&ouml;tigen einen Inhalt in einer zug&auml;nglichen Form? Bitte melden Sie sich &ndash; ich k&uuml;mmere mich darum:</p>

<p><strong>Christian Fiebich</strong><br />
Naturheilpraxis &ndash; Heilpraktiker<br />
Rebenstra&szlig;e 67<br />
95326 Kulmbach<br />
Telefon: <a href="tel:+4992214079882">09221 4079882</a><br />
E-Mail: <a href="mailto:mail@heilpraktiker-fiebich.de">mail@heilpraktiker-fiebich.de</a></p>',
       UNIX_TIMESTAMP(), 43, 0, 0
  FROM DUAL WHERE @seite > 0;

SET @fliesstext = IF(@seite > 0, LAST_INSERT_ID(), 0);

-- Bannerzeile mit der Überschrift ---------------------------------------------
INSERT INTO main_page_link (
    main_page_id, main_sitepart_id, main_sitepart_header_id,
    main_collection_list, main_collection_id, main_collection_setup_id,
    main_collection_page_list_id, main_collection_items, main_collection_view_type,
    main_page_group, main_page_group_code, main_page_group_name, main_page_group_link,
    main_page_link_parent_id, modified_user, modified_date, active,
    validity_from, validity_to, layout_area_id, layout_class_id,
    background_image_path, sorting
)
SELECT @seite, 1, @ueberschrift,
       0, 0, 0, 0, 0, 0,
       0, '', '', '',
       0, 43, UNIX_TIMESTAMP(), 1,
       NULL, NULL, 180, 0,
       '/userdata/images/Banner/rechtliches.jpg', 1
  FROM DUAL WHERE @seite > 0;

-- Weiße Box als Behälter -------------------------------------------------------
INSERT INTO main_page_link (
    main_page_id, main_sitepart_id, main_sitepart_header_id,
    main_collection_list, main_collection_id, main_collection_setup_id,
    main_collection_page_list_id, main_collection_items, main_collection_view_type,
    main_page_group, main_page_group_code, main_page_group_name, main_page_group_link,
    main_page_link_parent_id, modified_user, modified_date, active,
    validity_from, validity_to, layout_area_id, layout_class_id,
    background_image_path, sorting
)
SELECT @seite, 0, 0,
       0, 0, 0, 0, 0, 0,
       1, 'box--white', 'Box', '',
       0, 43, UNIX_TIMESTAMP(), 1,
       NULL, NULL, 179, 0,
       '', 2
  FROM DUAL WHERE @seite > 0;

SET @box = IF(@seite > 0, LAST_INSERT_ID(), 0);

-- Fließtext in die Box ---------------------------------------------------------
INSERT INTO main_page_link (
    main_page_id, main_sitepart_id, main_sitepart_header_id,
    main_collection_list, main_collection_id, main_collection_setup_id,
    main_collection_page_list_id, main_collection_items, main_collection_view_type,
    main_page_group, main_page_group_code, main_page_group_name, main_page_group_link,
    main_page_link_parent_id, modified_user, modified_date, active,
    validity_from, validity_to, layout_area_id, layout_class_id,
    background_image_path, sorting
)
SELECT @seite, 1, @fliesstext,
       0, 0, 0, 0, 0, 0,
       0, '', '', '',
       @box, 43, UNIX_TIMESTAMP(), 1,
       NULL, NULL, 179, 0,
       '', 1
  FROM DUAL WHERE @seite > 0;

-- Menüeintrag unter "Info" -----------------------------------------------------
-- sorting 6 stellt die Seite hinter Impressum ans Ende des Bereichs.
INSERT INTO main_navigation (
    main_site_id, main_language_id, parent_id, sorting, level,
    code, menu_name, title_name,
    meta_keywords, meta_description,
    active, modified_date,
    forward_navigation_id, forward, forward_type, forward_page_id,
    forward_url, forward_shop_category, hidden, is_landing_page
)
SELECT 45, 53, 765, 6, 2,
       'barrierefreiheit', 'Barrierefreiheit', 'Erklärung zur Barrierefreiheit',
       '', '',
       1, NOW(),
       0, 0, 1, @seite,
       '', 0, 0, 0
  FROM DUAL WHERE @seite > 0;

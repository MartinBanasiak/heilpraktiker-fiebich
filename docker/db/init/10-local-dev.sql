-- Lokale Dev-Anpassungen nach dem Dump-Import.
--
-- Die Site wird in dc/frontend/frontend.php per $_SERVER['SERVER_NAME'] gegen
-- main_site.site_url aufgeloest. Ohne diesen Patch liefert http://localhost:8080/
-- immer die 404-Seite der Anwendung.
UPDATE main_site SET site_url = 'localhost' WHERE site_url = 'www.heilpraktiker-fiebich.de';

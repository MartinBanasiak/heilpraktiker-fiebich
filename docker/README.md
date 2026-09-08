# Lokale Docker-Umgebung

Startet die Seite lokal unter http://localhost:8080

## Starten

    docker compose up -d
    docker compose exec web composer install    # nur beim ersten Mal (vendor/ fehlt im Repo)

Beim allerersten Start importiert MariaDB automatisch alle `web2105*.sql`-Dumps
aus dem Projekt-Root (Schema zuerst, dann Content) und danach die Patches aus
`docker/db/init/`. Das dauert ein bis zwei Minuten.

## Dienste

| Dienst   | URL / Port              | Zweck                          |
|----------|-------------------------|--------------------------------|
| web      | http://localhost:8080   | Apache + PHP 7.4 (mod_php)     |
| mysql    | localhost:3307          | MariaDB 10.11, DB `web2105`    |
| mailpit  | http://localhost:8025   | faengt ausgehende Mails ab     |
| redis    | localhost:6379          | Cache                          |

DB-Login: `root` / `123`

## Wichtig zu wissen

**PHP 7.4 statt 7.1.** Fuer PHP 7.1 gibt es keine Images fuer Apple Silicon.
7.4 ist die letzte Version, die den Code ohne Anpassungen ausfuehrt (`short_open_tag`,
kein strict typing). PHP 8 wuerde an vielen Stellen brechen.

**Warum `docker/php/dev-prepend.php`.** PHP 7.4 meldet Dinge, die 7.1 still
geschluckt hat. `dc/frontend/frontend.php:30` setzt `display_errors` hart auf "On",
noch bevor die Session startet - die Ausgabe schickt dann die HTTP-Header zu frueh
raus und `session_start()` scheitert. Ergebnis waere ein Fatal Error auf allen
Unterseiten. Der Prepend-Handler schreibt Notices deshalb nur ins Log:

    docker compose logs -f web

**Site-Aufloesung.** Die Anwendung sucht die Site ueber `$_SERVER['SERVER_NAME']`
in `main_site.site_url`. `docker/db/init/10-local-dev.sql` setzt diesen Wert lokal
auf `localhost`. Auf dem Server bleibt `www.heilpraktiker-fiebich.de` unveraendert.

**Zuruecksetzen.** `docker compose down -v` loescht das DB-Volume; der naechste
`up` importiert die Dumps neu.

# Werkzeuge

## webp-convert.php

Erzeugt neben jedem JPEG und PNG unter `userdata/` eine `.webp`-Variante.
Ausgeliefert wird sie über die Regeln in der `.htaccess`, sobald der Browser
das Format akzeptiert — gleiche Adresse, gleiche Abmessungen, kleinere Datei.

### Warum das ein Cron sein muss

Das CMS wandelt hochgeladene Bilder **nicht** von selbst um. Ohne
wiederkehrenden Lauf gilt: Jedes Bild, das die Redaktion ab jetzt einpflegt,
geht unkomprimiert raus. Es geht nichts kaputt — der Server liefert dann eben
das Original — aber der Vorteil verfällt mit jedem neuen Bild ein Stück, ohne
dass es jemand bemerkt.

Der Lauf ist billig: Bestehende Varianten werden übersprungen, erkannt am
Änderungsdatum. Über 320 Bilder dauert ein Leerlauf Sekundenbruchteile.

### Einmalig prüfen

Der Konverter braucht GD mit WebP-Unterstützung. Fehlt sie, bricht er mit
einer eindeutigen Meldung ab statt stillschweigend nichts zu tun:

    php -r 'var_dump(function_exists("imagewebp"));'

### Erster Lauf

Erst ansehen, was passieren würde:

    php tools/webp-convert.php --dry-run

Dann umwandeln:

    php tools/webp-convert.php

### Als Cron

    0 3 * * * cd /pfad/zum/projekt && php tools/webp-convert.php --quiet

`--quiet` unterdrückt die Zeile je Datei und schweigt vollständig, wenn es
nichts zu tun gab. Gemeldet wird nur, was erzeugt wurde oder schiefging;
im Fehlerfall ist der Exit-Code 1. Ein Cron, der jede Nacht eine inhaltslose
Mail schickt, wird nach zwei Wochen weggefiltert — und fällt dann auch dann
niemandem mehr auf, wenn er etwas Wichtiges meldet.

### Stolperfalle: Release-Verzeichnisse

Die Auslieferung läuft über `prodeploy` mit Release-Ordnern und einem Symlink
auf die aktive Version. Der Cron-Pfad muss deshalb auf den **Symlink** zeigen,
nicht auf ein konkretes Release — sonst läuft er nach dem nächsten Deployment
in einem alten Verzeichnis weiter.

Zweite Frage im selben Zusammenhang: Liegt `userdata/` als gemeinsames
Verzeichnis neben den Releases und wird hineinverlinkt? Dann überleben die
`.webp`-Dateien jedes Deployment und der Einmal-Lauf genügt. Liegt `userdata/`
dagegen in jedem Release, sind sie nach dem nächsten Deployment weg und müssen
neu erzeugt werden — der Cron holt das dann in der folgenden Nacht nach.
Vor dem ersten Cron einmal nachsehen, welcher Fall zutrifft:

    ls -la /pfad/zum/projekt/userdata

### Optionen

| Option | Wirkung |
|---|---|
| `--dry-run` | Nur zeigen, was erzeugt würde |
| `--quiet` | Nur melden, wenn etwas erzeugt wurde oder fehlschlug |
| `--force` | Auch bestehende Varianten neu erzeugen |
| `--quality=78` | Qualität abweichend von 82 setzen |
| `userdata/images` | Anderer Startordner |

Wird eine Variante größer als das Original, verwirft der Konverter sie wieder —
sonst würde die `.htaccess` ausgerechnet die größere Datei bevorzugt
ausliefern.

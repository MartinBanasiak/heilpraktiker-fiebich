# Werkzeuge

## webp-convert.php

Erzeugt neben jedem JPEG und PNG unter `userdata/` eine `.webp`-Variante.
Ausgeliefert wird sie über die Regeln in der `.htaccess`, sobald der Browser
das Format akzeptiert — gleiche Adresse, gleiche Abmessungen, kleinere Datei.

### Wofür dieses Skript noch da ist

Neu hochgeladene Bilder erledigt inzwischen `webp-serve.php` von selbst (siehe
unten). Dieses Skript ist für den **Bestand** gedacht: einmal über alle
vorhandenen Bilder laufen lassen, statt darauf zu warten, dass jedes einzelne
zum ersten Mal abgerufen wird.

Der Lauf ist billig: Bestehende Varianten werden übersprungen, erkannt am
Änderungsdatum. Über 320 Bilder dauert ein Leerlauf Sekundenbruchteile — er
lässt sich also gefahrlos wiederholen.

### Einmalig prüfen

Der Konverter braucht GD mit WebP-Unterstützung. Fehlt sie, bricht er mit
einer eindeutigen Meldung ab statt stillschweigend nichts zu tun:

    php -r 'var_dump(function_exists("imagewebp"));'

### Erster Lauf

Erst ansehen, was passieren würde:

    php tools/webp-convert.php --dry-run

Dann umwandeln:

    php tools/webp-convert.php

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


## webp-serve.php

Erzeugt eine fehlende WebP-Variante **beim ersten Abruf** und liefert sie aus.
Damit wandelt sich jedes neu hochgeladene Bild von selbst um, ohne Cron und
ohne dass jemand daran denken muss.

### Ablauf

Die `.htaccess` hat zwei Regeln. Die erste liefert eine vorhandene Variante
aus. Greift sie nicht, prüft die zweite, ob der Browser WebP akzeptiert, die
angefragte Datei existiert, ein JPEG oder PNG ist und daneben noch keine
Variante liegt — dann landet die Anfrage in diesem Skript. Es wandelt einmalig
um, legt die Datei ab und liefert sie aus. Jeder weitere Abruf läuft am Skript
vorbei, weil dann die erste Regel greift.

Wirksam ist das nur unterhalb von `userdata/`. Was unter `layout/` liegt,
gehört zum Build und wird dort behandelt.

### Warum nicht am Upload

Der naheliegende Ort wäre ein Hook im Datei-Manager. Dagegen sprechen zwei
Dinge: Dessen `FILE_UPLOAD`-Ereignis feuert, *bevor* die Datei geschrieben
ist — der Hook müsste den Zielpfad vorwegnehmen. Und der Datei-Manager liegt
unter `plugins/ckfinder/` als Fremdcode; ein Update würde die Erweiterung
mitnehmen, ohne dass es auffällt, weil nichts kaputtgeht — es wären nur wieder
alle Bilder unkomprimiert.

An der Auslieferung ist dagegen jeder Weg abgedeckt, auf dem ein Bild ins
System kommt: über die Redaktion, per FTP oder beim Einspielen einer Sicherung.

### Verhalten im Fehlerfall

Das Skript liefert nie eine Fehlerseite. Fehlt GD mit WebP, ist die Datei
unlesbar oder fehlen Schreibrechte, geht das Original raus — der Besucher
sieht sein Bild, nur eben größer.

Fällt die Variante größer aus als das Original, wird sie nicht abgelegt;
sonst würde die `.htaccess` ausgerechnet die größere Datei bevorzugt
ausliefern. Stattdessen entsteht eine leere Merkdatei `<bild>.kein-webp`,
damit der nächste Abruf die Umwandlung nicht erneut rechnet. Wird das Bild
später ersetzt, ist die Merkdatei älter als das Bild und es wird neu
entschieden.

### Sicherheit

Das Skript ist direkt aufrufbar und nimmt einen Pfad aus der URL entgegen.
Geprüft wird in dieser Reihenfolge: Präfix `userdata/`, kein Null-Byte,
`realpath()` — erst danach lässt sich feststellen, ob die Datei wirklich
unterhalb von `userdata/` liegt, weil eine Prüfung auf der rohen Zeichenkette
zu umgehen wäre. Dann Endungs-Whitelist und `is_file()`.

Gegengeprüft mit `../config/.env`, `userdata/../config/.env`,
`userdata/../../etc/passwd`, kodierten Varianten sowie PHP- und
`.webp`-Pfaden: alle 404.

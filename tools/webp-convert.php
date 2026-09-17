#!/usr/bin/env php
<?php
/**
 * Erzeugt WebP-Varianten fuer alle Bilder unter userdata/.
 *
 * Hintergrund: Die Bilder liegen als unbearbeitete Kamera- und
 * Export-Dateien im CMS - einzelne PNG mit knapp einem Megabyte, die im
 * Layout auf 200 Pixel skaliert angezeigt werden. Das ist der groesste
 * Hebel fuer die Ladezeit.
 *
 * Umgesetzt als reine Formatumwandlung: gleiche Bildabmessungen, gleicher
 * Dateiname plus ".webp". Ausgeliefert wird die Variante ueber die Regeln in
 * der .htaccess, sobald der Browser sie akzeptiert. Damit muss kein einziger
 * Redaktionsinhalt angefasst werden - die <img>-Tags stehen als
 * CKEditor-HTML in der Datenbank und waeren sonst alle einzeln zu aendern.
 *
 * Verwendung:
 *   php tools/webp-convert.php                 # alles unter userdata/
 *   php tools/webp-convert.php --dry-run       # nur zeigen, was passieren wuerde
 *   php tools/webp-convert.php --quality=78    # Qualitaet abweichend setzen
 *   php tools/webp-convert.php --force         # auch bestehende neu erzeugen
 *   php tools/webp-convert.php --quiet         # nur melden, wenn etwas passiert
 *   php tools/webp-convert.php userdata/images # anderer Startordner
 *
 * Im Docker-Container:
 *   docker compose exec web php tools/webp-convert.php
 *
 * Neu hochgeladene Bilder wandelt das CMS NICHT von sich aus um. Damit der
 * Vorteil nicht mit jedem Redaktionsbild ein Stueck weiter verfaellt, gehoert
 * dieser Aufruf in einen Cron - der Lauf ist billig, weil er bestehende
 * Varianten ueberspringt. Mit --quiet meldet er sich nur, wenn er etwas
 * erzeugt hat oder etwas schiefging; ein Cron, der jede Nacht eine Mail ohne
 * Inhalt schickt, wird nach zwei Wochen weggefiltert und faellt dann auch im
 * Fehlerfall niemandem mehr auf.
 */

const STANDARD_QUALITAET = 82;
const STANDARD_ORDNER = 'userdata';

$optionen = [
    'dry-run' => false,
    'force' => false,
    'quiet' => false,
    'quality' => STANDARD_QUALITAET,
];
$ordner = null;

foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--dry-run') {
        $optionen['dry-run'] = true;
    } elseif ($argument === '--force') {
        $optionen['force'] = true;
    } elseif ($argument === '--quiet') {
        $optionen['quiet'] = true;
    } elseif (strpos($argument, '--quality=') === 0) {
        $optionen['quality'] = (int)substr($argument, strlen('--quality='));
    } elseif (strpos($argument, '--') === 0) {
        fwrite(STDERR, "Unbekannte Option: $argument\n");
        exit(1);
    } else {
        $ordner = rtrim($argument, '/');
    }
}

if ($optionen['quality'] < 1 || $optionen['quality'] > 100) {
    fwrite(STDERR, "Qualitaet muss zwischen 1 und 100 liegen.\n");
    exit(1);
}

$wurzel = dirname(__DIR__);
$startOrdner = $ordner !== null ? $ordner : $wurzel . '/' . STANDARD_ORDNER;

if (!is_dir($startOrdner)) {
    fwrite(STDERR, "Ordner nicht gefunden: $startOrdner\n");
    exit(1);
}

if (!function_exists('imagewebp')) {
    fwrite(STDERR, "Diese PHP-Installation kann kein WebP schreiben.\n");
    fwrite(STDERR, "GD muss mit WebP-Unterstuetzung gebaut sein (--with-webp).\n");
    exit(1);
}

$zaehler = ['erzeugt' => 0, 'uebersprungen' => 0, 'fehler' => 0];
$bytesVorher = 0;
$bytesNachher = 0;

$dateien = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($startOrdner, FilesystemIterator::SKIP_DOTS)
);

foreach ($dateien as $datei) {
    if (!$datei->isFile()) {
        continue;
    }

    $endung = strtolower($datei->getExtension());
    if (!in_array($endung, ['jpg', 'jpeg', 'png'], true)) {
        continue;
    }

    $quelle = $datei->getPathname();
    $ziel = $quelle . '.webp';

    // Bereits vorhanden und aktuell? Dann nichts tun.
    if (!$optionen['force'] && is_file($ziel) && filemtime($ziel) >= filemtime($quelle)) {
        $zaehler['uebersprungen']++;
        continue;
    }

    $relativ = ltrim(str_replace($wurzel, '', $quelle), '/');

    if ($optionen['dry-run']) {
        printf("wuerde erzeugen: %s\n", $relativ . '.webp');
        $zaehler['erzeugt']++;
        continue;
    }

    $bild = ladeBild($quelle, $endung);
    if ($bild === null) {
        fwrite(STDERR, "  nicht lesbar: $relativ\n");
        $zaehler['fehler']++;
        continue;
    }

    // Transparenz erhalten - sonst werden freigestellte PNG schwarz.
    imagepalettetotruecolor($bild);
    imagealphablending($bild, false);
    imagesavealpha($bild, true);

    $erfolg = imagewebp($bild, $ziel, $optionen['quality']);
    imagedestroy($bild);

    if (!$erfolg) {
        fwrite(STDERR, "  Umwandlung fehlgeschlagen: $relativ\n");
        $zaehler['fehler']++;
        continue;
    }

    $vorher = filesize($quelle);
    $nachher = filesize($ziel);

    // Groesser geworden? Dann bringt die Variante nichts und wuerde ueber die
    // .htaccess sogar bevorzugt ausgeliefert. Also wieder weg damit.
    if ($nachher >= $vorher) {
        unlink($ziel);
        if (!$optionen['quiet']) {
            printf("  uebersprungen (WebP waere groesser): %s\n", $relativ);
        }
        $zaehler['uebersprungen']++;
        continue;
    }

    $bytesVorher += $vorher;
    $bytesNachher += $nachher;
    $zaehler['erzeugt']++;

    if (!$optionen['quiet']) {
        printf(
            "%-58s %7s -> %7s  (%d%% kleiner)\n",
            mb_strimwidth($relativ, 0, 58, '...'),
            formatiereBytes($vorher),
            formatiereBytes($nachher),
            (int)round((1 - $nachher / $vorher) * 100)
        );
    }
}

// Im Cron-Betrieb nur melden, wenn es etwas zu melden gibt. Ein Lauf, der
// nichts gefunden hat, schweigt - jede Ausgabe wuerde sonst eine Mail
// ausloesen und die echten Meldungen im Rauschen begraben.
$stillhalten = $optionen['quiet'] && $zaehler['erzeugt'] === 0 && $zaehler['fehler'] === 0;

if (!$stillhalten) {
    if (!$optionen['quiet']) {
        echo str_repeat('-', 100), "\n";
    }
    printf(
        "erzeugt: %d   uebersprungen: %d   Fehler: %d\n",
        $zaehler['erzeugt'],
        $zaehler['uebersprungen'],
        $zaehler['fehler']
    );

    if ($bytesVorher > 0) {
        printf(
            "Uebertragungsvolumen dieser Bilder: %s statt %s - %d%% weniger\n",
            formatiereBytes($bytesNachher),
            formatiereBytes($bytesVorher),
            (int)round((1 - $bytesNachher / $bytesVorher) * 100)
        );
    }
}

exit($zaehler['fehler'] > 0 ? 1 : 0);

/**
 * @return resource|GdImage|null
 */
function ladeBild($pfad, $endung)
{
    $bild = false;

    if ($endung === 'png') {
        $bild = @imagecreatefrompng($pfad);
    } else {
        $bild = @imagecreatefromjpeg($pfad);
    }

    return $bild === false ? null : $bild;
}

function formatiereBytes($bytes)
{
    if ($bytes >= 1048576) {
        return sprintf('%.1f MB', $bytes / 1048576);
    }

    return sprintf('%.0f KB', $bytes / 1024);
}

<?php
/**
 * Erzeugt eine fehlende WebP-Variante beim ersten Abruf und liefert sie aus.
 *
 * Warum an der Auslieferung und nicht am Upload:
 *
 * Der naheliegende Ort waere ein Hook im Datei-Manager. Dagegen sprechen zwei
 * Dinge. Erstens feuert dessen FILE_UPLOAD-Ereignis, BEVOR die Datei
 * geschrieben ist - der Hook muesste den Zielpfad vorwegnehmen. Zweitens liegt
 * der Datei-Manager unter plugins/ckfinder/ als Fremdcode; ein Update wuerde
 * die Erweiterung mitnehmen, und niemand wuerde es merken, weil nichts kaputt
 * geht - es waeren nur wieder alle Bilder unkomprimiert.
 *
 * Hier greift stattdessen die .htaccess: Akzeptiert der Browser WebP, liegt
 * aber keine Variante neben dem Bild, landet die Anfrage in diesem Skript. Es
 * wandelt einmalig um, legt die Datei ab und liefert sie aus. Jeder weitere
 * Abruf laeuft an diesem Skript vorbei, weil dann die statische Regel greift.
 *
 * Damit ist jeder Weg abgedeckt, auf dem ein Bild ins System kommt - ueber die
 * Redaktion, per FTP oder beim Einspielen einer Sicherung.
 *
 * Cache-Control setzt das Skript bewusst nicht selbst: mod_expires vergibt den
 * Wert anhand des Content-Type, und beide zusammen ergaben einen doppelten
 * Header ("max-age=2592000, public, max-age=2592000").
 *
 * Grundsatz: Dieses Skript darf nie ein Fehlerbild liefern. Geht irgendetwas
 * schief - kein GD, keine Schreibrechte, kaputte Datei - wird das Original
 * ausgeliefert. Der Besucher sieht sein Bild, nur eben groesser.
 */

const ERLAUBTE_ENDUNGEN = ['jpg', 'jpeg', 'png'];
// Endung der Merkdatei fuer Bilder, bei denen WebP groesser ausfaellt als das
// Original. Ohne sie wuerde jeder Abruf eines solchen Bildes die Umwandlung
// erneut rechnen - es entsteht ja nie eine .webp, an der die Regel in der
// .htaccess kuenftig vorbeifuehren koennte.
const MERKER_ENDUNG = '.kein-webp';
const QUALITAET = 82;
const ORDNER = 'userdata';

$wurzel = dirname(__DIR__);

/**
 * Liefert das Original aus und beendet die Ausfuehrung.
 */
function liefereOriginal($pfad)
{
    if (!is_file($pfad)) {
        header('HTTP/1.1 404 Not Found');
        exit;
    }

    $typen = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];
    $endung = strtolower(pathinfo($pfad, PATHINFO_EXTENSION));

    header('Content-Type: ' . ($typen[$endung] ?? 'application/octet-stream'));
    header('Content-Length: ' . filesize($pfad));
    header('Vary: Accept');
    readfile($pfad);
    exit;
}

// Angefragter Pfad. Die .htaccess reicht ihn als Parameter durch; verlassen
// duerfen wir uns darauf nicht.
$angefragt = isset($_GET['bild']) ? (string)$_GET['bild'] : '';

if ($angefragt === '') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

// Prefix-Pruefung vor realpath(): Ein Pfad, der gar nicht auf userdata/ zeigt,
// soll erst gar nicht aufgeloest werden.
if (strpos($angefragt, ORDNER . '/') !== 0 || strpos($angefragt, "\0") !== false) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$quelle = realpath($wurzel . '/' . $angefragt);

// realpath() loest ".." und Symlinks auf. Erst danach laesst sich pruefen, ob
// die Datei wirklich unterhalb von userdata/ liegt - eine Pruefung auf der
// rohen Zeichenkette waere zu umgehen.
$erlaubteWurzel = realpath($wurzel . '/' . ORDNER);
if ($quelle === false || $erlaubteWurzel === false
    || strpos($quelle, $erlaubteWurzel . DIRECTORY_SEPARATOR) !== 0) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$endung = strtolower(pathinfo($quelle, PATHINFO_EXTENSION));
if (!in_array($endung, ERLAUBTE_ENDUNGEN, true) || !is_file($quelle)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

// Ab hier ist die Quelle gueltig. Jeder weitere Fehler fuehrt zum Original,
// nicht zu einer Fehlerseite.
if (!function_exists('imagewebp')) {
    liefereOriginal($quelle);
}

$ziel = $quelle . '.webp';
$merker = $quelle . MERKER_ENDUNG;

// Schon einmal festgestellt, dass sich WebP fuer dieses Bild nicht lohnt?
// Dann das Original ausliefern, ohne es erneut zu berechnen. Die Merkdatei
// gilt nur, solange sie juenger ist als das Bild - wird das Bild ersetzt,
// wird neu entschieden.
if (is_file($merker) && filemtime($merker) >= filemtime($quelle)) {
    liefereOriginal($quelle);
}

// Zwischen der Pruefung in der .htaccess und diesem Aufruf kann ein paralleler
// Abruf die Variante bereits erzeugt haben.
if (is_file($ziel) && filemtime($ziel) >= filemtime($quelle)) {
    header('Content-Type: image/webp');
    header('Content-Length: ' . filesize($ziel));
    header('Vary: Accept');
    readfile($ziel);
    exit;
}

$bild = $endung === 'png' ? @imagecreatefrompng($quelle) : @imagecreatefromjpeg($quelle);
if (!$bild) {
    liefereOriginal($quelle);
}

// Transparenz erhalten - sonst werden freigestellte PNG schwarz.
imagepalettetotruecolor($bild);
imagealphablending($bild, false);
imagesavealpha($bild, true);

// Erst in eine Nachbardatei schreiben, dann umbenennen. Ein gleichzeitiger
// Abruf sieht dadurch entweder die alte Lage oder die fertige Datei, nie eine
// halb geschriebene. rename() ist innerhalb desselben Dateisystems atomar.
$temporaer = $ziel . '.' . getmypid() . '.tmp';
$erfolg = @imagewebp($bild, $temporaer, QUALITAET);
imagedestroy($bild);

if (!$erfolg || !is_file($temporaer)) {
    @unlink($temporaer);
    liefereOriginal($quelle);
}

// Groesser als das Original? Dann bringt die Variante nichts - abgelegt wird
// sie nicht, sonst wuerde die .htaccess ausgerechnet die groessere Datei
// bevorzugt ausliefern. Stattdessen eine leere Merkdatei, damit der naechste
// Abruf die Umwandlung nicht erneut rechnet.
if (filesize($temporaer) >= filesize($quelle)) {
    @unlink($temporaer);
    @touch($merker);
    liefereOriginal($quelle);
}

// Der Lauf war erfolgreich. Eine Merkdatei aus einem frueheren Versuch hat
// sich damit erledigt - sie stammt dann von einer aelteren Fassung des Bildes.
if (is_file($merker)) {
    @unlink($merker);
}

if (!@rename($temporaer, $ziel)) {
    // Keine Schreibrechte im Zielordner. Einmal ausliefern kostet nichts,
    // dauerhaft abgelegt wird nichts - beim naechsten Abruf derselbe Weg.
    $daten = @file_get_contents($temporaer);
    @unlink($temporaer);
    if ($daten === false) {
        liefereOriginal($quelle);
    }
    header('Content-Type: image/webp');
    header('Content-Length: ' . strlen($daten));
    header('Vary: Accept');
    echo $daten;
    exit;
}

header('Content-Type: image/webp');
header('Content-Length: ' . filesize($ziel));
header('Vary: Accept');
readfile($ziel);

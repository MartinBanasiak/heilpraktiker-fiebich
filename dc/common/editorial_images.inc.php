<?php
/*
 * Bild-Tags aus dem Redaktionsinhalt anreichern.
 *
 * Aus dem Briefing:
 *   "Ideal: WebP/AVIF, passende Bildabmessungen, width/height festlegen und
 *    Bilder ausserhalb des sichtbaren Startbereichs lazy-loaden."
 *
 * Das Format erledigt die WebP-Auslieferung in der .htaccess. Hier geht es um
 * die beiden anderen Punkte, und die brauchen Attribute im Markup:
 *
 *   width/height  Ohne diese Angaben kennt der Browser das Seitenverhaeltnis
 *                 erst, wenn das Bild geladen ist. Bis dahin hat das Bild die
 *                 Hoehe null, und der Text darunter springt beim Nachladen
 *                 nach unten. Das ist der Layout-Shift, den die Core Web
 *                 Vitals messen.
 *   loading=lazy  Bilder weit unten werden erst geladen, wenn sie in die
 *                 Naehe des Sichtfelds kommen.
 *
 * Die <img>-Tags stammen aus dem CKEditor und liegen in der Datenbank. Statt
 * jedes einzelne Bild in der Redaktion nachzupflegen - und das bei jedem neuen
 * Bild aufs Neue - ergaenzt dieser Filter die Angaben beim Ausliefern.
 *
 * Aufgerufen aus module/textcontent/textcontent.php.
 */

/**
 * Ergaenzt width, height, loading und decoding an <img>-Tags.
 *
 * @param string $html Redaktionelles HTML aus textcontent_header.content
 * @return string
 */
function dc_enrich_editorial_images($html)
{
    // Im Live-Edit-Modus nicht anfassen: Was hier ergaenzt wird, landet sonst
    // beim Speichern dauerhaft im Datenbankinhalt.
    if (!empty($GLOBALS['live_edit_mode'])) {
        return $html;
    }

    if (strpos($html, '<img') === false) {
        return $html;
    }

    return preg_replace_callback(
        '/<img\b[^>]*>/i',
        'dc_enrich_single_image',
        $html
    );
}

/**
 * @param array $treffer
 * @return string
 */
function dc_enrich_single_image($treffer)
{
    static $inhaltsBildNummer = 0;

    $tag = $treffer[0];

    $quelle = dc_bild_quelle($tag);
    if ($quelle === null) {
        return $tag;
    }

    $ergaenzungen = '';

    // Abmessungen nur setzen, wenn die Datei lokal liegt und das Attribut
    // fehlt. Ein vorhandenes width/height stammt aus der Redaktion und hat
    // Vorrang.
    if (!preg_match('/\swidth\s*=/i', $tag) && !preg_match('/\sheight\s*=/i', $tag)) {
        $groesse = dc_bild_abmessungen($quelle);
        if ($groesse !== null) {
            $ergaenzungen .= ' width="' . $groesse[0] . '" height="' . $groesse[1] . '"';
        }
    }

    if (!preg_match('/\sloading\s*=/i', $tag)) {
        // Bilder in der Kopfzeile - Logo und die Icons - nie lazy laden, die
        // stehen immer im sichtbaren Bereich. Ebenso das erste Bild des
        // eigentlichen Inhalts: Das ist in aller Regel der groesste sichtbare
        // Inhalt der Seite, und den lazy zu laden verschlechtert genau die
        // Kennzahl, um die es hier geht.
        $imKopfbereich = !empty($GLOBALS['dc_bildbereich_kopf']);

        if ($imKopfbereich) {
            $ergaenzungen .= '';
        } else {
            $inhaltsBildNummer++;
            $ergaenzungen .= $inhaltsBildNummer === 1 ? '' : ' loading="lazy"';
        }
    }

    if (!preg_match('/\sdecoding\s*=/i', $tag)) {
        $ergaenzungen .= ' decoding="async"';
    }

    if ($ergaenzungen === '') {
        return $tag;
    }

    return rtrim(rtrim($tag, '>'), '/') . $ergaenzungen . ' />';
}

/**
 * Liest das src-Attribut und gibt den Dateipfad im Projekt zurueck.
 *
 * @param string $tag
 * @return string|null
 */
function dc_bild_quelle($tag)
{
    if (!preg_match('/\ssrc\s*=\s*"([^"]+)"/i', $tag, $treffer)) {
        return null;
    }

    $src = $treffer[1];

    // Nur eigene, absolute Pfade. Externe Bilder und data-URIs bleiben aussen vor.
    if ($src === '' || $src[0] !== '/' || strpos($src, '//') === 0) {
        return null;
    }

    return rawurldecode(parse_url($src, PHP_URL_PATH));
}

/**
 * @param string $pfad Pfad ab Projektwurzel, beginnend mit /
 * @return array|null [breite, hoehe]
 */
function dc_bild_abmessungen($pfad)
{
    static $bekannt = [];

    if (array_key_exists($pfad, $bekannt)) {
        return $bekannt[$pfad];
    }

    $bekannt[$pfad] = null;

    $wurzel = dirname(__DIR__, 2);
    $datei = $wurzel . $pfad;

    // Kein Ausbruch aus dem Projektordner
    $echterPfad = realpath($datei);
    if ($echterPfad === false || strpos($echterPfad, $wurzel) !== 0) {
        return null;
    }

    $groesse = @getimagesize($echterPfad);
    if ($groesse === false || empty($groesse[0]) || empty($groesse[1])) {
        return null;
    }

    $bekannt[$pfad] = [(int)$groesse[0], (int)$groesse[1]];

    return $bekannt[$pfad];
}

<?php
/*
 * Nur fuer die lokale Docker-Umgebung.
 *
 * Der Code ist fuer PHP 7.1 geschrieben, laeuft hier aber auf 7.4. PHP 7.4 meldet
 * Dinge wie "Trying to access array offset on value of type null", die 7.1 still
 * geschluckt hat. dc/frontend/frontend.php:30 setzt display_errors hart auf "On",
 * bevor die Session startet - die Ausgabe schickt dann die Header zu frueh raus und
 * session_start() scheitert (Fatal Error auf allen Unterseiten).
 *
 * Ein eigener Error-Handler faengt das ab: alles wandert ins Apache-Log
 * (docker compose logs -f web), nichts ins HTML. Die App registriert spaeter ihren
 * eigenen Handler (GeneralErrorExceptionHandling) und uebernimmt dann wieder.
 */
set_error_handler(function ($severity, $message, $file, $line) {
    // @-Operator und error_reporting respektieren
    if (!(error_reporting() & $severity)) {
        return true;
    }
    error_log(sprintf('PHP (dev-prepend): %s in %s on line %d', $message, $file, $line));

    return true; // true = PHP gibt den Fehler nicht selbst aus
});

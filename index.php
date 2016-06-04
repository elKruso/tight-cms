<?php
/**
 * tightCMS
 *
 * die Startdatei von tightCMS
 *
 * Startet die Main-Klasse,
 * ruft zur Arbeit auf,
 * und Beendet alles wieder.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      index.php
 * @package   tightCMS
 * @version   2.1.0
 */

use tightCMS\Main;

require __DIR__ . '/bootstrap.php';

// Starten der Klasse
$mainclass = new Main();
if ($mainclass != null) {
    // Cernel und Module aktivieren
    if ($mainclass->init()) {
        // Die Arbeit erledigen
        $mainclass->work(false);
        // Aufräumen
        $mainclass->quit();
    }
    // Speicher freigeben
    unset($mainclass);
}

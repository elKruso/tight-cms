<?php
/**
 * tightCMS
 *
 * die Administrationsdatei von tightCMS
 *
 * Startet die Main-Klasse,
 * Arbeitet,
 * und Beendet alles wieder.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      admin.php
 * @package   tightCMS
 * @version   2.0.0
 */

use tightCMS\Main;

require __DIR__ . '/bootstrap.php';

// Starten der Klasse
$mainclass = new Main();
if ($mainclass != null) {
    // Cernel und Module aktivieren
    if ($mainclass->init()) {
        // Die Arbeit erledigen
        $mainclass->work(true);
        // Aufräumen
        $mainclass->quit();
    }
    // Speicher freigeben
    unset($mainclass);
}

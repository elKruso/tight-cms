<?php
/**
 * interface ErrorLogging
 *
 * The interface for logging errors, Writes the log of the actions.
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      ErrorLogging.php
 * @package   tightCMS/Cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface ErrorLogging
 */
interface ErrorLogging extends Cernel
{
    /**
     * Starts the logging
     *
     * @param string $filename Filename to add the log to
     * @return boolean
     */
    public function startLogging($filename);

    /**
     * Closes the logging
     *
     * @return boolean
     */
    public function stopLogging();

    /**
     * Writes the content to the file
     * Formated as "$modulname (DATUM): $inhalt"
     *
     * @param int    $state     State of the logging
     * @param string $modulname Name of the module
     * @param string $inhalt    Contents of the log
     * @return boolean
     */
    public function writeLog($state, $modulname, $inhalt);

    /**
     * Clears the current logfile
     *
     * @return boolean
     */
    public function clearLog();
}

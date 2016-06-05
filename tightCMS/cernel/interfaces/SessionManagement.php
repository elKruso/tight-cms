<?php
/**
 * Interface SessionManagement
 *
 * The interface for sessionmanagement, manages data from the session.
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      SessionManagement.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface SessionManagement
 */
interface SessionManagement extends Cernel
{
    /**
     * Starts the session with an optionally given id
     *
     * @param string $sessionid The session id, or a new one if empty
     * @return boolean Success?
     */
    public function start($sessionid = '');

    /**
     * Stops the session
     *
     * @return boolean Success?
     */
    public function stop();

    /**
     * Saves the value of the fieldname
     *
     * @param string $fieldname The fieldname to save to
     * @param mixed  $content   The content to save
     * @return boolean Success?
     */
    public function setValue($fieldname, $content);

    /**
     * Loads the value from the fieldname
     *
     * @param string $fieldname The name of the fieldname to read from
     * @return mixed The content of the field
     */
    public function getValue($fieldname);

    /**
     * Dumps the full session to the screen
     *
     * @return string Data in HTML
     */
    public function dumbSession();
}

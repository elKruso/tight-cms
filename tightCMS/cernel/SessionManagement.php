<?php
/**
 * Class SessionManagement
 *
 * The class for the session management
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      SessionManagement.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\SessionManagement as SessionManagementInterface;
use tightCMS\cernel\abstracts\Cernel;

/**
 * Class SessionManagement
 */
class SessionManagement extends Cernel implements SessionManagementInterface
{
    /**
     * Name of the session-cookies
     * @var String
     */
    private $cookieName = 'tightCMS';

    /**
     * Session-id
     * @var String
     */
    private $sessionId;

    /**
     * Session-name
     * @var String
     */
    private $sessionName;

    /**
     * Initialisation of the variables
     */
    public function __construct()
    {
        $this->sessionId   = '';
        $this->sessionName = '';
    }

    /**
     * clean up
     */
    public function __destruct()
    {
        unset($this->sessionId);
        unset($this->sessionName);
    }

    /**
     * Starts the session with an optionally given id
     *
     * @param string $sessionid The session id, or a new one if empty
     * @return boolean Success?
     */
    public function start($sessionid = '')
    {
        if (! empty($sessionid)) {
            // Use given session id
            session_id($sessionid);
        } else {
            // Read session from cookie
            $sessionCookieId = filter_input(INPUT_COOKIE, $this->cookieName);
            if (! empty($sessionCookieId)) {
                session_id($sessionCookieId);
            }
        }
        session_start();
        setcookie($this->cookieName, session_id(), 0);

        $this->sessionId   = session_id();
        $this->sessionName = session_name();

        return $this;
    }

    /**
     * Stops the session
     *
     * @return boolean Success?
     */
    public function stop()
    {
        session_unset();
        $_SESSION = array();
        setcookie($this->cookieName, '', 0);

        return $this;
    }

    /**
     * Saves the value of the fieldname
     *
     * @param string $fieldname The fieldname to save to
     * @param mixed  $content   The content to save
     * @return boolean Success?
     */
    public function setValue($fieldname, $content)
    {
        if (! empty($this->sessionId)) {
            $_SESSION[$fieldname] = $content;
            return ($_SESSION[$fieldname] == $content);
        }

        return false;
    }

    /**
     * Loads the value from the fieldname
     *
     * @param string $fieldname The name of the fieldname to read from
     * @return mixed The content of the field, null on empty
     */
    public function getValue($fieldname)
    {
        if (! empty($this->sessionId)) {
            if (isset($_SESSION[$fieldname])) {
                return $_SESSION[$fieldname];
            }
        }

        return null;
    }

    /**
     * Dumps the full session to the screen
     *
     * @return string Data in HTML
     */
    public function dumbSession()
    {
        return '
SESSION-DATA: ' . $this->cookieName . '<br>
-------------<br>
<pre>' . print_r($_SESSION, true) . '</pre>';
    }
}

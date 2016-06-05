<?php
/**
 * Interface UrlAccess
 *
 * The interface for access via url
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      UrlAccess.php
 * @package   tightCMS/cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface UrlAccess
 */
interface UrlAccess extends Cernel
{
    /**
     * Set the username and password
     *
     * @param string $username The username
     * @param string $password The password
     * @return boolean Success?
     */
    public function setCredentials($username, $password);

    /**
     * Sets the identifier with which the system is names 
     * (as browser calls itself "firefox")
     *
     * @param string $identifier The name of the system
     * @return boolean Success?
     */
    public function setIdentifier($identifier);

    /**
     * Forces use of https
     *
     * @return void
     */
    public function forceSecure();

    /**
     * Opens the url and returns the content
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return string The content of the page
     */
    public function readUrl($url, array $parameter = array(), $timeout = 30);

    /**
     * Opens the url and decodes the returning JSON
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return array The content of the JSON
     */
    public function readJson($url, array $parameter = array(), $timeout = 30);

    /**
     * Opens the url and decodes the returning JSON as object
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return object The content of the JSON
     */
    public function readJsonObject($url, array $parameter = array(), $timeout = 30);

    /**
     * Opens the url and returns the content as "array"
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return array The content of the page as array
     */
    public function htmlContents($url, array $parameter = array(), $timeout = 30);

    /**
     * Returns the last error message
     *
     * @return string
     */
    public function getError();
}

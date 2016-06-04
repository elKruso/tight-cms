<?php
/**
 * Class UrlAccess
 *
 * Die Klasse für den URLAccess.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      UrlAccess.php
 * @package   tightCMS/cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\UrlAccess as UrlAccessInterface;
use tightCMS\cernel\abstracts\Cernel;
use PHPHtmlParser\Dom as HtmlParser;
use PHPHtmlParser\Dom\HtmlNode;

/**
 * class UrlAccess
 */
class UrlAccess 
    extends Cernel 
    implements UrlAccessInterface
{
    /**
     * The curl connector
     * @var curl
     */
    private $curl;

    /**
     * The name of the client
     * @var string
     */
    private $identifier = 'TightCMS';

    /**
     * The error message
     * @var string
     */
    private $errorMessage = '';

    /**
     * The credentials to access a site
     * @var array
     */
    private $credentials = array(
        'username' => '',
        'password' => ''
    );

    /**
     * Construct the curl
     */
    function __construct()
    {
        $this->curl = curl_init();

        \curl_setopt($this->curl, CURLOPT_USERAGENT, $this->identifier . ' (PHP ' . \phpversion() . ')');
        \curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($this->curl, CURLOPT_POST, true);
        \curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        \curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
    }

    /**
     * Closes the curl
     */
    function __destruct()
    {
        \curl_close($this->curl);
        
        unset($this->curl);
        unset($this->credentials);
        unset($this->identifier);
        unset($this->errorMessage);
    }

    /**
     * Set the username and password
     *
     * @param string $username The username
     * @param string $password The password
     * @return self
     */
    public function setCredentials($username, $password)
    {
        $this->credentials['username'] = $username;
        $this->credentials['password'] = $password;

        return $this;
    }

    /**
     * Sets the identifier with which the system is names 
     * (as browser calls itself "firefox")
     *
     * @param string $identifier The name of the system
     * @return boolean Success?
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Forces use of https
     *
     * @return self
     */
    public function forceSecure()
    {
        \curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1);
        \curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        
        return $this;
    }

    /**
     * Opens the url and returns the content
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return string The content of the page
     */
    public function readUrl($url, array $parameter = array(), $timeout = 30)
    {
        $this->setOptCredentials();
        \curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        \curl_setopt($this->curl, CURLOPT_URL, $url);
        if (count($parameter) > 0) {
            \curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameter);
        } else {
            \curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        }
        $resonance = \curl_exec($this->curl);

        if (false === $resonance) {
            $this->errorMessage = \curl_errno($this->curl) . ': '
                . \curl_error();
        }

        return $resonance;
    }

    /**
     * Opens the url and decodes the returning JSON
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return array The content of the JSON
     */
    public function readJson($url, array $parameter = array(), $timeout = 30)
    {
        return json_decode(
            $this->readUrl($url, $parameter, $timeout), 
            true
        );
    }

    /**
     * Opens the url and decodes the returning JSON as object
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return object The content of the JSON
     */
    public function readJsonObject($url, array $parameter = array(), $timeout = 30)
    {
        return json_decode(
            $this->readUrl($url, $parameter, $timeout)
        );
    }

    /**
     * Opens the url and returns the content as "array"
     *
     * @param string $url       The url to retrieve
     * @param array  $parameter The parameters to send
     * @param int    $timeout   The waiting time
     * @return array The content of the page as array
     */
    public function htmlContents($url, array $parameter = array(), $timeout = 30)
    {
        $parser = new HtmlParser();
        $parser->load(
            $this->readUrl($url, $parameter, $timeout)
        );

        return $parser->root;
    }

    /**
     * Returns the last error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * SetOpt for credentials
     *
     * @return self
     */
    private function setOptCredentials()
    {
        if (! empty($this->credentials['username']) &&
            ! empty($this->credentials['password'])) {
            \curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            \curl_setopt(
                $this->curl,
                CURLOPT_USERPWD,
                $this->credentials['username'] . ':' 
                    . $this->credentials['password']
            );
        }
        return $this;
    }
}

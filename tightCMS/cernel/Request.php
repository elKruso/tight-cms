<?php
/**
 * Class Request
 *
 * The call for the request
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Request.php
 * @package   tightCMS/cernel
 * @version   1.1.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\Request as RequestInterface;
use tightCMS\cernel\abstracts\Cernel;

/**
 * Class Request
 */
class Request extends Cernel implements RequestInterface
{
    /**
     * The content of _GET
     * @var Array
     */
    private $get;

    /**
     * The content of _POST
     * @var Array
     */
    private $post;

    /**
     * The content of _SERVER
     * @var Array
     */
    private $server;

    /**
     * Construct the Data
     * Initialize the variables
     */
    public function __construct()
    {
        $this->get    = $this->getDataFromSystem('GET');
        $this->post   = $this->getDataFromSystem('POST');
        $this->server = $this->getDataFromSystem('SERVER');
        
        $this->clearAllData();
    }

    /**
     * Retrieves the data from $_GET
     *
     * @param string $index The index to get from $_GET
     * @return string The value from $_GET
     */
    public function get($index)
    {
        if (isset($this->get[$index])) {
            return $this->get[$index];
        }

        return null;
    }

    /**
     * Retrieves the data from $_POST
     *
     * @param string $index The index to get from $_POST
     * @return string The value from $_POST
     */
    public function post($index)
    {
        if (isset($this->post[$index])) {
            return $this->post[$index];
        }

        return null;
    }

    /**
     * Retrieves the data from $_POST || $_GET
     *
     * @param string $index The index to get from $_POST || $_GET
     * @return string The value from $_POST || $_GET
     */
    public function request($index)
    {
        $value = $this->post($index);
        if ($value === null) {
            $value = $this->get($index);
        }

        return $value;
    }

    /**
     * Retrieves the data from $_SERVER
     *
     * @param string $index The index to get from $_SERVER
     * @return string The value from $_SERVER
     */
    public function server($index)
    {
        if (isset($this->server[$index])) {
            return $this->server[$index];
        }

        return false;
    }

    /**
     * Returns true if $_POST || $_GET is not empty
     *
     * @param string $index The index from $_POST || $_GET
     * @return boolean Not empty?
     */
    public function notEmpty($index)
    {
        return !empty($this->post[$index]) ||
            !empty($this->get[$index]);
    }

    /**
     * Returns true if $_POST || $_GET is not set
     *
     * @param string $index The index from $_POST || $_GET
     * @return boolean Not set?
     */
    public function notSet($index)
    {
        return (! isset($this->post[$index])) ||
            (! isset($this->get[$index]));
    }

    /**
     * Retrieves the data from the system
     * 
     * @param string $sourceName The source: get, post, server
     * @return array Contents of the system
     */
    private function getDataFromSystem($sourceName)
    {
        switch ($sourceName) {
            case 'GET':
                $input = INPUT_GET;
                $keys = array_keys($_GET);
                break;
            case 'POST':
                $input = INPUT_POST;
                $keys = array_keys($_POST);
                break;
            case 'SERVER':
                $input = INPUT_SERVER;
                $keys = array_keys($_SERVER);
                break;
            default:
                return array();
        }

        return $this->getData($input, $keys);
    }
    
    /**
     * Reads the data from the source
     * 
     * @param integer $input     The source of the data
     * @param array   $keys      The list of elements to read
     * @param boolean $clearData Clear after read
     * @return array
     */
    private function getData($input, $keys)
    {
        $result = array();
        foreach ($keys as $key) {
            if (\is_array($key)) {
                $result[$key] = filter_input($input, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
            } else {
                $result[$key] = filter_input($input, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        return $result;
    }
    
    /**
     * Clears get and post
     */
    private function clearAllData()
    {
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
            $_GET[$key] = null;
        }
        $keys = array_keys($_POST);
        foreach ($keys as $key) {
            $_POST[$key] = null;
        }
    }
}

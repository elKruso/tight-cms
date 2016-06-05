<?php
/**
 * Interface Request
 *
 * The interface for the GET/POST data
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Request.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface Request
 */
interface Request extends Cernel
{
    /**
     * Retrieves the data from $_GET
     *
     * @param string $index The index to get from $_GET
     * @return string The value from $_GET
     */
    public function get($index);

    /**
     * Retrieves the data from $_POST
     *
     * @param string $index The index to get from $_POST
     * @return string The value from $_POST
     */
    public function post($index);

    /**
     * Retrieves the data from $_POST || $_GET
     *
     * @param string $index The index to get from $_POST || $_GET
     * @return string The value from $_POST || $_GET
     */
    public function request($index);

    /**
     * Retrieves the data from $_SERVER
     *
     * @param string $index The index to get from $_SERVER
     * @return string The value from $_SERVER
     */
    public function server($index);

    /**
     * Returns true if $_POST || $_GET is not empty
     *
     * @param string $index The index from $_POST || $_GET
     * @return boolean Not empty?
     */
    public function notEmpty($index);

    /**
     * Returns true if $_POST || $_GET is not set
     *
     * @param string $index The index from $_POST || $_GET
     * @return boolean Not set?
     */
    public function notSet($index);
}

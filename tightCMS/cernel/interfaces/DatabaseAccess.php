<?php
/**
 * Interface DatabaseAccess
 * The interface for the database access. All queries need to go through here.
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      DatabaseAccess.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;
/**
 * interface Datenbankzugriff
 */
interface DatabaseAccess extends Cernel
{
    /**
     * Connects to the database server
     *
     * @param string $user      Username to connect with
     * @param string $pass      Password to connect with
     * @param string $datenbank Name of the database
     * @param string $server    Servername to connect with
     * @param string $port      Portnumber if different (give default)
     * @return boolean|string   True on connect, server message on error
     */
    public function connect($user, $pass, $datenbank, $server, $port = '');

    /**
     * Disconnects from database server
     *
     * @return boolean Success?
     */
    public function disconnect();

    /**
     * Switches to a different database
     *
     * @param string $datenbank Name of the new database
     * @return boolean
     */
    public function switchDB($datenbank);

    /**
     * Set flag, so that the following statement will be 
     * executed without securitychecks
     *
     * @return void
     */
    public function unsafe();

    /**
     * Executes the statement on the database
     *
     * @param string $statement The statement with "?" as placeholder
     * @param array  $elemente  The list of data placed in the "?"
     * @return boolean|string   True on success, message on error
     */
    public function query($statement, array $elemente = array());

    /**
     * Return the number of rows in the result
     *
     * @return integer
     */
    public function rowCount();

    /**
     * Returns all rows of the last query as an associative array
     *
     * @return array Array containing data -> Array(Array('fieldname' => 'value'))
     */
    public function rowsAssoc();

    /**
     * Returns all rows of the last query as an array
     *
     * @return array Array containing data -> Array(Array(number => 'value'))
     */
    public function rowsArray();

    /**
     * Returns the next row as an associative array, selects next
     *
     * @return array Array with data -> Array('fieldname' => 'value')
     */
    public function rowAssoc();

    /**
     * Returns the next row as an array, selects next
     *
     * @return array Array mit data -> Array(number => 'value')
     */
    public function rowArray();

    /**
     * Returns the id of the last inserted record
     *
     * @return integer
     */
    public function lastIndex();

    /**
     * Returns the fieldnames of the result 
     *
     * @return array Array with fieldnames
     */
    public function rowTitles();

    /**
     * Returns to the first row
     *
     * @return boolean
     */
    public function reset();

    /**
     * Releases the current results
     *
     * @return boolean
     */
    public function free();
}

<?php
/**
 * Interface FileAccess
 *
 * The interface for the fileaccess.
 * TighCMS will only call these functions
 * 
 * @author    Daniel Kruse
 * @copyright 2014 Breitmeister Entertainment
 * @name      FileAccess.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface FileAccess
 */
interface FileAccess extends Cernel
{
    /**
     * Connects to a server
     * 
     * @param string $username Username to connect with
     * @param string $password Password to connect with
     * @param string $server   Servername to connect
     * @param string $port     Portnumber
     * @return boolean|string True on success, message on error
     */
    public function connect($username, $password, $server, $port = '');
    
    /**
     * Disconnects from the server
     * 
     * @return boolean
     */
    public function disconnect();
            
    /**
     * Opens the file to read, reads the content, closes the file, 
     * returns content
     *
     * @param string $filename Name of the file to open
     * @return string Contents of the file
     */
    public function readFile($filename);

    /**
     * Opens the file, reads the JSON-Content, closes the filem
     * returns content as array
     *
     * @param string $filename Name of the file to open
     * @return array Content of the file
     */
    public function readFileJSON($filename);

    /**
     * Creates the file, writes the content, closes the file
     *
     * @param string $filename Name of the file
     * @param string $content  Content to write
     * @return boolean
     */
    public function writeFile($filename, $content);

    /**
     * Creates the file, writes the content as JSON, closes the file
     *
     * @param string $filename Name of the file
     * @param array  $content  The content to write
     * @return boolean
     */
    public function writeFileJSON($filename, array $content);

    /**
     * Deletes the file
     *
     * @param string $filename Name of the file
     * @return boolean
     */
    public function deleteFile($filename);

    /**
     * Read the file containing data beginning with "return array(...)"
     *
     * @param string $filename Name of the file
     * @return array
     */
    public function readFilePHPArray($filename);

    /**
     * Reads the files of the given directory, and returns the names as 
     * an array. If hidden ist set to true, hidden files are returned.
     * (Files containing '.*' (not the structure elements '.' and '..')
     *
     * @param string  $path   Path to read from
     * @param boolean $hidden Retrieve hidden files?
     * @return boolean|array False on path not found, list if readable
     */
    public function readDirectory($path, $hidden = false);

    /**
     * Reads the files and structure of the given directory, and returns the 
     * structure as an array, containing filenames, and subarrays with filenames
     * and so on. If hidden ist set to true, hidden files are returned.
     * (Files containing '.*' (not the structure elements '.' and '..')
     *
     * @param string  $path   Path to read from
     * @param boolean $hidden Retrieve hidden files?
     * @return boolean|array False on path not found, list if readable
     */
    public function readDirectoryStructure($path);

    /**
     * Finds the files in path, matching the given pattern
     *
     * @param string  $path    Path to read from
     * @param string  $pattern Pattern to match with
     * @param boolean $hidden  Retrieve hidden files?
     * @return array Found files
     */
    public function findFilesInDirectory($path, $pattern);

    /**
     * Adds content to the given file
     *
     * @param string $filename Name of the file
     * @param string $content  Content to append
     * @return boolean
     */
    public function appendContent($filename, $content);

    /**
     * Has an error occured
     *
     * @return boolean
     */
    public function hasError();

    /**
     * Returns the errormessage
     *
     * @return string
     */
    public function getError();
}

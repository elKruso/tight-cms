<?php
/**
 * Class FileAccess
 *
 * Class for fileAccess on local system.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      FileAccess.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\FileAccess as FileAccessInterface;
use tightCMS\cernel\abstracts\Cernel;

/**
 * Class FileAccess
 */
class FileAccess extends Cernel implements FileAccessInterface
{
    /**
     * The error messages
     * @var string
     */
    private $errorstring;

    /**
     * Initialise the variables
     */
    public function __construct()
    {
        $this->errorstring = '';
    }

    /**
     * Frees the variables
     */
    public function __destruct()
    {
        unset($this->errorstring);
    }
    
    /**
     * Connects to a server
     * 
     * @param string $username Username to connect with
     * @param string $password Password to connect with
     * @param string $server   Servername to connect
     * @param string $port     Portnumber
     * @return boolean|string True on success, message on error
     */
    public function connect($username, $password, $server, $port = '') 
    {
        // No connection to a sever needed
        return true;
    }

    /**
     * Disconnects from the server
     * 
     * @return boolean
     */
    public function disconnect() 
    {
        return true;
    }

    /**
     * Opens the file to read, reads the content, closes the file, 
     * returns content
     *
     * @param string $filename Name of the file to open
     * @return string Contents of the file
     */
    public function readFile($filename)
    {
        $this->resetError();
        $inhalt = false;
        // Filename set?
        if (! empty($filename)) {
            if (file_exists($filename)) {
                $inhalt = \file_get_contents($filename);
            } else {
                $this->addError('Dateizugriff-Fehler: Datei existiert nicht.');
            }
            if ($inhalt === false) {
                $this->addError('Dateizugriff-Fehler: Konnte Datei nicht lesen.');
            }
        } else {
            $this->addError('Dateizugriff-Fehler: Kein Dateinamen angegeben.');
        }

        return $inhalt;
    }

    /**
     * Opens the file, reads the JSON-Content, closes the filem
     * returns content as array
     *
     * @param string $filename Name of the file to open
     * @return array Content of the file
     */
    public function readFileJSON($filename)
    {
        $content = $this->readFile($filename);
        if (false === $content && $this->hasError()) {
            return false;
        }

        return \json_decode($content, true);
    }

    /**
     * Creates the file, writes the content, closes the file
     *
     * @param string $filename Name of the file
     * @param string $content  Content to write
     * @return boolean
     */
    public function writeFile($filename, $content)
    {
        $this->resetError();
        $handle = false;
        // Filename set?
        if (! empty($filename)) {
            $handle = \file_put_contents($filename, $content);

            if ($handle === false) {
                $this->addError('Dateizugriff-Fehler: Konnte nicht in die Datei schreiben.');
            }
        } else {
            $this->addError('Dateizugriff-Fehler: kein Dateiname angegeben.');
        }

        return $handle;
    }

    /**
     * Creates the file, writes the content as JSON, closes the file
     *
     * @param string $filename Name of the file
     * @param array  $content  The content to write
     * @return boolean
     */
    public function writeFileJSON($filename, array $content)
    {
        $json = \json_encode($content);

        return $this->writeFile($filename, $json);
    }

    /**
     * Deletes the file
     *
     * @param string $filename Name of the file
     * @return boolean
     */
    public function deleteFile($filename)
    {
        $this->resetError();
        $handle = false;
        // Filename set?
        if (! empty($filename) && \file_exists($filename)) {
            // Delete
            $handle = \unlink($filename);

            if ($handle === false) {
                $this->addError('Dateizugriff-Fehler: Konnte Datei nicht löschen.');
            }
        } else {
            $this->addError('Dateizugriff-Fehler: Keine Datei zum löschen angegeben und nicht gefunden.');
        }

        return $handle;
    }

    /**
     * Read the file containing data beginning with "return array(...)"
     *
     * @param string $filename Name of the file
     * @return array
     */
    public function readFilePHParray($filename)
    {
        $array = array();
        if (file_exists($filename)) {
            $array = include($filename);
            if (! \is_array($array)) {
                echo 'No Array';
                return false;
            }
        } else {
            return 'Konnte "' . $filename . '" nicht finden.';
        }

        return $array;
    }

    /**
     * Reads the files of the given directory, and returns the names as 
     * an array. If hidden ist set to true, hidden files are returned.
     * (Files containing '.*' (not the structure elements '.' and '..')
     *
     * @param string  $path   Path to read from
     * @param boolean $hidden Retrieve hidden files?
     * @return boolean|array False on path not found, list if readable
     */
    public function readDirectory($path, $hidden = false)
    {
        $output = array(
            'files' => array(),
            'dirs' => array(),
            'all' => array()
        );
        $workpath = $this->fixPath($path);
        foreach (\glob($workpath . '*') as $eintrag) {
            if ($hidden || '.' !== $eintrag{1}) {
                $filename = \substr($eintrag, \strrpos($eintrag, '/') + 1);
                if (\is_dir($eintrag)) {
                    $output['dirs'][] = $filename;
                }
                if (\is_file($eintrag)) {
                    $output['files'][] = $filename;
                }
                $output['all'][] = $filename;
            }
        }

        return $output;
    }

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
    public function readDirectoryStructure($path, $hidden = false)
    {
        $output = array();
        $workpath = $this->fixPath($path);
        foreach (\glob($workpath . '*', GLOB_ONLYDIR) as $eintrag) {
            $einkey = \substr($eintrag, \strrpos($eintrag, '/') + 1);

            $output[$einkey] = $this->readDirectoryStructure($eintrag);
        }
        if (\count($output) === 0) {
            $output = false;
        }

        return $output;
    }

    /**
     * Finds the files in path, matching the given pattern
     *
     * @param string  $path    Path to read from
     * @param string  $pattern Pattern to match with
     * @param boolean $hidden  Retrieve hidden files?
     * @return array Found files
     */
    public function findFilesInDirectory($path, $pattern, $hidden = false)
    {
        // TODO: implement
        $output = $this->globRecursive($path . $pattern);

        return $output;
    }

    /**
     * Adds content to the given file
     *
     * @param string $filename Name of the file
     * @param string $content  Content to append
     * @return boolean
     */
    public function appendContent($filename, $content)
    {
        
    }

    /**
     * Has an error occured
     *
     * @return boolean
     */
    public function hasError()
    {
        return strlen($this->errorstring) > 0;
    }

    /**
     * Returns the errormessage
     *
     * @return string
     */
    public function getError()
    {
        return $this->errorstring;
    }

    /**
     * Fixes the path to default
     *
     * @param string $path
     * @return string
     */
    private function fixPath($path)
    {
        return rtrim($path, '/') . '/';
    }

    /**
     * Empties the errorstring
     *
     * @return void
     */
    private function resetError()
    {
        $this->errorstring = '';

        return $this;
    }

    /**
     * Adds an error message
     *
     * @param string $errorString
     * @return void
     */
    private function addError($errorString)
    {
        $this->errorstring .= $errorString;

        return $this;
    }

    /**
     * Finds matching files in path
     * From:
     * @link http://php.net/manual/de/function.glob.php
     *
     * @param string  $pattern
     * @param integer $flags
     * @return array
     */
    private function globRecursive($pattern, $flags = 0)
    {
        $files = \glob($pattern, $flags);
        $dirs = \glob(\dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT);
        foreach ($dirs as $dir) {
            $files = \array_merge($files, $this->globRecursive(
                $dir . '/' . \basename($pattern),
                $flags
            ));
        }

        return $files;
    }
}

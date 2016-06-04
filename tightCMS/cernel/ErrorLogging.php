<?php
/**
 * Class ErrorLogging
 *
 * The class for the ErrorLogging
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      ErrorLogging.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\ErrorLogging as ErrorLoggingInterface;
use tightCMS\cernel\abstracts\Cernel;
use tightCMS\cernel\FileAccess;

/**
 * Class ErrorLogging
 */
class ErrorLogging extends Cernel implements ErrorLoggingInterface
{
    /**
     * The file access
     * @var FileAccess
     */
    private $fileAccess;

    /**
     * Filename of the log file
     * @var string
     */
    private $fileName;

    /**
     * Format of the output with the variables: modulename, date, content
     * @var string
     */
    private $format = "%i: %s (%s): %s\n";

    /**
     * Constructor
     *
     * Initializes the vars
     */
    public function __construct($fileAccess)
    {
        $this->fileAccess = $fileAccess;
        $this->fileName   = '';
    }

    /**
     * Destructor
     *
     * Frees the vars
     */
    public function __destruct()
    {
        unset($this->fileName);
    }

    /**
     * Starts the logging
     *
     * @param string $filename Filename to add the log to
     * @return boolean
     */
    public function startLogging($filename)
    {
        if (TIGHT_LOG) {
            if (! empty($filename)) {
                $this->fileName = $filename;

                return $this->fileAccess->writeFile($this->fileName, '');
            }
        }
        return false;
    }

    /**
     * Closes the logging
     *
     * @return boolean
     */
    public function stopLogging()
    {
        $this->fileName = '';

        return true;
    }

    /**
     * Writes the content to the file
     * Formated as "$modulname (DATUM): $inhalt"
     *
     * @param int    $state     State of the logging
     * @param string $modulname Name of the module
     * @param string $inhalt    Contents of the log
     * @return boolean
     */
    public function writeLog($state, $modulname, $inhalt)
    {
        if (TIGHT_LOG) {
            if ((! empty($modulname)) && (! empty($inhalt))) {
                $eintrag = vsprintf($this->format, array(
                    $state,
                    $modulname,
                    date('d.m.Y H:i:s'),
                    $inhalt
                ));
                return $this->fileAccess->appendContent($this->fileName, $eintrag);
            }
        }

        return false;
    }

    /**
     * Leert das geöffnete Logfile
     *
     * @return boolean
     */
    public function clearLog()
    {
        if (TIGHT_LOG) {
            if (! empty($this->fileName)) {
                return $this->fileAccess->deleteFile($this->fileName);
            }
        }
        
        return false;
    }
}

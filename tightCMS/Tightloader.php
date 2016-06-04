<?php
/**
 * Class Tightloader
 * 
 * Setting up the autoloader and the basic system
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Tightloader.php
 * @package   tightCMS
 * @version   1.0.0
 */

namespace tightCMS;

use tightCMS\cernel\FileAccess;

/**
 * class tightloader
 */
class Tightloader
{
    /**
     * Constructor defining constants
     */
    public function __construct()
    {
        // Define paths
        \define('TIGHT_BASEDIR',   $this->makeFolder(__DIR__ . '/../'));
        \define('TIGHT_CONFIG',    $this->makeFolder(__DIR__ . '/../config'));
        \define('TIGHT_TEMPLATES', $this->makeFolder(__DIR__ . '/../templates'));
        \define('TIGHT_LOGGER',    $this->makeFolder(__DIR__ . '/../logger'));
        \define('TIGHT_MODULES',   $this->makeFolder(__DIR__ . '/../modules'));
        \define('TIGHT_EXTERNALS', $this->makeFolder(__DIR__ . '/../externals'));
        \define('TIGHT_LOG',       true);
        \define('TIGHT_REQUEST',   'request');
        // Config
        \error_reporting(E_ALL);
        \ini_set('display_errors', '1');
        \chdir(TIGHT_BASEDIR);
        // Load external autoloader
        if (!is_dir(TIGHT_EXTERNALS)) {
            echo 'No externals found, please run composer first!';
            exit;
        }
        require TIGHT_EXTERNALS . 'autoload.php';
        // Register own autoloader
        \spl_autoload_register(array($this, 'autoloader'));
    }

    /**
     * Start the autoloader
     *
     * @param string $className
     * @return void
     */
    public function autoloader($className)
    {
        $message = '';
        $success = false;
        try {
            $filename = TIGHT_BASEDIR . \str_replace('\\', '/', $className) . '.php';
            if (file_exists($filename)) {
                $success = include(TIGHT_BASEDIR . \str_replace('\\', '/', $className) . '.php');
            }
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }
        if (!$success) {
            $this->displayError($message, $className);
        }
    }

    /**
     * Shows the error
     * 
     * @param string $message
     * @param string $class
     */
    public function displayError($message, $class)
    {
        echo $message . '<br>';
        echo 'File not Found: ' . $class . '.php<br>';
        $trace = \debug_backtrace();
        $count = 0;
        foreach ($trace as $entry) {
            echo $count++ . ': ';

            if (!empty($entry['file'])) {
                echo $entry['file'] . ' ';
            }
            echo ' @ ' . $entry['function'];
            if (!empty($entry['line'])) {
                echo ' (Line: ' . $entry['line'] . ')';
            }
            echo '<br>';
        }
    }

    /**
     * Make the path
     *
     * @param string $path The subdirectory
     * @return string
     */
    private function makeFolder($path)
    {
        $prePath = \rtrim(\str_replace('\\', '/', $path), '/') . '/';
        
        return \realpath($prePath) . '/';
    }

    /**
     * Finds the folder containing the module
     *
     * @param string $modulname Name of the module
     * @return string Pfad zu dem Modul
     */
    public static final function findeModul($modulname)
    {
        $pfade = (new FileAccess())->findFilesInDirectory(TIGHT_MODULES, $modulname . '.php');
        if (count($pfade) == 0) {
            return false;
        }

        return 'modules\\' . \str_replace(
            array(TIGHT_MODULES, '/', '.php'),
            array('', '\\', ''),
            $pfade[0]
        );
    }
}

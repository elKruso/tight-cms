<?php
/**
 * Abstract TemplateEngine
 * 
 * Implements the basic functionality for the template engine
 * 
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      TemplateEngine.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\abstracts;

use tightCMS\cernel\abstracts\Cernel;
use tightCMS\cernel\interfaces\TemplateEngine as TemplateEngineInterface;
use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\interfaces\FileAccess as FileAccessInterface;

abstract class TemplateEngine extends Cernel implements TemplateEngineInterface
{
    /**
     * File Access
     * @var \tightCMS\Cernel\FileAccess
     */
    private $fileAccess = null;

    /**
     * DatabaseAccess
     * @var \tightCMS\Cernel\DatabaseAccess
     */
    private $dbAccess = null;

    /**
     * Start of the Tag
     * @var String
     */
    protected $tagStart = '[[';

    /**
     * End of the Tag
     * @var String
     */
    protected $tagEnd = ']]';

    /**
     * Filename 
     * @var String
     */
    protected $templateName;

    /**
     * Content of the template
     * @var String
     */
    protected $templateContent;

    /**
     * Contents for the template
     * Format: "Fieldname" => "Fieldcontent"
     * @var Array
     */
    protected $templateArray;

    /**
     * Template containing the contents
     * @var String
     */
    protected $templateOutput;

    /**
     * Errormessages
     * @var String
     */
    protected $error;

    /**
     * 404-Document
     * @var String
     */
    protected $templateError;

    /**
     * Content to add at end of file
     * @var string
     */
    protected $injectionAtEnd;

    /**
     * Constructor
     *
     * @param DatabaseAccessInterface $databaseAccess
     * @param FileAccessInterface     $fileAccess
     */
    public function __construct(
        DatabaseAccessInterface $databaseAccess,
        FileAccessInterface $fileAccess
    ) {
        $this->fileAccess      = $fileAccess;
        $this->dbAccess        = $databaseAccess;
        $this->templateName    = '';
        $this->templateContent = '';
        $this->templateArray   = array();
        $this->templateOutput  = '';
        $this->error           = '';
        $this->templateError   = '#0001: Error loading Template.<br/>'
            . 'Please inform the developer with the following informations.<br/><br/>';
        $this->injectionAtEnd  = '';
    }

    /**
     * Destructor
     *
     * Speicher freigeben
     */
    public function __destruct()
    {
        unset($this->templateName);
        unset($this->templateContent);
        unset($this->templateArray);
        unset($this->templateOutput);
        unset($this->error);
        unset($this->templateError);
        unset($this->injectionAtEnd);
    }

    /**
     * Loads the template file
     *
     * @param string $filename Path to the template file
     * @return boolean
     */
    public function loadTemplate($filename)
    {
        if (! $this->checkName($filename)) {
            return false;
        }
        // Empty templates
        $this->resetTemplate($filename);
        // Open file
        $this->templateContent = $this->fileAccess->readFile($filename);
        if ($this->templateContent === false) {
            // Add error messages
            $this->addError('#0002: File "' . 
                $filename . '" not found.');
            return false;
        }

        return true;
    }

    /**
     * Loads the template from the database
     *
     * @param string $templateName Name of the template
     * @param string $level        Level of the template
     * @return boolean|string False if not found, else content
     */
    public function loadTemplateFromDB($templateName, $level = '')
    {
        $status = true;
        if (! $this->checkName($templateName)) {
            return false;
        }
        if ($this->dbAccess === null) {
            $this->addError('#0003: No databaseAccess available.');
            return false;
        }
        $this->resetTemplate($templateName . '-' . $level);
        // SQL-Statement:
        $this->dbAccess->query("
            SELECT template
            FROM templatelist
            WHERE modul = ?
                AND ebene = ?
        ", array(
            $templateName,
            $level
        ));
        if ($this->dbAccess->rowCount() === 1) {
            $this->templateContent = $this->dbAccess->rowAssoc()['template'];
        } else {
            $this->addError('#0004: Wrong row count ('
                . $this->dbAccess->rowCount() . ')');
            $status = false;
        }

        return $status;
    }

    /**
     * Takes the content as a template
     *
     * @param string $content Content to set
     * @return boolean
     */
    public function setTemplate($content)
    {
        if (empty($content)) {
            $this->addError('#0005: No content delivered.');
            return false;
        }
        $this->resetTemplate('');
        $this->templateContent = $content;

        return true;
    }

    /**
     * Sets values to replace "[[fieldname]]" with
     *
     * @param string $fieldname Fieldname to be replaced
     * @param string $content   The content to set 
     * @return boolean
     */
    public function setContent($fieldname, $content)
    {
        $status = $this->checkFieldname($fieldname);
        if ($status) {
            if (! empty($this->templateArray[$fieldname])) {
                // content was already set
                unset($this->templateArray[$fieldname]);
                $this->addError('#0006: Fieldname "' 
                    . $fieldname . '" already set. Overwriting.');
            }
            // Save content
            $this->templateArray[$fieldname] = $content;
        }

        return $status;
    }

    /**
     * Injects code to the end of the template
     * 
     * @param string $jsText
     * @return void
     */
    public function injectAtEnd($jsText)
    {
        $this->injectionAtEnd = $jsText;
    }

    /**
     * Renders the output
     *
     * @return string Rendered template
     */
    public function render()
    {
        $output = '';
        if (empty($this->error)) {
            // Replace contents:
            if ($this->replacePlaceholders()) {
                // Set result
                $output = $this->templateOutput;
            } else {
                // Set error
                $output = $this->templateError .
                    '<div class="errorSystem">
                        ' . $this->error . '
                    </div>';
            }
        }
        
        return $output . $this->injectionAtEnd;
    }
    
    /**
     * Resets the contents
     */
    public function resetData()
    {
        $this->templateArray = array();

        return $this;
    }

    /**
     * Debug liste
     *
     * @return array
     */
    public function dumpPlaceholder()
    {
        return '
TEMPLATE-DATA: ' . $this->templateName . '<br>
--------------<br>
<pre>' . print_r($this->templateArray, true) . "</pre>\n";
    }

    /**
     * Reset the Templatedata
     *
     * @param string $name Name of the template
     * @return void
     */
    private function resetTemplate($name = '')
    {
        $this->templateName    = $name;
        $this->templateContent = '';
        $this->templateOutput  = '';
    }

    /**
     * Checks the fieldname to be a string
     * 
     * @param string $fieldname The fieldname to check
     * @return boolean
     */
    private function checkFieldname($fieldname)
    {
        $state = true;
        if (empty($fieldname)) {
            $this->addError('#0007: Fieldname is empty.');
            $state = false;
        }
        // prüfen, ob inhalte logisch
        if (is_numeric($fieldname)) {
            $this->addError('#0008: Fieldname is a number. Please use strings.');
            $state = false;
        }
        if (is_array($fieldname)) {
            $this->addError('#0009: Fieldname ist an array. Please use strings.');
            $state = false;
        }

        return $state;
    }

    /**
     * Checks the name
     *
     * @param string $name
     * @return boolean
     */
    private function checkName($name)
    {
        $state = true;
        if (empty($name)) {
            $this->addError('#0010: Dateiname nicht gesetzt.');
            $state = false;
        }
        if (is_numeric($name)) {
            $this->addError('#0011: Es wurde kein String übergeben.<br/>');
            $state = false;
        }

        return $state;
    }
    
    /**
     * Picks random data from the database
     *
     * @param string $tablename Table to pick data from
     * @param string $restrictions Restriction to the query
     * @return string
     */
    protected function getDataFromDB($tablename, $restrictions = '')
    {
        $filter = '';
        if (! empty($restrictions)) {
            // TODO: filter erstellen
        }
        // Request Database
        $this->dbAccess->query(
            "SELECT name
            FROM wort_" . $tablename . "
            " . $filter . "
            ORDER BY rand() "
        );
        $result = $this->dbAccess->rowAssoc();

        return $result['name'];
    }

    /**
     * Creates random data
     *
     * @param string $info Restricion containing '0-999' for numbers, 'a-z' for strings
     * @return string
     */
    protected function getRandomData($info)
    {
        $temp = explode('-', $info);

        if (is_numeric($temp[0]) && is_numeric($temp[1])) {
            // Numbers
            $return = rand($temp[0], $temp[1]);
        } else {
            // Alphabet
            $return = chr(rand(ord($temp[0]), ord($temp[1])));
        }

        return $return;
    }

    /**
     * Adds an error
     *
     * @param string $error
     * @return void
     */
    protected function addError($error)
    {
        if (strlen($error) !== 0) {
            $this->error .= $error . '<br/>';
        }
    }
}

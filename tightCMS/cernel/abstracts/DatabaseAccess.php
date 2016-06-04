<?php
/**
 * Abstract DatabaseAccess
 * 
 * Implements basic functions for database access
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      DatabaseAccess.php
 * @package   tightCMS
 * @version   1.0.0
 */

namespace tightCMS\cernel\abstracts;

use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\abstracts\Cernel as CernelAbstract;
use PHPSQLParser\PHPSQLParser;

/**
 * Abstract DatabaseAccess
 */
abstract class DatabaseAccess 
    extends CernelAbstract
    implements DatabaseAccessInterface
{
    /**
     * Defines the safe statements
     * @var array
     */
    protected $safeStatements = array(
        'SELECT',
        'INSERT',
        'REPLACE',
        'UPDATE',
        'DELETE'
    );

    /**
     * The SQL-Parser
     * @var PHPSQLParser
     */
    private $sqlParser;

    /**
     * Sichere Abfrage?
     * @var boolean
     */
    protected $safe;

    /**
     * Error message
     * @var string
     */
    private $errorstring;
    
    /**
     * Keys of the sql statement
     * @var array
     */
    private $sqlKeys;
    
    /**
     * Structure of the sql statement
     * @var array
     */
    private $sqlStruct;

    /**
     * "SELECT" -> True, else -> false
     * @var boolean
     */
    private $selection;

    /**
     * Name of the current queried table
     * @var string
     */
    private $tabName;

    /**
     * Constructs the required classes
     */
    public function __construct() 
    {
        $this->sqlParser   = new PHPSQLParser();
        $this->safe        = true;
        $this->errorstring = '';
    }
    
    /**
     * Frees the variables
     */
    public function __destruct() 
    {
        unset($this->sqlParser);
        unset($this->safe);
        unset($this->errorstring);
    }
    
    /**
     * Set flag, so that the following statement will be 
     * executed without securitychecks
     *
     * @return self
     */
    public function setUnsafe()
    {
        $this->safe = false;

        return $this;
    }

    /**
     * Sets the current safe state
     * 
     * @return self
     */
    protected function setSafe($state)
    {
        $this->safe = $state;

        return $this;
    }

    /**
     * Returns safe
     * 
     * @return boolean
     */
    protected function isSafe()
    {
        return $this->safe;
    }

    /**
     * Checks the statement if its safe
     * 
     * @param string $statement The statement to check
     * @return boolean
     */
    public function checkIfSafe($statement) 
    {
        $this->sqlParser->parse($statement);
        
        $this->sqlStruct = $this->sqlParser->parsed;
        $this->sqlKeys   = \array_keys($this->sqlStruct);

        if (! \in_array($this->sqlKeys[0], $this->safeStatements)) {
            $this->errorstring .= 'Datenbank-Fehler: Nur SELECT, INSERT, REPLACE, UPDATE, DELETE sind zugelassen.<br/>';
            return false;
        }

        return true;
    }

    /**
     * Updates the local variables to current query
     * 
     * @return self
     */
    public function getQueryInformation() 
    {
        if (\count($this->sqlKeys) > 0 && \in_array(
                $this->sqlKeys[0], 
                array('SELECT', 'SHOW')
            )
        ) {
            $this->selection = true;
        } else {
            $this->selection = false;
        }
        // letzte Datenbank merken, für lastIndex() and rowTitles()
        if (isset($this->sqlStruct['FROM'])) {
            $this->tabName = $this->sqlStruct['FROM'][0]['table'];
        }
        
        return $this;
    }
    
    /**
     * Add an error message
     * 
     * @param string $message The error message
     * @return self
     */
    protected function addErrorString($message)
    {
        $this->errorstring .= $message;
        
        return $this;
    }
    
    /**
     * Retrieves the error messages
     * 
     * @return string
     */
    protected function getErrorString()
    {
        return $this->errorstring;
    }
    
    /**
     * Retrieves the flag that indicates a select statement
     * 
     * @return boolean
     */
    protected function getSelection() {
        return $this->selection;
    }
    
    /**
     * Retrieves the table name of the last query
     * 
     * @return string
     */
    protected function getTabName() {
        return $this->tabName;
    }
}

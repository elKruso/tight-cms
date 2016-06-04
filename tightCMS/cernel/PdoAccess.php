<?php
/**
 * Class PdoAccess
 *
 * Das Interface für den Datenbankzugriff. Alle Abfragen müssen hierauf gesetzt werden.
 * Nur die hier aufgeführten Funktionen werden vom tightCMS ausgeführt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      PdoAccess.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\abstracts\DatabaseAccess as DatabaseAccessAbstract;

/**
 * Class PdoAccess
 */
class PdoAccess extends DatabaseAccessAbstract 
    implements DatabaseAccessInterface
{
    /**
     * Ressource PDO-Verbindung
     * @var PDO
     */
    private $pdoConnection;

    /**
     * Ressource MySQL-Abfrage
     * @var array
     */
    private $result;

    /**
     * Name of the server
     * @var string
     */
    private $server;

    /**
     * Number of the serverport
     * @var int
     */
    private $port;

    /**
     * Name of the user
     * @var string
     */
    private $user;

    /**
     * Password of the user
     * @var string
     */
    private $pass;

    /**
     * Current row of the database
     * @var int
     */
    private $current;

    /**
     * initialisiert die Variablen auf leere Werte
     */
    function __construct()
    {
        parent::__construct();
        
        $this->pdoConnection = false;
        $this->result        = null;
        $this->tabName       = '';
        $this->server        = '';
        $this->port          = '';
        $this->user          = '';
        $this->pass          = '';
        $this->current       = 0;
        $this->abfrage       = false;
    }

    /**
     * destructor()
     *
     * Schliesst offene verbindunge, und gibt speicher frei
     */
    function __destruct()
    {
        if ($this->pdoConnection !== false) {
            $this->disconnect();
        }
        if ($this->result !== null) {
            $this->free();
        }
        parent::__destruct();
        
        unset($this->pdoConnection);
        unset($this->result);
        unset($this->tabName);
        unset($this->server);
        unset($this->port);
        unset($this->user);
        unset($this->pass);
        unset($this->current);
        unset($this->abfrage);
    }

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
    public function connect($user, $pass, $datenbank, $server, $port = '')
    {
        if ($this->pdoConnection !== false) {
            $this->disconnect();
        }
        // Eingaben prüfen:
        $this->checkServerParameters($user, $pass, $server);
        if (strlen($this->getErrorString()) === 0) {
            // Verbindung zum Server:
            $this->server = $server;
            $this->port   = $port;
            $this->user   = $user;
            $this->pass   = $pass;

            $this->startConnection($datenbank);
        } else {
            $this->addErrorString('Datenbank-Fehler: Verbinden mit dem Datenbank-Server<br/>');
        }

        return $this->rueckMeldung();
    }

    /**
     * Disconnects from database server
     *
     * @return boolean Success?
     */
    public function disconnect()
    {
        if ($this->pdoConnection !== false) {
            unset($this->pdoConnection);
            $this->pdoConnection = false;
        } else {
            $this->addErrorString('Datenbank-Fehler: Keine Verbindung zum Schliessen ge&ouml;ffnet.<br/>');
        }

        return $this->rueckMeldung();
    }

    /**
     * Switches to a different database
     *
     * @param string $datenbank Name of the new database
     * @return boolean
     */
    public function switchDB($datenbank)
    {
        if ($this->pdoConnection !== false) {
            $this->disconnect();
            $this->startConnection($datenbank);
        }

        return $this->rueckMeldung();
    }

    /**
     * Executes the statement on the database
     *
     * @param string $statement The statement with "?" as placeholder
     * @param array  $elemente  The list of data placed in the "?"
     * @return boolean|string   True on success, message on error
     */
    public function query($statement, array $elemente = array())
    {
        if (empty($statement)) {
            $this->addErrorString('Datenbank-Fehler: Abfrage war leer<br/>');
            return false;
        }
        // nach methode unsafe() wird die folgende Sicherheit übersprungen:
        if ($this->isSafe()) {
            $safe = $this->checkIfSafe($statement);
        } else {
            $safe = true;
        }
        // Kein Fehler, dann gehts los:
        if (\strlen($this->getErrorString()) === 0) {
            if ($this->pdoConnection !== false) {
                $this->current = 0;
                // Abfrage ausführen:
                $stmt = $this->pdoConnection->prepare($statement);
                $stmt->execute($elemente);
                $this->result = $stmt->fetchAll();
                $stmt->closeCursor();
                unset($stmt);
                // result geladen
                if ((! \is_array($this->result)) && ($this->result === false)) {
                    $this->addErrorString('Datenbank-Fehler: Fehler im Statement.<br/>'
                        . $this->pdoConnection->errorInfo() . '<br/>'
                        . $statement . '<br/>');
                } else {
                    // Ermitteln ob Abfrage, oder Modifikation:
                    $this->getQueryInformation();
                }
            } else {
                $this->addErrorString('Datenbank-Fehler: Keine Verbindung vorhanden<br/>');
            }
        }

        return $this->rueckMeldung();
    }

    /**
     * Return the number of rows in the result
     *
     * @return integer
     */
    public function rowCount()
    {
        $anzahl = false;
        if ($this->result !== false) {
            if ($this->abfrage) {
                // SELECT-Abfrage
                $anzahl = \count($this->result);
            }
        }

        return $anzahl;
    }

    /**
     * Returns the fieldnames of the result 
     *
     * @return array Array with fieldnames
     */
    public function rowTitles()
    {
        $output = false;
        if (!empty($this->tabName)) {
            $sqlstmt = 'SHOW COLUMNS FROM ? ';
            $stmt = $this->pdoConnection->prepare($sqlstmt);
            $stmt->execute(array($this->tabName));
            $result = $stmt->fetchAll();
            $output = array();
            if ($result) {
                foreach ($result as $eintrag) {
                    \array_push($output, $eintrag['Field']);
                }
            }
        }

        return $output;
    }

    /**
     * Returns the next row as an associative array, selects next
     *
     * @return array Array with data -> Array('fieldname' => 'value')
     */
    public function rowAssoc()
    {
        $row = false;
        if ($this->result !== false) {
            if ($this->current >= $this->rowCount()) {
                return false;
            }
            if ($this->abfrage) {
                // SELECT-Abfrage
                $row = array();
                $temp = $this->result[$this->current];
                foreach ($temp as $key => $eintrag) {
                    if (! \is_numeric($key)) {
                        $row[$key] = $eintrag;
                    }
                }
                ++$this->current;
            }
        }

        return $row;
    }

    /**
     * Returns the next row as an array, selects next
     *
     * @return array Array mit data -> Array(number => 'value')
     */
    public function rowArray()
    {
        $row = false;
        if ($this->result !== false) {
            if ($this->current >= $this->rowCount()) {
                return false;
            }
            if ($this->abfrage) {
                // SELECT-Abfrage
                $row = array();
                $temp = $this->result[$this->current];
                foreach ($temp as $key => $eintrag) {
                    if (\is_numeric($key)) {
                        $row[$key] = $eintrag;
                    }
                }
                ++$this->current;
            }
        }

        return $row;
    }

    /**
     * Returns all rows of the last query as an associative array
     *
     * @return array Array containing data -> Array(Array('fieldname' => 'value'))
     */
    public function rowsAssoc()
    {
        $rows = false;
        if ($this->result !== false) {
            if ($this->abfrage) {
                // SELECT-Abfrage
                $rows = array();
                foreach ($this->result as $key => $eintrag) {
                    $temp = array();
                    foreach ($eintrag as $field => $value) {
                        if (! \is_numeric($field)) {
                            $temp[$field] = $value;
                        }
                    }
                    $rows[$key] = $temp;
                }
            }
        }

        return $rows;
    }

    /**
     * Returns all rows of the last query as an array
     *
     * @return array Array containing data -> Array(Array(number => 'value'))
     */
    public function rowsArray()
    {
        $rows = false;
        if ($this->result !== false && $this->abfrage) {
            // SELECT-Abfrage
            $rows = array();
            foreach ($this->result as $key => $eintrag) {
                $temp = array();
                foreach ($eintrag as $field => $value) {
                    if (\is_numeric($field)) {
                        $temp[$field] = $value;
                    }
                }
                $rows[$key] = $temp;
            }
        }
        return $rows;
    }

    /**
     * Returns to the first row
     *
     * @return boolean
     */
    public function reset()
    {
        $ergeb = false;
        if ($this->result !== false && $this->abfrage) {
            // SELECT-Abfrage
            if ($this->rowCount() > 0) {
                $this->current = 0;
                $ergeb = true;
            }
        }

        return $ergeb;
    }

    /**
     * Returns the id of the last inserted record
     *
     * @return integer
     */
    public function lastIndex()
    {
        if ($this->result !== false) {
            if (! $this->abfrage) {
                return $this->pdoConnection->lastInsertId();
            }
        }

        return false;
    }

    /**
     * Releases the current results
     *
     * @return boolean
     */
    public function free()
    {
        if ($this->result !== false) {
            // Datenbankverbindung schliessen:
            unset($this->result);
            $this->result = array();
            $this->current = 0;
        }

        return $this->rueckMeldung();
    }

    /**
     * Gibt TRUE oder ErrorString zurück, und leert den ErrorString
     *
     * @param $errorstring string Fehlerstring der Funktionen
     * @return mixed Boolean:True oder String:Fehlermeldung
     */
    private function rueckMeldung()
    {
        if (\strlen($this->getErrorString()) > 0) {
            return $this->getErrorString();
        }

        return true;
    }
    
    /**
     * Connects to a database
     * 
     * @param string $database Name of the database
     */
    private function startConnection($database)
    {
        $dsn = '%s:host=%s;port=%s;dbname=%s';
        $constr = \vsprintf($dsn, array(
            'mysql',
            $this->server,
            $this->port,
            $database
        ));
        $this->pdoConnection = new \PDO(
            $constr,
            $this->user,
            $this->pass,
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
        );
        // be quiet
        $this->pdoConnection->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_SILENT
        );
    }
    
    /**
     * Checks the parameters for the server
     * 
     * @param string $user   Username to connect with
     * @param string $pass   Password to connect with
     * @param string $server Server to connect with
     * @return boolean
     */
    private function checkServerParameters($user, $pass, $server) 
    {
        $result = true;
        if (empty($user)) {
            $this->addErrorString('Datenbank-Fehler: Kein Benutzer angegeben<br/>');
            $result = false;
        }
        if (empty($pass)) {
            $this->addErrorString('Datenbank-Fehler: Kein Passwort angegeben<br/>');
            $result = false;
        }
        if (empty($server)) {
            $this->addErrorString('Datenbank-Fehler: Kein Server angegeben<br/>');
            $result = false;
        }
        return $result;
    }
}

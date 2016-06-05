<?php
/**
 * Class Module
 *
 * Die Basisklasse für die erweiterungsmodule
 * Nur die hier aufgeführten Funktionen werden vom tightCMS ausgef�hrt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Module.php
 * @package   tightCMS/Cernel
 * @version   2.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\abstracts\Module as ModuleAbstract;
use tightCMS\cernel\interfaces\Module as ModuleInterface;
use tightCMS\Tightloader;

/**
 * Class Module
 */
abstract class Module extends ModuleAbstract
{
    /**
     * Tag list
     * @var array
     */
    protected $tagliste;

    /**
     * Parameter list
     * @var array
     * @todo make private and use getter
     */
    protected $parameterliste;

    /**
     * Parameter list
     * @var string
     */
    protected $work;

    /**
     * Rewrite ebene
     * @var false|string
     */
    private $rewriteEbene;

    /**
     * Constructor
     * Setzt die Variablen auf die Standartwerte
     *
     * @return self
     */
    public function __construct()
    {
        parent::__construct();

        $this->tagliste       = array();
        $this->parameterliste = array();
    }

    /**
     * Destructor
     * gibt den Speicher wieder frei
     *
     * @return void
     */
    public function __destruct()
    {
        unset($this->tagliste);
        unset($this->parameterliste);
    }

    /**
     * Returns the parameter at the index
     *
     * @param string|int $index
     * @return mixed
     */
    public final function getParameter($index)
    {
        return $this->parameterliste[$position];
    }

    /**
     * Gibt die Versionsnummer zurück + Copyright
     *
     * @return string "Modulname V1.0 - &copy; Jahr - Autor"
     */
    public function getVersion()
    {
        return 'Modul: Schnittstelle V2.1 - &copy; 2014 - Daniel Kruse';
    }

    /**
     * Gibt die informationen zurück
     * 
     * @return array
     */
    public function getInformation() 
    {
        return array();
    }
    
    /**
     * Returns help with manual
     *
     * @return string
     */
    public function help()
    {

    }

    /**
     * Loads the module with the given parameters, and returns the result 
     * of the action
     *
     * @param string $moduleName Name of the module (without .php)
     * @param string $action     The action to perform
     * @param string $parameters Parameters of the module
     * @return mixed Result of the action
     */
    public final function loadSubAction($moduleName, $action, array $parameters)
    {
        $ergebnis = false;
        $classRequest = false;
        if ($action == TIGHT_REQUEST) {
            $classRequest = true;
        }
        // Modul starten:
        $fqmn = Tightloader::findeModul($moduleName);
        $modul = new $fqmn();
        if ($modul && $modul instanceof ModuleInterface) {
            $modul->setCernel(
                $this->getSqlEngine(),
                $this->getErrorLogging(),
                $this->getLanguageSystem(),
                $this->getTemplateEngine(),
                $this->getSessionManagement(),
                $this->getRequest()
            );
            $modul->start();
            // action aus Modul aufrufen
            if ($classRequest) {
                $ergebnis = $modul;
            } else {
                $ergebnis = $modul->superAction($action, $parameters);
                $modul->stop();
                unset($modul);
            }
        }

        return $ergebnis;
    }

    /**
     * Loads the module with the given parameters, and returns the result
     * of the configuration
     *
     * @param string $moduleName    Name of the module (without .php)
     * @param string $configuration The action to perform
     * @param string $parameters    Parameters of the module
     * @return mixed Result of the configuration
     */
    public final function loadSubConfiguration($moduleName, $configuration, array $parameters)
    {
        $ergebnis = false;
        // Modul starten:
        $fqmn = Tightloader::findeModul($moduleName);
        $modul = new $fqmn();
        if ($modul && $modul instanceof ModuleInterface) {
            $modul->setCernel(
                $this->getSqlEngine(),
                $this->getErrorLogging(),
                $this->getLanguageSystem(),
                $this->getTemplateEngine(),
                $this->getSessionManagement(),
                $this->getRequest()
            );
            $modul->start();
            // Config aus Modul aufrufen
            $ergebnis = $modul->config($configuration, $parameters);
            $modul->stop();
            unset($modul);
        }

        return $ergebnis;
    }

    /**
     * Returns if module is installed
     *
     * @param string $moduleName The name of the module
     * @return boolean Installed?
     */
    public final function isModulInstalled($moduleName)
    {
        $output = false;
        $sqlstmt = "
            SELECT uid, modulname
            FROM modulinstalled
            WHERE modulname = ?
        ";
        $this->getSqlEngine()->query($sqlstmt, array(
            $moduleName
        ));
        // Liste der installierten Module lesen:
        while ($eintrag = $this->sqlEngine->rowAssoc()) {
            $output = true;
        }

        return $output;
    }

    /**
     *
     * @param type $modulname
     */
    public final function isModulAllowed()
    {
        $output = false;
        $sqlstmt = "
            SELECT uid
            FROM modulinstalled
            WHERE modulname = ?
        ";
        $this->getSqlEngine()->query($sqlstmt, array(
            $this->getModulName()
        ));
        $result = $this->sqlEngine->rowsAssoc();
        $rechte = $this->sessionManagement->liesSpeicher('rechte');

        foreach ($result as $eintrag) {
            if (\in_array($eintrag['uid'], $rechte)) {
                $output = true;
            }
        }

        return $output;
    }

    /**
     * Sets a variable to the tag list
     *
     * @param string $tagName    Name of the tag to set content to
     * @param string $tagContent Content for the tag
     * @return boolean Success?
     */
    public final function setTagToList($tagName, $tagContent)
    {
        if ((!empty($tagContent)) && (!empty($tagName))) {
            $this->tagliste['tag: ' . $tagName] = $tagContent;
        }
    }

    /**
     * Sets a list of variables to the tag list
     *
     * @param array $tagList List of tags mit array('name' => 'value')
     * @return boolean Success?
     */
    public final function setTagList(array $tagList)
    {
        $this->tagliste = array_merge($this->tagliste, $tagList);
    }

    /**
     * Returns the list of tags
     *
     * @return array Tag list with array('name' => 'value')
     */
    public final function getTagList()
    {
        return $this->tagliste;
    }

    /** SUPER Functions **/

    /**
     * Zeigt Einstellungsseite an
     *
     * @param string $work      Name der Auszuführenden Aktion
     * @param array  $parameter Liste der Parameter
     * @return mixed
     */
    public function superConfig($work = '', array $parameter = array())
    {
        // Modul config
        $this->work           = $work;
        $this->parameterliste = $parameter;
        $this->rewriteEbene   = false;

        $this->loadLocalJavascripts();

        return $this->config();
    }

    /**
     * Führt die Funktion des Moduls aus
     *
     * @param string $work      Name der Auszuführenden Aktion
     * @param array  $parameter Liste der Parameter
     * @return mixed
     */
    public function superAction($work = '', array $parameter = array())
    {
        // Modul action
        $this->work           = $work;
        $this->parameterliste = $parameter;
        $this->rewriteEbene   = false;

        $this->loadLocalJavascripts();

        return $this->action();
    }

    /** Cernel-Access **/

    /**
     * Loads the global configuration
     *
     * @return void
     */
    public final function loadGlobalConfiguration()
    {
        $modulname = 'Einstellungen';
        // EINSTELLUNGEN laden:
        $fqmn = Tightloader::findeModul($modulname);
        $einstell = new $fqmn();
        if ($einstell && $einstell instanceof ModuleInterface) {
            $einstell->setCernel(
                $this->getSqlEngine(),
                $this->getErrorLogging(),
                $this->getLanguageSystem(),
                $this->getTemplateEngine(),
                $this->getSessionManagement(),
                $this->getRequest()
            );
            $data = $einstell->superAction('load');
            if (isset($data['tag: sources'])) {
                $this->getSessionManagement()->setzSpeicher('sources', $data['tag: sources']);
            }
            $this->setTagList($data);
        }
    }

    /**
     * Load local javascripts
     *
     * @return void
     */
    public final function loadLocalJavascripts()
    {
        $module      = str_replace('\\', '/', \get_class($this));
        $path        = \substr($module, 0, \strrpos($module, '/')) . '/Javascripts/';
        $javascripts = $this->getFileAccess()->findFilesInDirectory($path, '*.js');

        $jstext = '';
        foreach ($javascripts as $jsfile) {
            $jstext .= '
                $.getScript("' . \str_replace('\\', '/', $jsfile) . '");';
        }

        $this->templateEngine->injectAtEnd(
            '<script type="text/javascript">' . $jstext . '
            </script>');
    }

    /**
     * Escape XSRF
     *
     * @param string $input
     * @return string
     */
    protected function escape($input)
    {
        return \htmlentities($input);
    }

    /**
     *
     * @param \tightCMS\cernel\type $rewriteEbene
     */
    protected function setRewriteEbene($rewriteEbene)
    {
        $this->rewriteEbene = $rewriteEbene;
    }

    /**
     * Gibt die neue Ebene zurück
     *
     * @return false|string
     */
    public function getRewriteEbene()
    {
        return $this->rewriteEbene;
    }
}

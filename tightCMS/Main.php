<?php
/**
 * Class Main
 *
 * die Hauptklasse des TightCMS, welche den Cernel beihaltet, und das passende Modul lädt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      main.php
 * @package   tightCMS
 * @version   1.0.0
 */

namespace tightCMS;

use tightCMS\cernel\interfaces\Module as ModuleInterface;
use tightCMS\cernel\factory\DatabaseAccess as DatabaseAccessFactory;
use tightCMS\cernel\factory\TemplateEngine as TemplateEngineFactory;
use tightCMS\cernel\Request;
use tightCMS\cernel\SessionManagement;
use tightCMS\cernel\FileAccess;
use tightCMS\cernel\ErrorLogging;
use tightCMS\cernel\LanguageSystem;
use tightCMS\cernel\Module;
use tightCMS\Tightloader;

/**
 * Class Main
 */
class Main
{
    /**
     * The Request-Class
     * @var tightCMS\cernel\Request
     */
    private $request;

    /**
     * The FileAccess-Class
     * @var FileAccess
     */
    private $fileAccess;

    /**
     * The Template-Engine
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * The DatabaseAccess
     * @var DatabaseAccess
     */
    private $dbEngine;

    /**
     * The Session
     * @var SessionManagement
     */
    private $sitzung;

    /**
     * The Language-System
     * @var LanguageSystem
     */
    private $languageTool;

    /**
     * The Error-Logging
     * @var ErrorLogging
     */
    private $errorLogging;

    /**
     * Flag for Ajax or not
     * @var Boolean
     */
    private $isAjax;

    /**
     * The basic template
     * @var string
     */
    private $baseTemplate;

    /**
     * Constructor
     * setzt alle Variablen auf NULL
     *
     * @return self
     */
    function __construct()
    {
        $this->isAjax         = false;
        $this->request        = null;
        $this->templateEngine = null;
        $this->dbEngine       = null;
        $this->sitzung        = null;
        $this->fileAccess     = null;
        $this->languageTool   = null;
        $this->errorLogging   = null;
        $this->baseTemplate   = '';
        // Ajax-Check:
        $xRequest = \filter_input(
            INPUT_SERVER,
            'HTTP_X_REQUESTED_WITH',
            FILTER_SANITIZE_STRING
        );
        if ((! empty($xRequest)) && 'xmlhttprequest' == \strtolower($xRequest)) {
            $this->isAjax = true;
        }
    }

    /**
     * Destructor
     * gibt alle Variablen wieder frei (-> unset l�st Klassen-Destructoren aus)
     *
     * @return void
     */
    function __destruct()
    {
        unset($this->request);
        unset($this->templateEngine);
        unset($this->dbEngine);
        unset($this->sitzung);
        unset($this->fileAccess);
        unset($this->languageTool);
        unset($this->errorLogging);
    }

    /**
     * Initialisierung der Klassen
     * startet alle Klassen, die im Cernel liegen,
     * und verbindet die Datenbank
     *
     * @return boolean Ready to go?
     */
    public function init()
    {
        // alle Klassen aus dem Cernel starten
        $this->request        = new Request();
        $this->sitzung        = new SessionManagement();
        $this->dbEngine       = DatabaseAccessFactory::createPdo();
        $this->fileAccess     = new FileAccess();
        $this->errorLogging   = new ErrorLogging($this->fileAccess);
        $this->languageTool   = new LanguageSystem($this->dbEngine);
        $this->templateEngine = TemplateEngineFactory::createRegEx(
            $this->dbEngine,
            $this->fileAccess
        );
        // Logging starten:
        $this->errorLogging->startLogging(TIGHT_LOGGER . 'logfile.txt');
        $this->errorLogging->writeLog(0, 'Start Init', 'Starting up');
        // Datenbank starten:
        $DbArray = $this->fileAccess->readFilePHParray(TIGHT_CONFIG . 'db.inc.php');
        $this->showErrorAndExit(
            $this->dbEngine->connect(
                $DbArray['DB_User'],
                $DbArray['DB_Pass'],
                $DbArray['DB_Name'],
                $DbArray['DB_Host']
            ), 'DB-Engine'
        );
        $this->sitzung->start();
        // Standard-Sprache laden:
        $this->languageTool->getDefaultLanguage();
        // Sprache aus Session lesen, und setzen
        $sprache = $this->sitzung->getValue('lang');
        if (!empty($sprache)) {
            $this->languageTool->setLanguage($sprache);
        }
        $this->errorLogging->writeLog(0, 'Start Init', 'Init finished.');

        return true;
    }

    /**
     * Arbeit der Klassen
     * Lädt Modul, lädt Template, setzt die Parameter, gibt gefülltes Template und Javascript (AJAX) aus
     *
     * @param boolean $admin Für Admin oder nicht
     * @return boolean
     */
    public function work($admin = false)
    {
        if (! $this->isAjax) {
            $this->errorLogging->clearLog();
        }
        // Aufrufparameter übernehmen:
        $modulname = $this->request->post('modul');
        $ebene     = $this->request->post('ebene');
        $work      = $this->request->post('work');
        $parameter = $this->request->post('param');
        //
        if (empty($ebene)) {
            $ebene = 'index';
        }
        // Fail-Proof
        if (! \is_array($parameter)) {
            $parameter = \json_decode(
                \html_entity_decode($parameter),
                true
            );
        }
        if (! \is_array($parameter)) {
            $parameter = array();
        }
        $this->errorLogging->writeLog(0, 'Start Work', 'Collected data.');
        // Parameter prüfen:
        if (empty($modulname)) {
            // Kein Parameter? Dann halt Defaults:
            if ($admin) {
                // Admin rief main auf
                $modulname = 'Admin';
                $this->baseTemplate = 'admin.html';
            } else {
                // User rief main auf
                $modulname = 'Body';
                $this->baseTemplate = 'index.html';
                if ($this->sitzung->getValue('frontend') == true) {
                    // User rief Main auf, UND IST eingeloggt
                    $modulname = 'Frontend';
                }
            }
        }
        $this->errorLogging->writeLog(0, 'Start Work', 'Using Module: ' . $modulname);
        // DEBUG ausgabe in "logfile.txt" - Beim Neuladen der Seite
        if (\in_array($modulname, array('Admin', 'Body', 'Frontend'))) {
            $this->errorLogging->writeLog(0, 'Webseite', '======== Starting Output ======== ');
        }
        // Modul starten:
        $this->errorLogging->writeLog(0, 'Start Work', 'Starting module: ' . $modulname);
        $this->runModul(
            $modulname,
            $ebene,
            $work,
            $parameter
        );
        // Template laden:
        $meldung = false;
        if ($this->isAjax) {
            $ffaa = $this->fileAccess->findFilesInDirectory(TIGHT_MODULES, $modulname);
            if (count($ffaa) > 0) {
                $meldung = $this->templateEngine->ladeTemplate(
                    str_replace(
                        '\\',
                        '/',
                        $ffaa[0] . '/Templates/' . $ebene . '.phtml'
                    )
                );
            }
        } else {
            $meldung = $this->templateEngine->loadTemplate(
                TIGHT_TEMPLATES . $this->baseTemplate
            );
        }
        $this->showErrorAndExit($meldung, 'TemplateEngine');
        // Modulliste laden, und AJAX-Laden erstellen (Div und Javascript):
        $this->errorLogging->writeLog(0, 'Start Work', 'Preparing output');
        $this->templateEngine->setContent(
            'AJAXed', $this->writeOutput(
                $this->templateEngine->getPlaceholder()
            )
        );
        // Ausgabe auf die Webseite:
        echo $this->templateEngine->render();

        return true;
    }

    /**
     * beenden der Main-Klasse
     *
     * @return void
     */
    public function quit()
    {
        $this->errorLogging->writeLog(0, 'Quit', 'Quitting.');
        $mod = $this->request->get('modul');
        if (empty($mod)) {
            $this->errorLogging->writeLog(
                0,
                'DEBUGGING',
                $this->sitzung->dumbSession()
            );
        }
    }

    /**
     * Startet das Modul, führt Action aus, und stoppt das Modul wieder
     *
     * @param string $modulname Name des Moduls
     * @param string $ebene     Ebene des Templates
     * @param string $work      Auftrag
     * @param array  $parameter Parameter des Auftrags
     * @return void
     */
    private function runModul($modulname, &$ebene, $work, array $parameter)
    {
        // Modul laden:
        $fqmn  = Tightloader::findeModul($modulname);
        $modul = new $fqmn();
        if ($modul && $modul instanceof ModuleInterface) {
            // Mitschreiben:
            $this->errorLogging->writeLog(
                0,
                'Module',
                $modul->getVersion()
            );
            // Kernfunktionen �bergeben:
            $modul->setCernel(
                $this->dbEngine,
                $this->errorLogging,
                $this->languageTool,
                $this->templateEngine,
                $this->sitzung,
                $this->request
            );
            // Modul Starten:
            $modul->start();
            // wenn der Konf-Parameter gesetzt ist
            $konf = $this->request->post('konfig');
            if (!empty($konf)) {
                // Konfigurationsroutine aufrufen
                $modul->superConfig($work, $parameter);
            } else {
                // Aktionsroutine aufrufen
                $modul->superAction($work, $parameter);
            }
            // Tags aus dem Modul holen:
            $tagliste = $modul->getTagList();
            // Alle Tags aus dem Modul zu der TemplateEngine:
            if (\is_array($tagliste)) {
                foreach ($tagliste as $tagName => $tagContent) {
                    $this->templateEngine->setzeContent($tagName, $tagContent);
                }
            }
            $rewrite = $modul->getRewriteEbene();
            if (false !== $rewrite) {
                $ebene = $rewrite;
            }
            // Modul stoppen:
            $modul->stop();
            // Mitschreiben:
            $this->errorLogging->writeLog(0, 'Module', 'Success');

            return true;
        } else {
            // Modul ist durch, Ausgabe vorbereiten:
            $this->errorLogging->writeLog(
                0,
                'ModulError',
                'Modul konnte nicht gestartet werden (' . $modulname . ')'
            );
            echo 'ModulError: Modul konnte nicht gestartet werden (' . $modulname . ')';

            return false;
        }
    }

    /**
     * Erstellt den Javascript aufruf, und setzt die Modul-Tags
     *
     * @param array $modulListe Array mit Modulnamen aus dem Template
     * @return string Javascript, mit dem Ladebefehlen f�r AJAX
     */
    private function writeOutput(array $modulListe)
    {
        $javascript = '';
        // Je nach Parameter die Dinge anzeigen, die aus den Modulen geladen werden.
        foreach ($modulListe as $module) {
            // Modulparameter holen:
            $templiste = $this->getModuleParameter($module);
            if (!empty($templiste['modul'])) {
                // MODUL gefunden:
                $this->templateEngine->setContent(
                    $module, $templiste['divcontainer']
                );
                // Modul lesen:
                $modul = '';
                if (isset($templiste['modul'])) {
                    $modul = $templiste['modul'];
                }
                // Ebene lesen:
                $ebene = '';
                if (isset($templiste['ebene'])) {
                    $ebene = $templiste['ebene'];
                }
                // Target lesen:
                $target = '';
                if (isset($templiste['target'])) {
                    $target = $templiste['target'];
                }
                // Parameter lesen:
                $parameter = '';
                if (isset($templiste['parameter'])) {
                    $parameter = $templiste['parameter'];
                }
                // Javascript zum laden weiterer Module erstellen:
                $javascript .= "tightGMS.tightModule('" . $modul . "', '" . $ebene . "', '" . $parameter . "', [], '" . $target . "');";
            } else {
                // TAG gefunden:
                $this->templateEngine->setzeContent($module, $this->tagliste[$module]);
            }
        }

        return '
<script type="text/javascript">
tightGMS.addOnStart("' . $javascript . '");
</script>' . "\n";
    }

    /**
     * erstellt aus dem Input ein Array.
     * In: "modul: LoginModul"
     * Out: array('modul' => 'LoginModul')
     *
     * @param string $input Platzhaltertext aus Template
     * @return array Assoziatives Array mit Parameterdaten
     */
    private function getModuleParameter($input)
    {
        $output = array();
        $feldname = '';
        // Parameter auftrennen:
        $liste = \explode(' ', $input);
        foreach ($liste as $eintrag) {
            // wenn es ein Doppelpunkt hat
            if (\strpos($eintrag, ':') !== false) {
                // ist es der Feldname.
                $feldname = \substr(\trim($eintrag), 0, -1);
            } else {
                // ansonsten der Wert.
                $output[$feldname] = \trim($eintrag);
            }
        }
        // erstellen der Modul-Container:
        if (!empty($output['modul'])) {
            $output['divcontainer'] = '
                <div id="' . $output['modul'] . '_wait" class="modulwaiter">
                    <img src="images/ajax-loader.gif" alt="Loading..." title="Lade Inhalt" />
                </div>
                <div id="' . $output['modul'] . '" class="modulholder" style="display: none">
                </div>';
        }

        return $output;
    }

    /**
     * On error do exit
     *
     * @param string $message
     * @param string $system
     */
    private function showErrorAndExit($message, $system)
    {
        if (! \is_bool($message)) {
            if (TIGHT_LOG) {
                $this->errorLogging->writeLog(0, $system, $message);
            }
            echo 'Fehler im System.<br>Sehen Sie im Logfile nach.';
            exit;
        }
    }
}

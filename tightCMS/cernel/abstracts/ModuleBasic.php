<?php
/**
 * Abstract ModuleBasic
 *
 * Das Interface für die erweiterungsmodule
 * Nur die hier aufgeführten Funktionen werden vom tightCMS ausgeführt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Module.php
 * @package   tightCMS
 * @version   1.0.0
 */

namespace tightCMS\cernel\abstracts;

use tightCMS\cernel\interfaces\Module as ModuleInterface;

use tightCMS\cernel\interfaces\TemplateEngine as TemplateEngineInterface;
use tightCMS\cernel\interfaces\FileAccess as FileAccessInterface;
use tightCMS\cernel\interfaces\MailSystem as MailSystemInterface;
use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\interfaces\LanguageSystem as LanguageSystemInterface;
use tightCMS\cernel\interfaces\ErrorLogging as ErrorLoggingInterface;
use tightCMS\cernel\interfaces\SessionManagement as SessionManagementInterface;
use tightCMS\cernel\interfaces\LayoutTools as LayoutToolsInterface;
use tightCMS\cernel\interfaces\Request as RequestInterface;

use tightCMS\cernel\TemplateEngine as TemplateEngineClass;
use tightCMS\cernel\FileAccess;
use tightCMS\cernel\MailSystem;
use tightCMS\cernel\DatabaseAccess as DatabaseAccessClass;
use tightCMS\cernel\LanguageSystem;
use tightCMS\cernel\ErrorLogging;
use tightCMS\cernel\SessionManagement;
use tightCMS\cernel\LayoutTools;
use tightCMS\cernel\Request;

/**
 * Abstract ModuleBasic
 */
abstract class ModuleBasic implements ModuleInterface
{
    /**
     * Template Enginge
     * @var TemplateEngineClass
     */
    protected $templateEngine;

    /**
     * File Access
     * @var FileAccess
     */
    protected $fileAccess;

    /**
     * Mail Engine
     * @var MailSystem
     */
    protected $mailSystem;

    /**
     * SQL Engine
     * @var DatabaseAccess
     */
    protected $sqlEngine;

    /**
     * Language System
     * @var LanguageSystem
     */
    protected $languageSystem;

    /**
     * Error Logging
     * @var ErrorLogging
     */
    protected $errorLogging;

    /**
     * Session Management
     * @var SessionManagement
     */
    protected $sessionManagement;

    /**
     * Layout Tools
     * @var LayoutTools
     */
    protected $design;

    /**
     * Request
     * @var Request
     */
    protected $request;

    /**
     * Construct the class with empty data
     */
    public function __construct()
    {
        $this->templateEngine    = null;
        $this->fileAccess        = null;
        $this->mailSystem        = null;
        $this->sqlEngine         = null;
        $this->languageSystem    = null;
        $this->errorLogging      = null;
        $this->sessionManagement = null;
        $this->design            = null;
        $this->request           = null;
    }

    /**
     * Destruct everything
     */
    public function __destruct()
    {
        unset($this->templateEngine);
        unset($this->fileAccess);
        unset($this->mailSystem);
        unset($this->sqlEngine);
        unset($this->languageSystem);
        unset($this->errorLogging);
        unset($this->sessionManagement);
        unset($this->design);
        unset($this->request);
    }

    /**
     * Sets the cernel to the nodule
     *
     * @param DatabaseAccessInterface    $databaseAccess    The active database access
     * @param ErrorLoggingInterface      $errorLogging      The active error logging
     * @param LanguageSystemInterface    $languageSystem    The active language system
     * @param TemplateEngineInterface    $templateEngine    The active template engine
     * @param SessionManagementInterface $sessionManagement The active session management
     * @param RequestInterface           $request           The active request
     * @return void
     */
    public function setCernel(
        DatabaseAccessInterface $databaseAccess,
        ErrorLoggingInterface $errorLogging,
        LanguageSystemInterface $languageSystem,
        TemplateEngineInterface $templateEngine,
        SessionManagementInterface $sessionManagement,
        RequestInterface $request
    ) {
        $this->sqlEngine         = $databaseAccess;
        $this->errorLogging      = $errorLogging;
        $this->languageSystem    = $languageSystem;
        $this->templateEngine    = $templateEngine;
        $this->sessionManagement = $sessionManagement;
        $this->request           = $request;
    }

    /**
     * gibt die Versionsnummer zurück + Copyright
     *
     * @return string "Modulname V1.0 - &copy; Jahr - Autor"
     */
    public function getVersion()
    {
        return 'Basic Version undefinded';
    }

    /**
     *
     * @return TemplateEngineClass
     */
    public function getTemplateEngine()
    {
        if (null === $this->templateEngine) {
            $this->templateEngine = new TemplateEngineClass(
                $this->getSqlEngine(),
                $this->getFileAccess()
            );
        }
        return $this->templateEngine;
    }

    /**
     *
     * @return FileAccess
     */
    public function getFileAccess()
    {
        if (null === $this->fileAccess) {
            $this->fileAccess = new FileAccess();
        }
        return $this->fileAccess;
    }

    /**
     *
     * @return MailSystem
     */
    public function getMailSystem()
    {
        if (null === $this->mailSystem) {
            $this->mailSystem = new MailSystem();
        }
        return $this->mailSystem;
    }

    /**
     *
     * @return DatabaseAccess
     */
    public function getSqlEngine()
    {
        if (null === $this->sqlEngine) {
            $this->sqlEngine = new DatabaseAccessClass();
        }
        return $this->sqlEngine;
    }

    /**
     *
     * @return LanguageSystem
     */
    public function getLanguageSystem()
    {
        if (null === $this->languageSystem) {
            $this->languageSystem = new LanguageSystem($this->getSqlEngine());
        }
        return $this->languageSystem;
    }

    /**
     *
     * @return ErrorLogging
     */
    public function getErrorLogging()
    {
        if (null === $this->errorLogging) {
            $this->errorLogging = new ErrorLogging($this->getFileAccess());
        }
        return $this->errorLogging;
    }

    /**
     *
     * @return SessionManagement
     */
    public function getSessionManagement()
    {
        if (null === $this->sessionManagement) {
            $this->sessionManagement = new SessionManagement();
        }
        return $this->sessionManagement;
    }

    /**
     *
     * @return LayoutTools
     */
    public function getDesign()
    {
        if (null === $this->design) {
            $this->design = new LayoutTools();
        }
        return $this->design;
    }

    /**
     *
     * @return Request
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = new Request();
        }
        return $this->request;
    }

    /**
     *
     * @param TemplateEngineInterface $templateEngine
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setTemplateEngine(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;

        return $this;
    }

    /**
     *
     * @param FileAccessInterface $fileAccess
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setFileAccess(FileAccessInterface $fileAccess)
    {
        $this->fileAccess = $fileAccess;

        return $this;
    }

    /**
     *
     * @param MailSystemInterface $mailSystem
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setMailSystem(MailSystemInterface $mailSystem)
    {
        $this->mailSystem = $mailSystem;

        return $this;
    }

    /**
     *
     * @param DatabaseAccessInterface $sqlEngine
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setSqlEngine(DatabaseAccessInterface $sqlEngine)
    {
        $this->sqlEngine = $sqlEngine;

        return $this;
    }

    /**
     *
     * @param LanguageSystemInterface $languageSystem
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setLanguageSystem(LanguageSystemInterface $languageSystem)
    {
        $this->languageSystem = $languageSystem;

        return $this;
    }

    /**
     *
     * @param ErrorLoggingInterface $errorLogging
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setErrorLogging(ErrorLoggingInterface $errorLogging)
    {
        $this->errorLogging = $errorLogging;

        return $this;
    }

    /**
     *
     * @param SessionManagementInterface $sessionManagement
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setSessionManagement(SessionManagementInterface $sessionManagement)
    {
        $this->sessionManagement = $sessionManagement;

        return $this;
    }

    /**
     *
     * @param LayoutToolsInterface $design
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setDesign(LayoutToolsInterface $design)
    {
        $this->design = $design;

        return $this;
    }

    /**
     *
     * @param RequestInterface $request
     * @return \tightCMS\cernel\abstracts\Module
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }
}

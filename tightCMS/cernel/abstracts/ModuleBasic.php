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
use tightCMS\cernel\interfaces\Request as RequestInterface;

use tightCMS\cernel\factory\TemplateEngine as TemplateEngineFactory;
use tightCMS\cernel\FileAccess;
use tightCMS\cernel\MailSystem;
use tightCMS\cernel\factory\DatabaseAccess as DatabaseAccessFactory;
use tightCMS\cernel\LanguageSystem;
use tightCMS\cernel\ErrorLogging;
use tightCMS\cernel\SessionManagement;
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
     * Returns the versions + copyright
     *
     * @return string "Modulename V1.0 - &copy; Year - Author"
     */
    public function getVersion()
    {
        return 'Basic Version undefinded';
    }

    /**
     * Returns Template Engine
     * 
     * @return TemplateEngineClass
     */
    public function getTemplateEngine()
    {
        if (null === $this->templateEngine) {
            $this->templateEngine = TemplateEngineFactory::createRegEx(
                $this->getSqlEngine(),
                $this->getFileAccess()
            );
        }
        
        return $this->templateEngine;
    }

    /**
     * Returns file access
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
     * Returns mail system
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
     * Returns database access
     * 
     * @return DatabaseAccess
     */
    public function getSqlEngine()
    {
        if (null === $this->sqlEngine) {
            $this->sqlEngine = DatabaseAccessFactory::createPdo();
        }
        
        return $this->sqlEngine;
    }

    /**
     * Returns language system
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
     * Returns error logging
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
     * Returns session management
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
     * Returns request
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
     * Sets template engine
     * 
     * @param TemplateEngineInterface $templateEngine
     * @return self
     */
    public function setTemplateEngine(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;

        return $this;
    }

    /**
     * Sets file access
     *
     * @param FileAccessInterface $fileAccess
     * @return self 
     */
    public function setFileAccess(FileAccessInterface $fileAccess)
    {
        $this->fileAccess = $fileAccess;

        return $this;
    }

    /**
     * Sets mail system
     * 
     * @param MailSystemInterface $mailSystem
     * @return self
     */
    public function setMailSystem(MailSystemInterface $mailSystem)
    {
        $this->mailSystem = $mailSystem;

        return $this;
    }

    /**
     * Set sql engine
     * 
     * @param DatabaseAccessInterface $sqlEngine
     * @return self
     */
    public function setSqlEngine(DatabaseAccessInterface $sqlEngine)
    {
        $this->sqlEngine = $sqlEngine;

        return $this;
    }

    /**
     * Set language system
     *
     * @param LanguageSystemInterface $languageSystem
     * @return self
     */
    public function setLanguageSystem(LanguageSystemInterface $languageSystem)
    {
        $this->languageSystem = $languageSystem;

        return $this;
    }

    /**
     * Set error logging
     * 
     * @param ErrorLoggingInterface $errorLogging
     * @return self
     */
    public function setErrorLogging(ErrorLoggingInterface $errorLogging)
    {
        $this->errorLogging = $errorLogging;

        return $this;
    }

    /**
     * Set session management
     * 
     * @param SessionManagementInterface $sessionManagement
     * @return self
     */
    public function setSessionManagement(SessionManagementInterface $sessionManagement)
    {
        $this->sessionManagement = $sessionManagement;

        return $this;
    }

    /**
     * Sets request
     * 
     * @param RequestInterface $request
     * @return self
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }
}

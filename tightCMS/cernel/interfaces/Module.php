<?php
/**
 * Interface Module
 *
 * The interface for the modules
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Module.php
 * @package   tightCMS/cernel/interfaces
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;
use tightCMS\cernel\TemplateEngine as TemplateEngineInterface;
use tightCMS\cernel\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\LanguageSystem as LanguageSystemInterface;
use tightCMS\cernel\ErrorLogging as ErrorLoggingInterface;
use tightCMS\cernel\SessionManagement as SessionManagementInterface;
use tightCMS\cernel\Request as RequestInterface;

/**
 * interface Module
 */
interface Module extends Cernel
{
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
    );

    /**
     * Call this if the module can be configured
     *
     * @return void
     */
    public function moduleNeedsConfiguration();

    /**
     * Call this if the module has no menu
     *
     * @return void
     */
    public function moduleHasNoMenu();

    /**
     * Call this if the module is for admins
     *
     * @return void
     */
    public function moduleForAdmins();

    /**
     * Call this if the module is undeleteable
     *
     * @return void
     */
    public function moduleIsUndeleteable();

    /**
     * Returns help with manual
     *
     * @return string
     */
    public function help();

    /**
     * Switches the module active/inactive
     *
     * @return void
     */
    public function switchModuleActive();

    /**
     * Installs the module, 
     * sets namespace for translations, 
     * sets parameters for cernel
     *
     * @return boolean Success?
     */
    public function install();

    /**
     * Uninstalls the Module
     *
     * @return boolean Success?
     */
    public function uninstall();

    /**
     * Initialises the module for usage
     *
     * @return boolean Success?
     */
    public function start();

    /**
     * Shows the page for the configuration
     *
     * @return boolean Success?
     */
    public function config();

    /**
     * Shows the page for the action
     *
     * @return boolean Success?
     */
    public function action();

    /**
     * Uninitialises the module after usage
     *
     * @return void
     */
    public function stop();

    /**
     * Loads the module with the given parameters, and returns the result 
     * of the action
     *
     * @param string $moduleName Name of the module (without .php)
     * @param string $action     The action to perform
     * @param string $parameters Parameters of the module
     * @return mixed Result of the action
     */
    public function loadSubAction($moduleName, $action, array $parameters);

    /**
     * Loads the module with the given parameters, and returns the result
     * of the configuration
     *
     * @param string $moduleName    Name of the module (without .php)
     * @param string $configuration The action to perform
     * @param string $parameters    Parameters of the module
     * @return mixed Result of the configuration
     */
    public function loadSubConfiguration($moduleName, $configuration, array $parameters);

    /**
     * Returns if module is installed
     *
     * @param string $moduleName The name of the module
     * @return boolean Installed?
     */
    public function isModulInstalled($moduleName);

    /**
     * Sets a variable to the tag list
     *
     * @param string $tagName    Name of the tag to set content to
     * @param string $tagContent Content for the tag
     * @return boolean Success?
     */
    public function setTagToList($tagName, $tagContent);

    /**
     * Sets a list of variables to the tag list
     *
     * @param array $tagList List of tags mit array('name' => 'value')
     * @return boolean Success?
     */
    public function setTagList(array $tagList);

    /**
     * Returns the list of tags
     *
     * @return array Tag list with array('name' => 'value')
     */
    public function getTagList();

    /********************************************\ 
    |** Check if the following is still needed **|
    \********************************************/
    
    /**
     * Loads the global configuration
     *
     * @return void
     */
    function loadGlobalConfiguration();

    /**
     * Load local javascripts
     *
     * @return void
     */
    public function loadLocalJavascripts();

    /**
     * Returns the parameter at the index
     *
     * @param string|int $index
     * @return mixed
     */
    public function getParameter($index);
}

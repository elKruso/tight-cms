<?php
/**
 * Module Frontend
 *
 * This class serves as a wrapper for the frontend
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Frontend.php
 * @package   tightCMS/Module
 * @version   1.0.1
 */

namespace modules\basics\Frontend;

use tightCMS\cernel\Module;

/**
 * Class Frontend
 */
class Frontend extends Module
{
    /**
     * Returns the Version of the Module
     *
     * @return string
     */
    public function getVersion()
    {
        return 'Module: ' . \get_class($this) . ' V1.0.0 - &copy; Daniel Kruse';
    }

    /**
     * Installation of the Module
     *
     * @return void
     */
    public function install()
    {
        $this->moduleIsUndeleteable();
    }

    /**
     * Start of the Module
     *
     * @return void
     */
    public function start()
    {
        // Initialisation of the Module
    }

    /**
     * Action of the Module
     *
     * @return void
     */
    public function action()
    {
        $this->loadGlobalConfiguration();
    }

    /**
     * Configuration of the Module
     *
     * @return void
     */
    public function config()
    {
        // Show Admin-Configuration
    }

    /**
     * Stop of the Module
     *
     * @return void
     */
    public function stop()
    {
        // Uninitialisation of the Module
    }

    /**
     * Deinstallation of the Module
     *
     * @return void
     */
    public function uninstall()
    {
        // Deinstallations, like SQL-DROP, FileDelete, etc.
    }
}

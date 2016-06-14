<?php
/**
 * Module AdminArea
 *
 * This class serves as a wrapper for the admin area
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      AdminArea.php
 * @package   tightCMS/Module/basic
 * @version   1.0.1
 */

namespace modules\basics\AdminArea;

use tightCMS\cernel\Module;

/**
 * Class AdminArea
 */
class AdminArea extends Module
{
    /**
     * Returns the Version of the Module
     *
     * @return string
     */
    public function getVersion()
    {
        return 'Modul: ' . \get_class($this) . ' V0.9.1 - &copy; Daniel Kruse';
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
        // User-ansicht anzeigen
        $this->setTagToList('welcome', '<center>Willkommen im Adminbereich</center>');
        $user = $this->getSessionManagement()->getValue('user');
        if (!empty($user)) {
            $this->setTagToList('drin', "
                Im Adminbereich gibt es viel zu tun, meistens jedenfalls.<br/>
                <br/>
                Link in der Navigation sind oben die Adminmodule. Von diesen kriegt der User meistens nichts mit, nur von deren Auswirkungen.<br/>
                Beispielsweise nenne ich mal die Designer, welche die Gestaltung der Seite darbringen, aber von den Nutzern nicht angefasst werden sollten.<br/>
                <br/>
                Der Untere Bereich auf der Navigation dient der Konfiguration der Nutzermodule, welche die User auf der Seite bedienen.<br/>
                Die Navigation geh&ouml;rt wie die Pages dazu, da Sie als Admin diese Konfigurieren, und der Nutzer diese sieht.<br/>
                <br/>
                Also, legen Sie los, aber machen sie mir hier nix kaputt.
            ");
        } else {
            $this->setTagToList('drin', "
                Admin-Bereich.<br/>
                <br/>
                Bitte logggen Sie sich ein, um mitzuspielen.
            ");
        }
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

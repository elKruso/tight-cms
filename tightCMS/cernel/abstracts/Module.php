<?php
/**
 * Abstract Module
 *
 * Das Interface für die erweiterungsmodule
 * Nur die hier aufgeführten Funktionen werden vom tightCMS ausgeführt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Module.php
 * @package   tightCMS/cernel/abstracts
 * @version   1.0.0
 */

namespace tightCMS\cernel\abstracts;

use tightCMS\cernel\abstracts\ModuleBasic;
use tightCMS\cernel\interfaces\Module as ModuleInterface;

/**
 * Abstract Module
 */
abstract class Module extends ModuleBasic implements ModuleInterface
{
    /**
     * Module for admins?
     * @var boolean
     */
    private $forAdmin;

    /**
     * Module deleteable?
     * @var boolean
     */
    private $deleteable;

    /**
     * Module has configuration?
     * @var boolean
     */
    private $hasConfig;

    /**
     * Module has menu-entry
     * @var boolean
     */
    private $hasMenu;

    /**
     * Name of module
     * @var string
     */
    private $modulName;

    /**
     * Id of module
     * @var integer
     */
    private $modulId;

    /**
     * Initialise the members
     */
    public function __construct()
    {
        parent::__construct();

        $this->modulId   = -1;
        $temp = get_class($this);
        $this->modulName = substr($temp, strrpos($temp, '\\') + 1);

        $this->hasConfig  = false;
        $this->hasMenu    = true;
        $this->forAdmin   = false;
        $this->deleteable = true;
    }

    /**
     * Free the members
     */
    public function __destruct()
    {
        parent:__destruct();

        unset($this->modulId);
        unset($this->modulName);

        unset($this->hasConfig);
        unset($this->hasMenu);
        unset($this->forAdmin);
        unset($this->deleteable);
    }

    /**
     * Call this if the module is undeleteable
     *
     * @return self
     */
    public final function moduleIsUndeleteable()
    {
        $this->deleteable = false;
        
        return $this;
    }

    /**
     * Returns true if module is deleteable
     *
     * @return boolean
     */
    protected final function isModuleDeleteable()
    {
        return $this->deleteable;
    }

    /**
     * Call this if the module is for admins
     *
     * @return self
     */
    public final function moduleForAdmins()
    {
        $this->forAdmin = true;
        
        return $this;
    }

    /**
     * Returns true if module is for admins
     *
     * @return boolean
     */
    protected final function isForAdmin()
    {
        return $this->forAdmin;
    }

    /**
     * Call this if the module can be configured
     *
     * @return self
     */
    public final function moduleNeedsConfiguration()
    {
        $this->hasConfig = true;
        
        return $this;
    }

    /**
     * Call this if the module has no menu
     *
     * @return self
     */
    public final function moduleHasNoMenu()
    {
        $this->hasMenu = false;
        
        return $this;
    }

    /**
     * Has the module a menu entry?
     *
     * @return boolean
     */
    protected final function hasMenu()
    {
        return $this->hasMenu;
    }

    /**
     * Has the module a configuration
     *
     * @return boolean
     */
    protected final function hasConfig()
    {
        return $this->hasConfig;
    }

    /** The Super Functions **/

    /**
     * Installs the module, set up translation, and parameters for cernel
     *
     * @return boolean Success?
     */
    public function superInstall()
    {
        $this->install();

        $temp = get_class($this);
        $this->modulName = substr($temp, strrpos($temp, '\\') + 1);
        // Save to database
        $this->getSqlEngine()->query("
            REPLACE INTO modulInstalled (
                modulName,
                active,
                admin,
                deleteable
            ) VALUES (
                ?,
                '1',
                ?,
                ?
            )
        ", array(
            $this->modulName,
            $this->isForAdmin()?1:0,
            $this->isModuleDeleteable()?1:0
        ));

        $this->modulId = -1;
        $this->getSqlEngine()->query("
            SELECT uid
            FROM modulInstalled
            WHERE modulName = ?
        ", array(
            $this->modulName
        ));
        if ($this->sqlEngine->rowCount() > 0) {
            $eintrag = $this->sqlEngine->rowAssoc();
            $this->modulId = $eintrag['uid'];
        }
    }

    /**
     * Deinstalls module
     *
     * @return boolean Success?
     */
    public function superUninstall()
    {
        // TODO: prüfen, ob existiert... und exception werfen
        $this->uninstall();

        $temp = get_class($this);
        $this->modulName = substr($temp, strrpos($temp, '\\') + 1);
        $this->getSqlEngine()->query("
            DELETE
            FROM modulInstalled
            WHERE modulName = ?
        ", array(
            $this->modulName
        ));
    }

    /**
     * Switches the module active/inactive
     *
     * @return boolean Success?
     */
    public final function switchModuleActive()
    {
        $this->getSqlEngine()->query("
            UPDATE modulInstalled
            SET active = IF (active = 0, 1, 0)
            WHERE modulName = ?
        ", array(
            get_class($this)
        ));
    }

    /**
     * Returns Modulename
     *
     * @return string
     */
    public function getModulName()
    {
        return $this->modulName;
    }
}

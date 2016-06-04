<?php
/**
 * Factory DatabaseAccess
 *
 * The factory for the database access
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      DatabaseAccess.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel\factory;

use tightCMS\cernel\PdoAccess;
use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;

/**
 * Factory DatabaseAccess
 */
class DatabaseAccess
{
    /**
     * Creates an instance of PdoAccess
     * 
     * @return DatabaseAccessInterface
     */
    public static function createPdo()
    {
        return new PdoAccess();
    }
}

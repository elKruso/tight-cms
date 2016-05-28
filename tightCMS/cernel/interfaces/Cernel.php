<?php
/**
 * Interface Cernel
 * Basic interface for all cernel-modules
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Cernel.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

/**
 * Cernel interface for global functions
 */
interface Cernel
{
    /**
     * Returns the Version of the cernel-module
     * 
     * @return string VersionNumber
     */
    public function getVersion();

    /**
     * Returns information in an array
     * 
     * @return array containing:
     * - author
     * - licence
     * - etc
     */
    public function getInformation();
}

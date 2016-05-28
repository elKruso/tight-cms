<?php
/**
 * Abstract Cernel
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      Cernel.php
 * @package   tightCMS/cernel/abstracts
 * @version   1.0.0
 */

namespace tightCMS\cernel\abstracts;

use tightCMS\cernel\interfaces\Cernel as CernelInterface;

/**
 * Abstract for cernel interface
 */
abstract class Cernel implements CernelInterface
{
    /**
     * Returns the Version of the cernel-module
     * @return string VersionNumber
     */
    public function getVersion()
    {
        return '0.0.0';
    }

    /**
     * Returns information in an array
     * @return array containing:
     * - author
     * - licence
     * - etc
     */
    public function getInformation()
    {
        return array(
            'Name' => get_class(),
            'Version' => $this->getVersion(),
            'Author' => '',
            'Licence' => 'CC-BY',
            'Homepage' => ''
        );
    }
}

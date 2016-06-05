<?php
/**
 * Factory TemplateEngine
 *
 * The factory for the template engine
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      TemplateEngine.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel\factory;

use tightCMS\cernel\interfaces\TemplateEngine as TemplateEngineInterface;
use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\interfaces\FileAccess as FileAccessInterface;
use tightCMS\cernel\TemplateEngineRegEx;

/**
 * Class TemplateEngine
 */
class TemplateEngine extends TemplateEngineRegEx implements TemplateEngineInterface
{
    /**
     * Returns an instance of TemplateEngineRegEx
     * 
     * @return TemplateEngineInterface
     */
    public static function createRegEx(
        DatabaseAccessInterface $databaseAccess,
        FileAccessInterface $fileAccess
    ) {
        return new TemplateEngineRegEx(
            $databaseAccess,
            $fileAccess
        );
    }
}

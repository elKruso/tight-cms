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
    public static function createRegEx()
    {
        return new TemplateEngineRegEx();
    }
}

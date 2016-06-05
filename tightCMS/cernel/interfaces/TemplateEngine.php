<?php
/**
 * Interface TemplateEngine
 *
 * The interface for the template engine
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      TemplateEngine.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;
use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\interfaces\FileAccess as FileAccessInterface;

/**
 * interface TemplateEngine
 */
interface TemplateEngine extends Cernel
{
    /**
     * Constructor
     *
     * @param DatabaseAccessInterface $databaseAccess
     * @param FileAccessInterface     $fileAccess
     */
    public function __construct(
        DatabaseAccessInterface $databaseAccess,
        FileAccessInterface $fileAccess
    );

    /**
     * Loads the template file
     *
     * @param string $filename Path to the template file
     * @return boolean
     */
    public function loadTemplate($filename);

    /**
     * Loads the template from the database
     *
     * @param string $templateName Name of the template
     * @param string $level        Level of the template
     * @return boolean|string False if not found, else content
     */
    public function loadTemplateFromDB($templateName, $level = '');

    /**
     * Takes the content as a template
     *
     * @param string $content Content to set
     * @return boolean
     */
    public function setTemplate($content);

    /**
     * Returns the elements to replace
     *
     * @param boolean $tags Return all placeholder inclusive tags
     * @return array List of placeholder
     */
    public function getPlaceholder($tags = false);

    /**
     * Sets values to replace "[[fieldname]]" with
     *
     * @param string $fieldname Fieldname to be replaced
     * @param string $content   The content to set 
     * @return boolean
     */
    public function setContent($fieldname, $content);

    /**
     * Injects code to the end of the template
     * 
     * @param string $jsText
     * @return void
     */
    public function injectAtEnd($jsText);

    /**
     * Renders the output
     *
     * @return string Rendered template
     */
    public function render();

    /**
     * Resets the contents
     * 
     * @return self
     */
    public function resetData();

    /**
     * Dumps the full list of placeholders and content to the screen
     * 
     * @return array
     */
    public function dumpPlaceholder();
}

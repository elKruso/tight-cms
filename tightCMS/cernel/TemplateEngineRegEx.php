<?php
/**
 * Class Templates
 *
 * A template engine based on regex
 * Replaces the "[[fieldname]]" with the content set by "setContent".
 * Renders the template.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      TemplateEngine.php
 * @package   tightCMS/Cernel
 * @version   1.1.0
 */

/** -----------------------------------------------------------------------
 * @tutorial
 * Im Template befinden sich einige Feldnamen, die in [[]] stecken, zb. "Hallo [name]"
 * Dann sollte im Folgenden der Feldname "name" lauten:
 *
 * $templateEngine = new templates();
 * $templateEngine->ladeTemplate('path/to/template/file.htm');
 * while (nochDatenUebrig) {
 *      $templateEngine->setzeContent('feldname', 'dieDaten');
 * }
 * echo $templateEngine->ausgeben();		// endgültige ausgabe
 ** -----------------------------------------------------------------------
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\TemplateEngine as TemplateEngineInterface;
use tightCMS\cernel\abstracts\TemplateEngine as TemplateEngineAbstract;

/**
 * Class TemplateEngine
 */
class TemplateEngineRegEx 
    extends TemplateEngineAbstract 
    implements TemplateEngineInterface
{
    /**
     * Returns the elements to replace
     *
     * @param boolean $tags Return all placeholder inclusive tags
     * @return array List of placeholder
     */
    public function getPlaceholder($tags = false)
    {
        $searched = 'modul';
        $matches  = array();
        if ($tags) {
            $searched = 'tag';
        }
        // Find placeholders
        preg_match_all(
            '/\[\[(' . $searched . ': [_A-Za-z0-9:\s]*)\]\]/',
            $this->templateContent,
            $matches
        );

        return $matches[1];
    }

    /**
     * Replaces the placeholder with the content
     *
     * @return boolean Success?
     */
    protected function replacePlaceholders()
    {
        if (strlen($this->templateContent) === 0) {
            $this->addError('#0012: Template "' .
                $this->templateName . '" is empty!');
            return false;
        }
        $template = $this->replaceTag(
            $this->gibPlatzhalter(false), 
            $this->templateContent
        );
        $templateStep = $this->replaceTag(
            $this->gibPlatzhalter(true), 
            $template
        );
        $this->templateOutput = $this->replaceTag(
            array('AJAXed'),
            $templateStep
        );

        return true;
    }

    /**
     * Replaces the given tags
     *
     * @param array  $list     The taglist
     * @param string $template The template to fill
     * @return string
     */
    protected function replaceTag($list, $template)
    {
        foreach ($list as $value) {
            $replacer = '';
            if (isset($this->templateArray[$value])) {
                $replacer = $this->templateArray[$value];
            }
            $template = str_replace(
                $this->tagStart . $value . $this->tagEnd,
                $replacer,
                $template
            );
        }

        return $template;
    }
}

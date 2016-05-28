<?php
/**
 * Interface LanguageSystem
 *
 * The interface for the translations
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      LanguageSystem.php
 * @package   tightCMS/Cernel/interfaces
 * @version   f1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface LanguageSystem
 */
interface LanguageSystem extends Cernel
{
    /**
     * Returns the default language
     *
     * @return string ISO of the language (DE, EN, RU, FR, ...)
     */
    public function getDefaultLanguage();

    /**
     * Sets the language to load
     *
     * @param string $languageId ISO of the language (DE, EN, RU, FR, ...)
     * @return boolean Success?
     */
    public function setLanguage($languageId);

    /**
     * Returns true if translation is neccessary
     *
     * @return boolean Translation required?
     */
    public function needsTranslation();

    /**
     * Returns the translated text
     *
     * @param string $templateName  Name of the template
     * @param string $templateLevel Name of the level
     * @return String The translated template
     */
    public function translate($templateName, $templateLevel);

    /**
     * Alias for translate
     */
    public function _($pageText, $pageId);
}

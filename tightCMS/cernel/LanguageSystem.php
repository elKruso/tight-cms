<?php
/**
 * Class LanguageSystem
 *
 * Die Klasse für die Sprachverwaltung
 * Nur die hier aufgeführten Funktionen werden vom tightCMS ausgeführt.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      LanguageSystem.php
 * @package   tightCMS/Cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\interfaces\DatabaseAccess as DatabaseAccessInterface;
use tightCMS\cernel\interfaces\LanguageSystem as LanguageSystemInterface;
use tightCMS\cernel\abstracts\Cernel;

/**
 * Class LanguageSystem
 */
class LanguageSystem extends Cernel implements LanguageSystemInterface
{
    /**
     * The database access
     * @var DatabaseAccess
     */
    private $sqlEngine;

    /**
     * The language
     * @var string
     */
    private $lang;

    /**
     * The language id
     * @var int
     */
    private $langId;

    /**
     * The language default
     * @var string
     */
    private $langDef;

    /**
     * The language default id
     * @var int
     */
    private $langDefId;

    /**
     * Constructs the class
     *
     * @param DatabaseAccessInterface $databaseAccess Database access
     */
    function __construct(DatabaseAccessInterface $databaseAccess)
    {
        $this->sqlEngine = $databaseAccess;

        $this->lang      = '';
        $this->langId    = 0;
        $this->langDef   = '';
        $this->langDefId = 0;
    }

    /**
     * Frees the data
     */
    function __destruct()
    {
        unset($this->lang);
        unset($this->langId);
        unset($this->langDef);
        unset($this->langDefId);
    }

    /**
     * Returns the default language
     *
     * @return string ISO of the language (DE, EN, RU, FR, ...)
     */
    public function getDefaultLanguage()
    {
        $this->sqlEngine->query("
            SELECT uid, kuerzel
            FROM languages
            WHERE primar = '1'
        ");
        if ($this->sqlEngine->rowCount() > 0) {
            $result = $this->sqlEngine->rowAssoc();
            // This ids can change
            $this->lang      = $result['kuerzel'];
            $this->langId    = $result['uid'];
            // These ids are fixed
            $this->langDef   = $result['kuerzel'];
            $this->langDefId = $result['uid'];
        }

        return $this;
    }

    /**
     * Sets the language to load
     *
     * @param string $languageId ISO of the language (DE, EN, RU, FR, ...)
     * @return boolean Success?
     */
    public function setLanguage($languageId)
    {
        $this->langId = $languageId;
        // ISO of the id
        $this->sqlEngine->query("
            SELECT kuerzel
            FROM languages
            WHERE uid = ?
        ", array(
            $this->langId
        ));
        if ($this->sqlEngine->rowCount() > 0) {
            $this->lang = $this->sqlEngine->rowAssoc()['kuerzel'];
        }

        return true;
    }

    /**
     * Returns true if translation is neccessary
     *
     * @return boolean Translation required?
     */
    public function needsTranslation()
    {
        if (empty($this->lang)) {
            $this->getDefaultLanguage();
        }

        return ($this->langId !== $this->langDefId);
    }

    /**
     * Returns the translated text
     *
     * @param string $templateName  Name of the template
     * @param string $templateLevel Name of the level
     * @return String The translated template
     */
    public function translate($templateName, $templateLevel)
    {
        // Read translation
        $this->sqlEngine->query("
            SELECT content, feld
            FROM translations
            WHERE sprache = ?
                    AND seite = ?
            ORDER BY feld ASC
        ", array(
            $this->langId,
            $templateName,
            $templateLevel
        ));
        // TODO: translation
    }

    /**
     * Alias for translate
     * 
     * @param string $templateName  Name of the template
     * @param string $templateLevel Name of the level
     * @return String The translated template
     */
    public function _($templateName, $templateLevel)
    {
        return $this->translate($templateName, $templateLevel);
    }
}

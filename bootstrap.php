<?php
/**
 * tightCMS
 *
 * the bootstrap
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      bootstrap.php
 * @package   tightCMS
 * @version   2.0.0
 */

use tightCMS\Tightloader;
use tightCMS\cernel\FileAccess;
use Doctrine\ORM\Tools\Setup as OrmSetup;
use Doctrine\ORM\EntityManager as OrmEntityManager;

// Setup autoloader
require_once __DIR__ . '/tightCMS/Tightloader.php';

new Tightloader();

/*******************
 * Setup externals *
 *******************/

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = OrmSetup::createAnnotationMetadataConfiguration(array(TIGHT_BASEDIR . '/modules'), $isDevMode);
// database configuration parameters
$dbData = (new FileAccess())->readFilePHParray(TIGHT_CONFIG . 'db.inc.php');
$conn = array(
    'driver' => 'pdo_mysql',
    'user' => $dbData['DB_User'],
    'password' => $dbData['DB_Pass'],
    'dbname' => $dbData['DB_Name']
);
// obtaining the entity manager
$entityManager = OrmEntityManager::create($conn, $config);

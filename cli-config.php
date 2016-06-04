<?php
/**
 * tightCMS
 *
 * The cli config
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      cli-config.php
 * @package   tightCMS
 * @version   2.0.0
 */

require_once __DIR__ . '/bootstrap.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);

<?php
/**
 * Expresso Lite
 * Bootstrap to be included in all PHP files.
 * It performs the following:
 *  1 - Loads the configuration and version number files
 *  2 - Register class loader
 *  3 - Starts/resume session
 *
 * @license   http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author    Charles Wust <charles.wust@serpro.gov.br>
 * @copyright Copyright (c) 2014-2015 Serpro (http://www.serpro.gov.br)
 */

require_once (dirname(__FILE__).'/../version.php');
require_once (dirname(__FILE__).'/../conf.php');
require_once (dirname(__FILE__) .'/SplClassLoader.php');

define('LITE_BASE_DIR', dirname(__FILE__) . '/..');

$classLoader = new SplClassLoader('ExpressoLite', dirname(__FILE__));
$classLoader->register();

@session_start();


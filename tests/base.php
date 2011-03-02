<?php

error_reporting(E_ALL|E_STRICT);
set_time_limit(0);

define('WWW_DIR', dirname(__FILE__).'/..');
define('APP_DIR', WWW_DIR);
define('LIBS_DIR',WWW_DIR.'/../../!libs');


require_once dirname(__FILE__) . '/run.php';

if (!defined('NETTE')) require_once LIBS_DIR.'/Nette/loader.php';
require_once LIBS_DIR.'/xdibi/dibi.php';

if (PHP_SAPI !== 'cli' AND !isset($_GET['command']))
{
	Debug::enable(false);
	Debug::$showBar = true;
	Debug::$strictMode = true;
}
Environment::setVariable('tempDir', APP_DIR . '/temp');

dibi::connect(array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'user' => 'root',
	'password' => '',
	'database' => 'test',
	'charset' => 'utf8',
	'profiler' => true,
));


require_once dirname(__FILE__) . '/../../dump.php';

require_once dirname(__FILE__) . '/../../DataSourceX/DataSourceX/extension.php';
require_once dirname(__FILE__) . '/../Model/loader.php';

class Model extends RepositoriesCollection
{

}
/**
 * @var Model
 * @global
 */
$model = new Model;

TestHelpers::$oldDump = true;

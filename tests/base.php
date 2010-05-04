<?php

error_reporting(E_ALL|E_STRICT);
set_time_limit(0);

define('WWW_DIR', dirname(__FILE__).'/..');
define('APP_DIR', WWW_DIR);
define('LIBS_DIR',WWW_DIR.'/../../!libs');


require_once dirname(__FILE__) . '/run.php';

require_once LIBS_DIR.'/Nette/loader.php';
require_once LIBS_DIR.'/xdibi/dibi.php';

dibi::connect(array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'user' => 'root',
	'password' => '',
	'database' => 'test',
	'charset' => 'utf8',
	'profiler' => true,
));

if (PHP_SAPI !== 'cli' AND !isset($_GET['command']))
{
	Debug::enable(false);
	Debug::enableProfiler();
	Debug::$strictMode = true;
	dibi::getProfiler()->useFirebug = true;
}


/** Dump */
function d($var)
{
	if (func_num_args() > 1) $var = func_get_args();
	Debug::dump($var);
}
/** Console dump */
function cd()
{
	// TODO nefunguje grrrr
	foreach (func_get_args() as $var)
		Debug::consoleDump($var);
}
/** Profiler dump */
function dd($var)
{
	if (func_num_args() > 1) $var = func_get_args();
	d::$d[] = $var;
	Debug::addColophon(create_function('','return Debug::dump(d::$d['.(count(d::$d)-1).'],true);'));
}
/** Storage of profiler dump */
class d {static $d=array();}


require_once dirname(__FILE__) . '/../Model/loader.php';

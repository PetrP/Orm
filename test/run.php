<?php

set_time_limit(0);

require_once __DIR__ . '/libs/Nette/loader.php';
Environment::setMode('console');

set_include_path(__DIR__ . '/libs/PHPUnit');

require_once 'PHP/CodeCoverage/Filter.php';
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUnit');

if (extension_loaded('xdebug')) {
		xdebug_disable();
}

require_once 'PHPUnit/Autoload.php';

//define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');
//PHPUnit_TextUI_Command::main(false);

class xxx extends PHPUnit_TextUI_Command
{
	public function run(array $argv, $printer = NULL)
	{
		$this->arguments['printer'] = $printer;
		parent::run($argv, false);
	}
}

class xxxx extends PHPUnit_Util_TestDox_ResultPrinter
{
	private $file;

	public $debug = false;

	protected $printsHTML = TRUE;

	public function __construct()
	{
		$this->file = tempnam(sys_get_temp_dir(), 'test');
		parent::__construct(fopen($this->file, 'w'));
	}

	public function render()
	{
		echo file_get_contents($this->file);
		@unlink($this->file);
	}

	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
	{
		if ($this->debug) Debug::toStringException($e);
		$this->ass($test, $e, 'Failure');
		parent::addFailure($test, $e, $time);
	}

	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		if ($this->debug) Debug::toStringException($e);
		$this->ass($test, $e, 'Error');
		parent::addError($test, $e, $time);
	}

	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->ass($test, $e, 'Incomplete');
		parent::addIncompleteTest($test, $e, $time);
	}

	protected function ass(PHPUnit_Framework_Test $test, Exception $e, $state)
	{
		$r = new ReflectionClass($test);
		$dir = preg_replace('#^' . preg_quote(__DIR__ . '\\', '#') . '#si', '', $r->getFileName());
		$class = preg_replace('#^Tests\\\\(.*)Test$#si', '$1',get_class($test));
		$method = lcfirst($this->currentTestMethodPrettified);
		$this->write("<h2>{$state} <a href='?dir={$dir}'>{$class}::{$method}</a></h2>");
		$this->write(
			$state === 'Error' ?
			'<p><pre>' . htmlspecialchars($e) . '</pre></p>' :
			'<p>' . htmlspecialchars($e->getMessage()) . '</p>'
		);
	}

	protected function endRun()
	{
		if (!$this->failed)
		{
			$this->write('<h1>OK</h1>');
		}
		else
		{
			$this->write("<h1>FAILURES! {$this->failed}</h1>");
		}
		if ($this->incomplete) $this->write("Incomplete: {$this->incomplete}<br>");
		if ($this->skipped) $this->write("Skipped: {$this->skipped}<br>");
	}

}

$dirs = array();
if (isset($_GET['dir']))
{
	$dirs[] = __DIR__ . '\\' . $_GET['dir'];
}
else
{
	$dirs[] = __DIR__ . '\unit';
}


$arg = array_merge(array('--no-globals-backup'), explode(' ', trim(
' --strict'
)), $dirs);

$command = new xxx;
$printer = new xxxx;
$printer->debug = isset($_GET['dir']);
echo "<!DOCTYPE HTML>\n<meta charset='utf-8'>";
if (isset($_GET['dir'])) echo "<h1><a href='?'>back</a></h1>";
$command->run($arg, $printer);
$printer->render();

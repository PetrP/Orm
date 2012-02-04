<?php

namespace Orm\Builder;

use Nette\Utils\Finder;
use Nette\Object;

/**
 * Generuje testy pro php <= 5.2.5
 */
class PartialSupportTestsConverter extends Object
{

	/** @var string */
	private $php52TestDir;

	/** @var string */
	private $phpPartialTestDir;

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($php52TestDir, $phpPartialTestDir)
	{
		$this->php52TestDir = $php52TestDir;
		$this->phpPartialTestDir = $phpPartialTestDir;
		if (basename($this->php52TestDir) !== 'tests' OR basename($this->phpPartialTestDir) !== 'tests')
		{
			throw new \Exception;
		}
	}

	public function convert()
	{
		Helpers::wipeStructure($this->phpPartialTestDir);
		Helpers::copyStructure($this->php52TestDir, $this->phpPartialTestDir);

		$this->convertCases();
		$this->convertLibs();
		$this->addMissingFunctions();
	}

	/**
	 * http://orm.petrprochazka.com/forum/topic/96/entity-magicke-pretezovani-settru-a-gettru/
	 * php <= 5.2.5
	 */
	private function convertCases()
	{
		foreach (Finder::findFiles('*')->from($this->phpPartialTestDir . '/cases') as $file)
		{
			$c = file_get_contents($file);

			// php <= 5.2.5
			$c = preg_replace_callback('#(.*)parent::((?:get|set)[^(]+)(\([^;]+);(.*)#i', function ($m) {
				list(, $start, $method, $args, $end) = $m;
				return
					"if (method_exists(get_parent_class(), '$method')) {\n" .
					"{$start}parent::$method{$args};{$end}\n" .
					"} else {\n" .
					"{$start}parent::__call('$method', array{$args});{$end}\n" .
					"}"
				;
			}, $c);

			file_put_contents($file, $c);
		}
	}

	private function convertLibs()
	{
		foreach (Finder::findFiles('*')->from($this->phpPartialTestDir . '/libs') as $file)
		{
			$c = file_get_contents($file);

			// php <= 5.2.4
			$c = str_replace('debug_backtrace(FALSE)', 'debug_backtrace()', $c);

			// php <= 5.2.2
			$c = preg_replace('#(\$[a-z0-9_]+)->getRealPath\(\)#si', 'realpath($1->getPathname())', $c);
			$c = preg_replace('#(\$[a-z0-9_]+)->getBasename\(\)#si', 'basename($1->getPathname())', $c);

			file_put_contents($file, $c);
		}
	}

	private function addMissingFunctions()
	{
		// php <= 5.2.0
		file_put_contents($this->phpPartialTestDir . '/loader.php',
		file_get_contents($this->phpPartialTestDir . '/loader.php') . <<<'EOT'

if (!function_exists('sys_get_temp_dir'))
{
	function sys_get_temp_dir()
	{
		foreach (array_filter(array(getenv('TMP'), getenv('TEMP'), getenv('TMPDIR'))) as $temp)
		{
			return $temp;
		}
		// if the directory does not exist, tempnam() may generate a file in the system's temporary directory.
		// Directory __FILE__ does not exist.
		$temp = tempnam(__FILE__, '');
		if (file_exists($temp))
		{
				unlink($temp);
				return dirname($temp);
		}
		return NULL;
	}
}
EOT

		);

	}

}

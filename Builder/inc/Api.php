<?php

namespace Orm\Builder;

use Nette\Object;
use Exception;
use ApiGen\Generator;
use Nette\Utils\Finder;

class Api extends Object implements IZipperFiles
{
	/** @array of files */
	private $dirs = array();

	/**
	 * @param string
	 * @param string
	 */
	public function generate($source, $destination)
	{
		$this->dirs[] = $destination;
		$config = new ApiConfig($source, $destination);
		$generator = new Generator($config);
		$generator->parse();
		if ($config->wipeout AND is_dir($config->destination) AND !$generator->wipeOutDestination())
		{
			throw new Exception('Cannot wipe out destination directory');
		}
		$generator->generate();
	}

	/** @return array */
	public function getFiles()
	{
		$files = array();
		if ($this->dirs)
		{
			foreach (Finder::findFiles('*')->from($this->dirs) as $file)
			{
				$files[] = $file;
			}
		}
		return $files;
	}

}

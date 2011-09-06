<?php

namespace Orm\Builder;

use Nette\Object;
use ZipArchive;
use Exception;
use Nette\Utils\Strings;

class Zipper extends Object
{
	/** @var ZipArchive */
	private $zip;

	/** @var string */
	private $rootDir;

	/** @var array of dir */
	private $matches = array();

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($file, $rootDir)
	{
		$this->zip = new ZipArchive;
		@unlink($file);
		$r = $this->zip->open($file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		if ($r !== true)
		{
			throw new Exception($r);
		}
		$this->rootDir = $rootDir;
		if (!is_dir($this->rootDir))
		{
			throw new Exception($this->rootDir);
		}
	}

	/** @param string */
	public function addMatch($match)
	{
		$match = realpath($match);
		if (!$match) throw new Exception;
		$this->matches[] = $match;
	}

	/** @param Builder */
	public function add(Builder $builder)
	{
		foreach ($builder->getFiles() as $fromPath => $toPath)
		{
			if (!$this->isMatch($fromPath))
			{
				continue;
			}
			if (!Strings::startsWith($toPath, $this->rootDir))
			{
				throw new Exception($toPath);
			}
			if (!$this->zip->addFile($toPath, substr($toPath, strlen($this->rootDir)+1)))
			{
				throw new Exception;
			}
		}
	}

	public function save()
	{
		if (!$this->zip->close())
		{
			throw new Exception;
		}
	}

	/**
	 * @param Builder
	 * @return bool
	 */
	private function isMatch($fromPath)
	{
		foreach ($this->matches as $match)
		{
			if (Strings::startsWith($fromPath, $match))
			{
				return true;
			}
		}
		return false;
	}

}

<?php

namespace Orm\Builder;

use Nette\Object;
use ZipArchive;
use Exception;
use Nette\Utils\Strings;

class Zipper extends Object
{
	/** @var bool */
	private $enable;

	/** @var ZipArchive */
	private $zip;

	/** @var string */
	private $rootDir;

	/** @var array of dir */
	private $matches = array();

	/**
	 * @param string
	 * @param string
	 * @param true
	 */
	public function __construct($file, $rootDir, $enable = true)
	{
		$this->enable = $enable;
		if (!$this->enable) return;
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

	/** @param IZipperFiles */
	public function add(IZipperFiles $files)
	{
		if (!$this->enable) return;
		foreach ($files->getFiles() as $file)
		{
			if (!Strings::startsWith($file, $this->rootDir))
			{
				throw new Exception($file);
			}
			if (!is_file($file)) throw new Exception($file);
			$localName = strtr(substr($file, strlen($this->rootDir)+1), '\\', '/');
			if (!$this->zip->addFile($file, $localName))
			{
				throw new Exception;
			}
		}
	}

	public function save()
	{
		if (!$this->enable) return;
		if (!$this->zip->close())
		{
			throw new Exception;
		}
	}

}

interface IZipperFiles
{
	/** @return array of filenames */
	public function getFiles();
}

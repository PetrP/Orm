<?php

namespace Orm\Builder;

use Nette\Utils\Strings;
use Nette\Utils\Finder;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;
use Nette\Object;
use SplFileInfo;
use Exception;

class Readme extends Object implements IZipperFiles
{

	private $content;

	/** @var string[] */
	private $files = array();

	/**
	 * @param string filename
	 * @param VersionInfo
	 * @param string|NULL
	 */
	public function __construct($from, VersionInfo $info, $extraVersionText = NULL)
	{
		$content = file_get_contents($from);
		$tmp = "Orm {$info->tag}" . ($extraVersionText ? " ({$extraVersionText})" : '') . " released on {$info->date}";
		$tmp = "\n" . $tmp . "\n" . str_repeat('=', strlen($tmp));
		$content = Strings::replace($content, '#^\n?Orm\n===#si', $tmp);
		$content = Strings::replace($content, '#(?<=\n|^)```[^\n]*(\n|$)#s', '');
		$content = Strings::replace($content, '#\[([^\]]+)\]\(([^\)]+)\)#s', '$1 ($2)');
		$this->content = $content;
	}

	/**
	 * @param string filename
	 * @return Readme $this
	 */
	public function addTo($to)
	{
		@mkdir(dirname($to), 0777, true);
		file_put_contents($to, $this->content);
		$this->files[$to] = $to;
		return $this;
	}

	/** @return array of filenames */
	public function getFiles()
	{
		return $this->files;
	}
}

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

	/** @var string */
	private $file;

	/**
	 * @param string filename
	 * @param string filename
	 * @param VersionInfo
	 * @param string|NULL
	 */
	public function __construct($from, $to, VersionInfo $info, $extraVersionText = NULL)
	{
		$content = file_get_contents($from);
		$tmp = "Orm {$info->tag}" . ($extraVersionText ? " ({$extraVersionText})" : '') . " released on {$info->date}";
		$tmp = "\n" . $tmp . "\n" . str_repeat('=', strlen($tmp));
		$content = Strings::replace($content, '#^\n?Orm\n===#si', $tmp);
		$content = Strings::replace($content, '#(?<=\n|^)```[^\n]*(\n|$)#s', '');
		$content = Strings::replace($content, '#\[([^\]]+)\]\(([^\)]+)\)#s', '$1 ($2)');
		file_put_contents($this->file = $to, $content);
		register_shutdown_function(function () use ($to) {
			@unlink($to);
		});
	}

	/** @return array of filenames */
	public function getFiles()
	{
		return array($this->file);
	}
}

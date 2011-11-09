<?php

namespace Orm\Builder;

use Nette\Utils\Strings;
use Nette\Utils\Finder;
use Nette\NotImplementedException;
use Nette\Object;
use SplFileInfo;
use Exception;

class Builder extends Object implements IZipperFiles
{
	/** Namespace ORM */
	const NS = 1;
	/** Non Namespace ORM */
	const NONNS = 2;

	/** Namespace Nette */
	const NS_NETTE = 4;
	/** Prefixed Nette */
	const PREFIXED_NETTE = 8;
	/** Non Namespace Nette */
	const NONNS_NETTE = 16;

	/** @var int */
	private $version;

	/** @var array fromPath => toPath */
	private $files = array();

	/** @var VersionInfo */
	private $info;

	/**
	 * @param int
	 * @param VersionInfo
	 */
	public function __construct($version, VersionInfo $info)
	{
		$this->version = $version;
		$this->info = $info;
	}

	/**
	 * Prekopiruje a udela zmeny dle nastavene verze.
	 * @param string directory or file
	 * @param string directory or file
	 */
	public function build($from, $to)
	{
		$from = realpath($from);
		if (!file_exists($to))
		{
			$tmp = NULL;
			foreach (explode(DIRECTORY_SEPARATOR, str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $to)) as $d)
			{
				if ($tmp === NULL) $tmp = $d;
				else $tmp .= DIRECTORY_SEPARATOR . $d;
				if (!file_exists($tmp))
				{
					if (strpos($d, '.') === false)
					{
						@mkdir($tmp);
					}
					else
					{
						file_put_contents($tmp, '');
						break;
					}
				}
			}
		}
		$to = realpath($to);
		$this->wipe($to);
		if (!$from OR !$to) throw new Exception();
		if (is_dir($from))
		{
			$iterator = Finder::find('*')->from($from);
		}
		else
		{
			$iterator = array(new SplFileInfo($from));
		}
		foreach ($iterator as $file)
		{
			$fromPath = (string) $file;
			$toPath = $this->convertPath($fromPath, $from, $to);
			if ($file->isDir())
			{
				mkdir($toPath);
			}
			else if ($file->isFile())
			{
				$data = file_get_contents($fromPath);
				$data = $this->convert($data);
				file_put_contents($toPath, $data);
				$this->files[$fromPath] = $toPath;
			}
		}
	}

	/** @return array fromPath => toPath */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Smaze vse co obsahuje.
	 * @param string directory or file
	 */
	private function wipe($what)
	{
		$iterator = array();
		if (is_file($what))
		{
			$iterator[] = new SplFileInfo($what);
		}
		else if (is_dir($what))
		{
			$iterator = Finder::find('*')->from($what)->childFirst();
		}
		foreach ($iterator as $file)
		{
			if ($file->isDir())
			{
				rmdir($file);
			}
			else if ($file->isFile())
			{
				unlink($file);
			}
		}
	}

	/**
	 * Prevede cestu z povodniho adresare do noveho
	 * @param string file
	 * @param string directory
	 * @param string directory
	 * @return string
	 */
	private function convertPath($file, $from, $to)
	{
		if ($file !== $from AND !Strings::startsWith($file, $from . DIRECTORY_SEPARATOR)) throw new Exception;
		$relative = preg_replace('#^' . preg_quote($from, '#') . '(' . preg_quote(DIRECTORY_SEPARATOR, '#') . '|$)#', '', $file);
		return rtrim($to . DIRECTORY_SEPARATOR . $relative, DIRECTORY_SEPARATOR);
	}

	/**
	 * Upravi obsah podle verze
	 * @param string
	 * @return string
	 */
	private function convert($data)
	{
		$data = PhpParser::standardizeLineEndings($data);
		$data = PhpParser::buildInfo($data, $this->version, $this->info);

		if ($this->version & self::NONNS) // php52
		{
			$data = PhpParser::replaceGlobalScopeRenames($data);
			$data = PhpParser::versionFix($data, true);
			$inNamespaceOrm = (bool) preg_match('#namespace\s+Orm([a-z0-9_\\\\\s]*);#si', $data);
		}
		else if ($this->version & self::NS) // php53
		{
			$data = PhpParser::versionFix($data, false);
		}
		else throw new Exception;

		$data = PhpParser::removeNamespace($data, $this->version & self::NONNS, $this->version & self::NONNS_NETTE);
		if ($this->version & self::PREFIXED_NETTE)
		{
			throw new NotImplementedException;
		}
		else if (!($this->version & self::NONNS_NETTE) AND !($this->version & self::NS_NETTE))
		{
			throw new Exception;
		}

		if ($this->version & self::NONNS AND ($inNamespaceOrm OR $this->version & self::NONNS_NETTE)) // php52
		{
			$data = PhpParser::replaceClosures($data);
			$data = PhpParser::replaceLateStaticBinding($data);
			$data = PhpParser::replaceDirConstant($data);
		}
		return $data;
	}

}

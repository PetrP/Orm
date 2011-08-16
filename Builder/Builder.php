<?php

namespace Orm\Builder;

use Nette\Utils\Strings;
use Nette\Utils\Finder;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;
use Nette\Object;
use SplFileInfo;
use Exception;

class Builder extends Object
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

	/** @var bool */
	private $isDev;

	/**
	 * @param int
	 * @param bool
	 */
	public function __construct($version, $isDev = false)
	{
		$this->version = $version;
		$this->isDev = $isDev;
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
			}
		}
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
		$data = PhpParser::buildInfo($data, $this->version, $this->isDev);

		if ($this->version & self::NONNS AND $this->version & self::NONNS_NETTE)
		{
			$data = PhpParser::versionFix($data, true);
			$data = PhpParser::replaceGlobalScopeRenames($data);
			$data = PhpParser::removeNamespace($data, true, true);
			$data = PhpParser::replaceClosures($data);
			$data = PhpParser::replaceLateStaticBinding($data);
			$data = PhpParser::replaceDirConstant($data);
		}
		else
		{
			$data = PhpParser::versionFix($data, false);
			if ($this->version & self::NONNS AND $this->version & self::NS_NETTE)
			{
				$data = PhpParser::replaceGlobalScopeRenames($data);
				$data = PhpParser::removeNamespace($data, true, false);
			}
			else if ($this->version & self::NONNS AND $this->version & self::PREFIXED_NETTE)
			{
				throw new NotImplementedException();
			}
			else if ($this->version & self::NS AND $this->version & self::NONNS_NETTE)
			{
				$data = PhpParser::removeNamespace($data, false, true);
			}
			else if ($this->version & self::NS AND $this->version & self::NS_NETTE)
			{

			}
			else if ($this->version & self::NS AND $this->version & self::PREFIXED_NETTE)
			{
				throw new NotImplementedException();
			}
			else
			{
				throw new InvalidArgumentException;
			}
		}
		return $data;
	}

}

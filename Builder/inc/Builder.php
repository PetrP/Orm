<?php

namespace Orm\Builder;

use Nette\NotImplementedException;
use Nette\Object;
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
		Helpers::wipeStructure($to);
		$_this = $this;
		$files = & $this->files;
		Helpers::copyStructure($from, $to, function ($data, $fromPath, $toPath) use ($_this, & $files) {
			$files[$fromPath] = $toPath;
			return $_this->_convert($data);
		});
	}

	/** @return array fromPath => toPath */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Upravi obsah podle verze
	 * @param string
	 * @return string
	 * @access private
	 */
	public function _convert($data)
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

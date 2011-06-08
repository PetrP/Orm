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

	/** @param int */
	public function __construct($version)
	{
		$this->version = $version;
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
		if ($this->version & self::NONNS AND $this->version & self::NONNS_NETTE)
		{
			$data = preg_replace('#namespace\s+Orm\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
			$inNamespace = (bool) preg_match('#namespace\s+([a-z0-9_\\\\\s]+);#si', $data);
			$data = preg_replace_callback('#(use\s+)([a-z0-9_\\\\\s]+)(;\n\n?)#si', function (array $m) use ($inNamespace) {
				if ($inNamespace AND strpos($m[2], 'Orm') === 0)
				{
					return $m[1] . substr($m[2], strrpos($m[2], '\\')+1) . $m[3];
				}
			}, $data);
			$data = preg_replace('#\\\\?Orm\\\\\\\\?([a-z0-9_])#si', '$1', $data);
			$data = preg_replace('#\\\\?Nette\\\\\\\\?([a-z0-9_]+[^\\\\a-z0-9_])#si', '$1', $data);
			$data = preg_replace('#\\\\?Nette\\\\\\\\?[a-z0-9_]+\\\\\\\\?([a-z0-9_]+)#si', '$1', $data);
			$data = PhpParser::replaceClosures($data);
			$data = PhpParser::replaceDirConstant($data);

		}
		else if ($this->version & self::NONNS AND $this->version & self::NS_NETTE)
		{
			$data = preg_replace('#namespace\s+Orm\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
			$inNamespace = (bool) preg_match('#namespace\s+([a-z0-9_\\\\\s]+);#si', $data);
			$data = preg_replace_callback('#(use\s+)([a-z0-9_\\\\\s]+)(;\n\n?)#si', function (array $m) use ($inNamespace) {
				if (strpos($m[2], 'Nette') === 0)
				{
					return $m[0];
				}
				if ($inNamespace AND strpos($m[2], 'Orm') === 0)
				{
					return $m[1] . substr($m[2], strrpos($m[2], '\\')+1) . $m[3];
				}
			}, $data);
			$data = preg_replace('#\\\\?Orm\\\\\\\\?([a-z0-9_])#si', '$1', $data);
		}
		else if ($this->version & self::NONNS AND $this->version & self::PREFIXED_NETTE)
		{
			throw new NotImplementedException();
		}
		else if ($this->version & self::NS AND $this->version & self::NONNS_NETTE)
		{
			$inNamespace = (bool) preg_match('#namespace\s+([a-z0-9_\\\\\s]+);#si', $data);
			$data = preg_replace_callback('#(use\s+)([a-z0-9_\\\\\s]+)(;\n\n?)#si', function (array $m) use ($inNamespace) {
				if ($inNamespace AND strpos($m[2], 'Nette') === 0)
				{
					return $m[1] . substr($m[2], strrpos($m[2], '\\')+1) . $m[3];
				}
				if (!$inNamespace AND strpos($m[2], 'Orm') === 0)
				{
					return $m[0];
				}
				if ($inNamespace)
				{
					return $m[0];
				}
				return NULL;
			}, $data);
			$data = preg_replace('#\\\\?Nette\\\\\\\\?([a-z0-9_]+[^\\\\a-z0-9_])#si', '$1', $data);
			$data = preg_replace('#\\\\?Nette\\\\\\\\?[a-z0-9_]+\\\\\\\\?([a-z0-9_]+)#si', '$1', $data);
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
		return $data;
	}

}

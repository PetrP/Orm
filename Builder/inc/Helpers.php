<?php

namespace Orm\Builder;

use Nette\Utils\Strings;
use Nette\Utils\Finder;
use Nette\Object;
use SplFileInfo;
use Exception;

class Helpers extends Object
{

	/**
	 * Prekopiruje a udela zmeny dle nastavene verze.
	 * @param string directory or file
	 * @param string directory or file
	 * @param callback|NULL ($data, $fromPath, $toPath) and return $data
	 */
	public static function copyStructure($from, $to, $eachCallback = NULL)
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
			$toPath = self::convertPath($fromPath, $from, $to);
			if ($file->isDir())
			{
				mkdir($toPath);
			}
			else if ($file->isFile())
			{
				$data = file_get_contents($fromPath);
				if ($eachCallback)
				{
					$data = $eachCallback($data, $fromPath, $toPath);
				}
				file_put_contents($toPath, $data);
			}
		}
	}

	/**
	 * Smaze vse co obsahuje.
	 * @param string directory or file
	 */
	public static function wipeStructure($what)
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
	private static function convertPath($file, $from, $to)
	{
		if ($file !== $from AND !Strings::startsWith($file, $from . DIRECTORY_SEPARATOR)) throw new Exception;
		$relative = preg_replace('#^' . preg_quote($from, '#') . '(' . preg_quote(DIRECTORY_SEPARATOR, '#') . '|$)#', '', $file);
		return rtrim($to . DIRECTORY_SEPARATOR . $relative, DIRECTORY_SEPARATOR);
	}

}

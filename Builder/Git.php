<?php

namespace Orm\Builder;

use Nette\Object;
use Exception;
use Nette\InvalidStateException;

class Git extends Object
{
	public static $gitExe = NULL;
	private static $escGitExe = NULL;

	private $repo;

	public function getPath()
	{
		return $this->repo;
	}

	public function __construct($repo)
	{
		if (!is_dir("$repo/.git")) throw new Exception;
		$this->repo = $repo;
	}

	public function command($cmd, array $env = NULL, $throwError = true)
	{
		if (!self::$escGitExe)
		{
			if (self::$gitExe)
			{
				$gitExe = self::$gitExe;
			}
			else
			{
				$gitExe = 'git';
				foreach (array(
					'C:/Program Files/Git/bin/git.exe',
					'C:/Program Files (x86)/Git/bin/git.exe',
					'C:/Programs/Git/bin/git.exe',
					'C:/Progra~1/Git/bin/git.exe',
					'C:/Progra~2/Git/bin/git.exe',
					'C:/Progra~3/Git/bin/git.exe',
					'C:/Progra~4/Git/bin/git.exe',
					'C:/Git/bin/git.exe',
					'/usr/local/git/bin/git',
				) as $file)
				{
					if (file_exists($file))
					{
						$gitExe = $file;
						break;
					}
				}
			}
			self::$escGitExe = $this->escape($gitExe);
		}

		$proc = proc_open($command = (strpos($cmd, '~GIT~') === false ? (strpos($cmd,'"')?'"':'').self::$escGitExe . ' ' : '') . str_replace('~GIT~', self::$escGitExe, $cmd),
			array(
				array("pipe","r"),
				array("pipe","w"),
				array("pipe","w"),
			),
			$pipes, $this->repo, $env
		);
		$result = stream_get_contents($pipes[1]);
		$error = stream_get_contents($pipes[2]);
		proc_close($proc);
		if ($throwError AND $error) throw new GitException(str_replace('-', '‒', $error), str_replace('-', '‒', $command));
		return $result;
	}

	public function getSha($input)
	{
		return $this->command("log ".$this->escape($input)." -1 --pretty=format:" . $this->escape('%H', true));
	}

	/**
	 * @see escapeshellarg
	 * @param string
	 * @param bool escapeshellarg na windows nahrazuje % mezerou, protoze nelze escapovat, format v gitu ale procento pouziva proto je potreba ho ignorovat
	 * @return string
	 */
	private function escape($arg, $escapePercent = false)
	{
		if ($escapePercent)
		{
			if (strpos($arg, '0/0') !== false) throw new UnexpectedValueException("String contain '0/0'");
			$arg = str_replace('%', '0/0', $arg);
		}
		else
		{
			if (strpos($arg, '%') !== false) throw new UnexpectedValueException("String contain '%'. It`s buggy in windows.");
		}
		$arg = escapeshellarg($arg);
		if ($escapePercent) $arg = str_replace('0/0','%',$arg);
		return $arg;
	}
}

class GitException extends InvalidStateException
{
	public function __construct($message, $command, Exception $previous = NULL)
	{
		$command = self::normalizeCommand($command);
		parent::__construct("$message: `$command`", 0, $previous);
	}

	public static function normalizeCommand($command)
	{
		return preg_replace('#^"*.*git(\.exe)?"*#si', 'git', $command);
	}
}

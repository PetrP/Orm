<?php

namespace Orm\Builder;

use Nette\Object;
use Exception;

/**
 * Information about versions.
 */
class VersionsService extends Object
{

	private $git;

	/**
	 * @param Git
	 * @param bool
	 * @param string|NULL
	 */
	public function __construct(Git $git)
	{
		$this->git = $git;
	}

	public function getHeadShortSha()
	{
		$head = $this->git->getSha('HEAD');
		return substr($head, 0, 7);
	}

	public function getHeadInfo()
	{
		$info = $this->git->command('log HEAD -1 --format="format:%an (%ae)%n%ai (%ar)%n%h %s%n%d" --decorate="short"');
		return $info;
	}

	public function isCleanWorkingTree()
	{
		if ($isCleanWorkingTree = $this->git->command('diff-index HEAD -- Orm/') === '')
		{
			$isCleanWorkingTree = $this->git->command('ls-files --others --exclude-standard -- Orm/') === '';
		}
		return $isCleanWorkingTree;
	}

	public function getMajorVersions()
	{
		$versions = array();
		foreach (array_filter(explode("\n", $this->git->command('show-ref'))) as $t)
		{
			list(, $name) = explode(' ', $t);
			if (strncmp($name, $tmp = 'refs/heads/', strlen($tmp)) === 0 OR strncmp($name, $tmp = 'refs/remotes/', strlen($tmp)) === 0)
			{
				$name = substr($name, strrpos($name, '/')+1);
				if (preg_match('#^v[0-9]+\.[0-9]$#s', $name))
				{
					$versions[$name] = $name;
				}
			}
		}
		natsort($versions);
		return array_reverse($versions, true);
	}

	public function getStableVersions()
	{
		foreach (array_filter(explode("\n", $this->git->command('show-ref --tags --dereference'))) as $t)
		{
			list($hash, $name) = explode(' ', $t);
			$name = substr($name, strrpos($name, '/')+1);
			if (preg_match('#^(v[0-9]+\.[0-9]+)\.([0-9]+)(?:\^\{\})?$#s', $name, $match))
			{
				$name = str_replace('^{}', '', $name);
				$versions[$name] = array($name, $hash);
			}
		}
		uksort($versions, 'strnatcmp');
		return array_reverse($versions, true);
	}

	public function getNextMinorVersions()
	{
		$versions = array_fill_keys($this->getMajorVersions(), -1);
		foreach (array_filter(explode("\n", $this->git->command('show-ref --tags'))) as $t)
		{
			list(, $name) = explode(' ', $t);
			$name = substr($name, strrpos($name, '/')+1);
			if (preg_match('#^(v[0-9]+\.[0-9]+)\.([0-9]+)$#s', $name, $match))
			{
				$versions[$match[1]] = max($match[2], isset($versions[$match[1]]) ? $versions[$match[1]] : -1);
			}
		}
		foreach ($versions as $major => $next)
		{
			$versions[$major] = $major . '.' . ($next+1);
		}
		return $versions;
	}

	public function getCurrentMajor()
	{
		$versions = array();
		foreach (array_filter(explode("\n", $this->git->command('branch --no-color --contains'))) as $t)
		{
			$name = trim($t, ' *');
			if (preg_match('#^v[0-9]+\.[0-9]$#s', $name))
			{
				$versions[$name] = $name;
			}
		}
		natsort($versions);
		$versions = array_reverse($versions);
		if (!$versions)
		{
			foreach ($this->getMajorVersions() as $version)
			{
				if (strpos($this->git->command('branch --no-color --contains ' . $this->git->escape($version)), '* ') !== false)
				{
					$versions[$version] = $version;
					break;
				}
			}
		}
		return $versions ? current($versions) : NULL;
	}

	public function getCurrentTag()
	{
		try {
			$tag = trim($this->git->command('describe --tags --exact-match'));
			if (preg_match('#^v[0-9]+\.[0-9]+\.[0-9]+$#s', $tag))
			{
				return $tag;
			}
		} catch (GitException $e) {}
		return NULL;
	}

	public function getDevelopmentStageVersions()
	{
		return array(
			'dev' => 'dev',
			'alfa' => 'alfa',
			'beta' => 'beta',
			'RC' => 'RC',
		);
	}
}

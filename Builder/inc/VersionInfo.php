<?php

namespace Orm\Builder;

use Nette\Object;
use Exception;

/**
 * Information about current version.
 */
class VersionInfo extends Object
{

	/** @var string v1.2.3 */
	protected $tag;

	/** @var string 1.2.3 */
	protected $version;

	/** @var string 10203 */
	protected $versionId;

	/** @var bool */
	protected $stable;

	/** @var string 2011-11-11 */
	protected $date;

	/** @var string 0000000000000000000000000000000000000000 */
	protected $sha;

	/** @var string 00000000 */
	protected $shortSha;

	/**
	 * @param Git
	 * @param bool
	 * @param string
	 */
	public function __construct(Git $git, $isDev, $customTag)
	{
		list($head, $commitDate) = $this->getHead($git, $isDev);
		if ($customTag === 'detect')
		{
			if ($isDev) throw new Exception;
			list($tag, $date) = $this->getCurrentTag($git, $head, $commitDate);
		}
		else
		{
			$tag = $customTag;
			$date = $commitDate;
		}
		$this->parseTag($tag);
		$this->date = $date;
		if ($this->isStable() AND $isDev) throw new Exception;
		if ($this->isStable() AND $this->versionId === -1) throw new Exception;
		if (!$isDev AND $this->versionId === -1) throw new Exception;
	}

	/**
	 * @param Git
	 * @param bool
	 * @return array (string 0000000000000000000000000000000000000000, string 2011-11-11)
	 */
	protected function getHead(Git $git, $isDev)
	{
		$this->sha = $head = $git->getSha('HEAD');
		$this->shortSha = substr($this->sha, 0, 7);
		if ($isCleanWorkingTree = $git->command('diff-index HEAD -- Orm/') === '')
		{
			$isCleanWorkingTree = $git->command('ls-files --others --exclude-standard -- Orm/') === '';
		}
		if (!$isCleanWorkingTree)
		{
			$this->shortSha .= ' with uncommitted changes';
			$this->sha .= ' with uncommitted changes';
			if (!$isDev)
			{
				throw new Exception('Working Tree is not clean; Clear it or add dev parametr to url: run.php?dev');
			}
		}
		$commitDate = \Nette\DateTime::from($git->command('show -s HEAD --format="%ci"'))->format('Y-m-d');
		return array($head, $commitDate);
	}

	/**
	 * @param Git
	 * @param string
	 * @param string
	 * @return array (string v1.2.3, string 2011-11-11)
	 */
	protected function getCurrentTag(Git $git, $head, $commitDate)
	{
		$tags = array();
		foreach (array_filter(explode("\n", $git->command('show-ref --tags'))) as $t)
		{
			list($tsha, $tname) = explode(' ', $t);
			$tname = substr($tname, strrpos($tname, '/')+1);
			if (!preg_match('#^v[0-9]+\.[0-9]+\.[0-9]+$#s', $tname)) continue;
			if (trim($git->command("cat-file -t $tsha")) === 'tag')
			{
				$tagContent = $git->command("cat-file tag $tsha");
				if (preg_match('#^object ([0-9a-f]{40})\n#', $tagContent, $match))
				{
					if ($match[1] === $head)
					{
						$tagDate = 'unknown';
						if (preg_match('#\ntagger [^\>]+> ([0-9]+) #', $tagContent, $match))
						{
							$d = \Nette\DateTime::from($match[1]);
							$tagDate = $d->format('Y-m-d');
						}
						$tags[] = array($tname, $tagDate);
					}
				}
			}
			else if ($tsha === $head)
			{
				$tags[] = array($tname, $commitDate);
			}
		}
		if (!$tags)
		{
			throw new Exception('There is no version tag; Add one or add dev parametr to url: run.php?dev');
		}
		if (count($tags) > 1)
		{
			throw new Exception('There is more then one version tag.');
		}
		return current($tags);
	}

	/**
	 * @param string|NULL
	 * @return array (string v1.2.3-RC4, float)
	 */
	protected function parseTag($customTag)
	{
		if (!$customTag) return $this->parseTag('v0.0.0-dev0');
		$stages = array(
			'dev' => 5,
			'alfa' => 7,
			'beta' => 8,
			'RC' => 9,
		);
		if (preg_match('(^v([0-9]+\.[0-9]+)\.([0-9]+)-(' . implode('|', array_map('preg_quote', array_keys($stages))) . ')([0-9]+)$)s', $customTag, $match))
		{
			list(, $major, $minor, $stage, $stageNumber) = $match;
		}
		else if (preg_match('(^v([0-9]+\.[0-9]+)\.([0-9]+)$)s', $customTag, $match))
		{
			$stage = $stageNumber = NULL;
			list(, $major, $minor) = $match;
		}
		else
		{
			throw new Exception('Custom tag is in invalid format (v1.2.3-dev4, v1.2.3-alfa4, , v1.2.3-beta4, v1.2.3-RC4, v1.2.3)');
		}

		$this->stable = $stage === NULL;
		$this->tag = "v{$major}.{$minor}" . ($this->stable ? '' : "-{$stage}{$stageNumber}");
		$this->version = ltrim($this->tag , 'v');
		$this->versionId = $this->parseId("{$major}.{$minor}");
		if (!$this->stable)
		{
			if ($stageNumber == 0)
			{
				$this->versionId = -1;
			}
			if ($this->versionId !== -1)
			{
				$this->versionId += ('0.' . str_repeat($stages[$stage], $stageNumber)) - 1;
			}
		}
	}

	/**
	 * @param string 1.2.3
	 * @param int 102030
	 */
	private function parseId($version)
	{
		if ($version === '0.0.0') return -1;
		$tmp = explode('.', $version);
		return $tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2];
	}

	/** @return string v1.2.3 */
	public function getTag()
	{
		return $this->tag;
	}

	/** @return string 1.2.3 */
	public function getVersion()
	{
		return $this->version;
	}

	/** @return string 10203 */
	public function getVersionId()
	{
		return $this->versionId;
	}

	/** @return string 2011-11-11 */
	public function getDate()
	{
		return $this->date;
	}

	/** @return string 0000000000000000000000000000000000000000 */
	public function getSha()
	{
		return $this->sha;
	}

	/** @var string 00000000 */
	public function getShortSha()
	{
		return $this->shortSha;
	}

	/** @var bool false for RC|beta|alfa|dev */
	public function isStable()
	{
		return $this->stable;
	}

}

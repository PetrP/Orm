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

	/** @var string 2011-11-11 */
	protected $date;

	/** @var string 0000000000000000000000000000000000000000 */
	protected $sha;

	/** @var string 00000000 */
	protected $shortSha;

	/**
	 * @param Git
	 * @param bool
	 * @param string|NULL
	 */
	public function __construct(Git $git, $isDev, $customTag = NULL)
	{
		list($head, $commitDate) = $this->getHead($git, $isDev);
		$tags = $this->getCurrentTags($git, $head, $commitDate);
		$tags = $this->handleCustomTag($tags, $isDev, $customTag, $commitDate);
		if (!$tags)
		{
			throw new Exception('There is no version tag; Add one or add dev parametr to url: run.php?dev');
		}
		if (count($tags) > 1)
		{
			throw new Exception('There is more then one version tag.');
		}

		$tag = current($tags);
		$this->tag = $tag[0];
		$this->version = ltrim($this->tag, 'v');
		if (!$this->versionId)
		{
			$this->versionId = $this->parseId($this->version);
		}
		$this->date = $tag[1];
	}

	/**
	 * @param Git
	 * @param bool
	 * @return array (string 0000000000000000000000000000000000000000, string 2011-11-11)
	 */
	private function getHead(Git $git, $isDev)
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
	 * @return array of array (string v1.2.3, string 2011-11-11)
	 */
	private function getCurrentTags(Git $git, $head, $commitDate)
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
		return $tags;
	}

	/**
	 * @param array of array
	 * @param bool
	 * @param string|NULL
	 * @param string
	 * @return array of array (string v1.2.3, string 2011-11-11)
	 */
	private function handleCustomTag($tags, $isDev, $customTag, $commitDate)
	{
		if ($isDev)
		{
			if (!$customTag AND $tags)
			{
				$tags[0][0] .= '-dev';
				$this->versionId = -1;
			}
			else
			{
				list($t, $id) = $this->parseCustomTag($customTag);
				$tags[] = array($t, $commitDate);
				$this->versionId = $id === -1 ? -1 : $this->parseId(ltrim($t, 'v')) + $id;
			}
		}
		return $tags;
	}

	/**
	 * @param string|NULL
	 * @return array (string v1.2.3-RC4, float)
	 */
	private function parseCustomTag($customTag)
	{
		if (!$customTag)
		{
			return array('v0.0.0-dev0', -1);
		}
		if (!strpos($customTag, '-')) throw new Exception('Custom tag is in invalid format (v1.2.3-RC4)');
		list($tag, $tmp) = explode('-', $customTag, 2);
		list(, $type, $number) = \Nette\Utils\Strings::match(strtolower($tmp), '#^([a-z]+)([0-9]+)$#');
		if ($type === NULL) throw new Exception('Custom tag is in invalid format (v1.2.3-RC4)');
		$tag = 'v' . ltrim($tag, 'v');
		$types = array(
			'dev' => array('dev', 5),
			'alfa' => array('alfa', 7),
			'beta' => array('beta', 8),
			'rc' => array('RC', 9),
		);
		if (isset($types[$type]))
		{
			list($type, $n) =  $types[$type];
			return array("$tag-$type$number", ('0.' . str_repeat($n, $number)) - 1);
		}
		return array("$tag-$type$number", -1);
	}

	/**
	 * @param string
	 * @param int
	 */
	protected function parseId($version)
	{
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
}

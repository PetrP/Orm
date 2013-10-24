<?php

namespace Orm\Builder;

use Nette\Object;
use Exception;

class Runner extends Object
{

	const QUICK = 'quick';
	const TEST = 'test';
	const DEVELOPMENT = 'development';
	const STABLE = 'stable';
	const NEW_STABLE = 'newStable';

	/** @var string */
	private $root;

	/** @var Git */
	private $git;

	/** @var VersionInfo */
	private $info;

	public function __construct(Git $git)
	{
		$this->git = $git;
		$this->root = realpath(__DIR__ . '/..');
	}

	/**
	 * Run runner.
	 * @param string self::*
	 * @param string v1.2.3-RC4
	 * @return VersionInfo
	 */
	public function run($mode, $version)
	{
		$this->info = $info = $this->createVersionInfo($mode, $version);
		$this->{'run' . ucfirst($mode)}();
		$this->info = NULL;
		return $info;
	}

	/**
	 * @param string self::*
	 * @param string v1.2.3-RC4
	 * @return VersionInfo
	 */
	protected function createVersionInfo($mode, $version)
	{
		if ($mode === self::QUICK OR $mode === self::TEST)
		{
			$info = new VersionInfo($this->git, true, 'v0.0.0-dev0');
			if ($info->isStable()) throw new \Exception;
		}
		else if ($mode === self::DEVELOPMENT)
		{
			$info = new VersionInfo($this->git, false, $version);
			if ($info->isStable()) throw new \Exception;
		}
		else if ($mode === self::STABLE)
		{
			$info = new VersionInfo($this->git, false, 'detect');
			if ($info->tag !== $version) throw new \Exception;
			if (!$info->isStable()) throw new \Exception;
		}
		else if ($mode === self::NEW_STABLE)
		{
			$info = new VersionInfo($this->git, false, $version);
			if (!$info->isStable()) throw new \Exception;
		}
		else
		{
			throw new \Exception;
		}
		return $info;
	}

	protected function runQuick()
	{
		$b53 = new Builder(Builder::NS | Builder::NS_NETTE, $this->info);
		$b53->build($this->root . '/../Orm', $this->root . '/php53/Orm');

		$b52 = new Builder(Builder::NONNS | Builder::NONNS_NETTE, $this->info);
		$b52->build($this->root . '/../Orm', $this->root . '/php52/Orm');

		return (object) array('b53' => $b53, 'b52' => $b52);
	}

	protected function runDevelopment()
	{
		$b = $this->runQuick();

		$zipDownload = new Zipper($this->root . "/Orm-{$this->info->tag}.zip", $this->root);
		$zipComposer = new Zipper($this->root . "/Orm-{$this->info->tag}-composer.zip", $this->root . '/php53/Orm');

		$zipDownload->add($b->b53);
		$zipComposer->add($b->b53);

		$zipDownload->add($b->b52);

		$api = new Api;
		$api->generate($this->root . '/php52/Orm', $this->root . '/php52/Api');
		$api->generate($this->root . '/php53/Orm', $this->root . '/php53/Api');

		$zipDownload->add($api);

		$zipDownload->add(new Readme($this->root . "/../README.md", $this->root . '/README', $this->info));
		$zipDownload->add(new Readme($this->root . "/../README.md", $this->root . '/php52/Orm/README', $this->info, 'PHP 5.2'));
		$zipDownload->add(new Readme($this->root . "/../README.md", $this->root . '/php52/Api/README', $this->info, 'PHP 5.2'));
		$zipDownload->add(new Readme($this->root . "/../README.md", $this->root . '/php53/Orm/README', $this->info, 'PHP 5.3'));
		$zipDownload->add(new Readme($this->root . "/../README.md", $this->root . '/php53/Api/README', $this->info, 'PHP 5.3'));

		$zipComposer->add(new ComposerJson($this->root . '/php53/Orm/composer.json', $this->info));

		$zipDownload->save();
		$zipComposer->save();

		@mkdir($this->root . '/ftp'); @mkdir($this->root . '/ftp/api'); @mkdir($this->root . '/ftp/download');  @mkdir($this->root . '/ftp/composer');
		mkdir($this->root . "/ftp/api/{$this->info->tag}");
		rename($this->root . "/Orm-{$this->info->tag}.zip", $this->root . "/ftp/download/Orm-{$this->info->tag}.zip");
		rename($this->root . "/Orm-{$this->info->tag}-composer.zip", $this->root . "/ftp/composer/Orm-{$this->info->tag}.zip");
		rename($this->root . '/php52/Api', $this->root . "/ftp/api/{$this->info->tag}/php52");
		rename($this->root . '/php53/Api', $this->root . "/ftp/api/{$this->info->tag}/php53");

		$packagesJson = new PackagesJson();
		$packagesJson->generate($this->root . '/ftp/composer', $this->root . '/ftp/composer/packages.json', 'http://orm.petrprochazka.com/composer/');
	}

	protected function runStable()
	{
		$this->runDevelopment();
	}

	protected function runNewStable()
	{
		$escapedTag = $this->git->escape($this->info->tag);
		$this->git->command("tag -a -m {$escapedTag} {$escapedTag}");

		$this->runStable();
	}

	protected function runTest()
	{
		$this->runQuick();

		foreach (array(
			Builder::NS | Builder::NS_NETTE => 'php53/Nette_with_namespaces',
			Builder::NS | Builder::NONNS_NETTE => 'php53/Nette_without_namespaces',
			//Builder::NS | Builder::PREFIXED_NETTE => 'php53/Nette_prefixed',
			Builder::NONNS | Builder::NONNS_NETTE => 'php52/Nette_without_namespaces',
			Builder::NONNS | Builder::NS_NETTE => 'php52/Nette_with_namespaces',
			//Builder::NS | Builder::PREFIXED_NETTE => 'php52/Nette_prefixed',
		) as $version => $dir)
		{
			$b = new Builder($version, $this->info);
			foreach (array(
				'tests/cases',
				'tests/boot.php',
				'tests/loader.php',
				'tests/libs/HttpPHPUnit',
				'tests/libs/dump.php',
			) as $p)
			{
				$b->build($this->root . "/../$p", $this->root . "/$dir/$p");
			}

			foreach (array(
				'DataSourceX',
				'dibi',
				'Nette',
				'PHPUnit',
			) as $lib)
			{
				if ($lib === 'Nette' AND !($version & Builder::NS_NETTE)) continue;
				Helpers::wipeStructure($this->root . "/$dir/tests/libs/$lib");
				Helpers::copyStructure($this->root . "/../tests/libs/$lib", $this->root . "/$dir/tests/libs/$lib");
			}
			Helpers::copyStructure($this->root . '/data/tests-run.php', $this->root . "/$dir/tests/run.php");
		}
		Helpers::copyStructure($this->root . '/data/tests-run-php52.php', $this->root . "/php52/Nette_without_namespaces/tests/run.php");

		$partialSupport = new PartialSupportTestsConverter($this->root . '/php52/Nette_without_namespaces/tests', $this->root . '/php52/Nette_without_namespaces_partial/tests');
		$partialSupport->convert();
	}

}

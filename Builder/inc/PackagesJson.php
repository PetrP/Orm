<?php

namespace Orm\Builder;

use Exception;
use Nette\Utils\Finder;
use ZipArchive;

class PackagesJson
{
	public function generate($archivesDir, $targetFile, $urlPrefix)
	{
		$packages = array();
		$archives = Finder::findFiles('*.zip')->in($archivesDir);
		foreach ($archives as $archive)
		{
			$zip = new ZipArchive();
			$zip->open($archive);
			$composerJson = $zip->getFromName('composer.json');
			if ($composerJson === FALSE)
			{
				throw new Exception('Missing composer.json in ' . $archive);
			}
			$composerJson = json_decode($composerJson, TRUE);
			if ($composerJson === FALSE)
			{
				throw new Exception('Invalid JSON in composer.json in ' . $archive);
			}

			$composerJson['dist'] = array(
				'type' => 'zip',
				'url' => $urlPrefix . basename($archive),
			);

			$packages[$composerJson['name']][$composerJson['version']] = $composerJson;
		}

		$packagesJson = json_encode(array('packages' => $packages));
		file_put_contents($targetFile, $packagesJson);
	}
}

<?php

namespace Orm\Builder;

use Nette\Object;

class ComposerJson extends Object implements IZipperFiles
{

	/** @var string */
	private $file;

	/**
	 * @param string       path co created composer.json
	 * @param VersionInfo
	 */
	public function __construct($to, VersionInfo $info)
	{
		$version = preg_replace('(-dev[0-9]+$)', '-dev', $info->tag);
		$json = array(
			'name' => 'petrp/orm',
			'description' => "Petr's ORM",
			'version' => $version,
			'homepage' => 'http://orm.petrprochazka.com',
			'time' => $info->date,
			'license' => 'BSD-3-Clause',
			'authors' => array(
				array(
					'name' => 'Petr Procházka',
					'email' => 'petr@petrp.cz',
					'homepage' => 'http://petrp.cz',
				)
			),
			'support' => array(
				'forum' => 'http://orm.petrprochazka.com/forum',
				'issues' => 'https://github.com/PetrP/Orm/issues',
				'source' => 'https://github.com/PetrP/Orm',
			),
			'autoload' => array(
				'files' => array('Orm.php'),
			),
			'require' => array(
				'php' => '>=5.3.0',
				'nette/nette' => '*',
			),
			'conflict' => array(
				'php' => '5.3.3',
			),
			'suggest' => array(
				'dibi/dibi' => 'Required for DibiMapper',
			),
		);

		$flags = PHP_VERSION_ID > 50400 ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : 0;
		$content = json_encode($json, $flags);
		file_put_contents($this->file = $to, $content);
	}

	/** @return array of filenames */
	public function getFiles()
	{
		return array($this->file);
	}
}

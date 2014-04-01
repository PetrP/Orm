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
		// TODO: description, check requirements
		$json = array(
			'name' => 'petrp/orm',
			'version' => $info->getVersion(),
			'homepage' => 'http://orm.petrprochazka.com',
			'license' => 'BSD-3-Clause',
			'authors' => array(
				array(
					'name' => 'Petr Procházka',
					'homepage' => 'http://petrprochazka.com',
				)
			),
			'autoload' => array(
				'files' => array('Orm.php'),
			),
			'require' => array(
				'php' => '~5.3',
				'nette/nette' => '*', // TODO
			),
			'suggest' => array(
				'dibi/dibi' => 'Required for DibiMapper',
			),
		);

		$flags = PHP_VERSION_ID > 50400 ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : 0;
		$content = json_encode($json, $flags);
		file_put_contents($this->file = $to, $content);
		register_shutdown_function(function () use ($to) {
			@unlink($to);
		});
	}

	/** @return array of filenames */
	public function getFiles()
	{
		return array(realpath($this->file));
	}
}

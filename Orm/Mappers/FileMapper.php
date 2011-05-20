<?php

namespace Orm;

use Nette\Utils\SafeStream;

require_once dirname(__FILE__) . '/ArrayMapper.php';

abstract class FileMapper extends ArrayMapper
{
	private static $isStreamRegistered = false;

	public function __construct(IRepository $repository)
	{
		parent::__construct($repository);
		if (!self::$isStreamRegistered)
		{
			SafeStream::register();
			self::$isStreamRegistered = true;
		}
	}


	abstract protected function getFilePath();

	final protected function loadData()
	{
		$path = $this->getFilePath();
		if (!file_exists($path))
		{
			$this->saveData(array());
		}
		return unserialize(file_get_contents('safe://' . $path));
	}
	final protected function saveData(array $data)
	{
		file_put_contents('safe://' . $this->getFilePath(), serialize($data));
	}

}

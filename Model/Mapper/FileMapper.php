<?php

abstract class FileMapper extends ArrayMapper
{
	private static $isStreamRegistered = false;

	public function __construct(Repository $repository)
	{
		parent::__construct($repository);
		if (!self::$isStreamRegistered)
		{
			SafeStream::register();
			self::$isStreamRegistered = true;
		}
	}


	abstract protected function getFilePath();

	protected function loadData()
	{
		$path = $this->getFilePath();
		if (!file_exists($path))
		{
			$this->saveData(array());
		}
		return unserialize(file_get_contents('safe://' . $path));
	}
	protected function saveData(array $data)
	{
		file_put_contents('safe://' . $this->getFilePath(), serialize($data));
	}

}
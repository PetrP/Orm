<?php

use Orm\FileMapper;
use Orm\RepositoryContainer;

class FileMapper_FileMapper extends FileMapper
{
	private $file;

	protected function getFilePath()
	{
		if (!$this->file)
		{
			$this->file = $file = realpath(tempnam(sys_get_temp_dir(), __CLASS__));
			@unlink($file);
			register_shutdown_function(function () use ($file) {
				@unlink($file);
			});
		}
		return $this->file;
	}

	public function _loadData()
	{
		return $this->loadData();
	}

	public function _saveData(array $data)
	{
		return $this->saveData($data);
	}

	public function _getFilePath()
	{
		return $this->getFilePath();
	}
}

abstract class FileMapper_Base_Test extends TestCase
{
	protected $m;
	protected function setUp()
	{
		$this->m = new FileMapper_FileMapper(new TestsRepository(new RepositoryContainer));
	}
}

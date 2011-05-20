<?php

use Orm\FileMapper;
use Orm\RepositoryContainer;

class FileMapper_FileMapper extends FileMapper
{
	protected function getFilePath()
	{
		return TMP_DIR . '/' . __CLASS__ . '.data';
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

class FileMapper_Base_Test extends TestCase
{
	protected $m;
	protected function setUp()
	{
		$this->m = new FileMapper_FileMapper(new TestsRepository(new RepositoryContainer));
		@unlink($this->m->_getFilePath());
	}
}

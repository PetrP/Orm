<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\FileMapper::loadData
 */
class FileMapper_loadData_Test extends FileMapper_Base_Test
{
	public function test()
	{
		$this->assertFileNotExists($this->m->_getFilePath());
		$this->assertSame(array(), $this->m->_loadData());
		$this->assertFileExists($this->m->_getFilePath());
		$this->assertSame('a:0:{}', file_get_contents($this->m->_getFilePath()));
	}

	public function test2()
	{
		file_put_contents($this->m->_getFilePath(), 'a:1:{s:1:"a";s:1:"b";}');
		$this->assertSame(array('a' => 'b'), $this->m->_loadData());
	}
}
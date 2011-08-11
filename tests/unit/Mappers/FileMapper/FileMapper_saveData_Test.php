<?php


/**
 * @covers Orm\FileMapper::saveData
 */
class FileMapper_saveData_Test extends FileMapper_Base_Test
{
	public function test()
	{
		$this->assertFileNotExists($this->m->_getFilePath());
		$this->m->_saveData(array());
		$this->assertFileExists($this->m->_getFilePath());
		$this->assertSame('a:0:{}', file_get_contents($this->m->_getFilePath()));
	}

	public function test2()
	{
		$this->m->_saveData(array('a' => 'b'));
		$this->assertSame('a:1:{s:1:"a";s:1:"b";}', file_get_contents($this->m->_getFilePath()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\FileMapper', 'saveData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

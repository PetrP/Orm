<?php

use Orm\RepositoryContainer;
use Nette\Utils\SafeStream;

/**
 * @covers Orm\FileMapper::__construct
 */
class FileMapper_construct_Test extends TestCase
{
	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->assertAttributeSame(false, 'isStreamRegistered', 'Orm\FileMapper');
		new FileMapper_FileMapper($r);
		$this->assertAttributeSame(true, 'isStreamRegistered', 'Orm\FileMapper');
		new FileMapper_FileMapper($r);
		$this->assertAttributeSame(true, 'isStreamRegistered', 'Orm\FileMapper');
		$this->assertContains(SafeStream::PROTOCOL, stream_get_wrappers());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\FileMapper', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

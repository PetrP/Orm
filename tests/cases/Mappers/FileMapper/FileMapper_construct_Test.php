<?php

use Orm\RepositoryContainer;
use Nette\Utils\SafeStream;

/**
 * @covers Orm\FileMapper::__construct
 */
class FileMapper_construct_Test extends TestCase
{
	protected function tearDown()
	{
		$wrapers = stream_get_wrappers();
		if (in_array(SafeStream::PROTOCOL, $wrapers, true))
		{
			stream_wrapper_unregister(SafeStream::PROTOCOL);
		}
		SafeStream::register();
	}

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$wrapers = stream_get_wrappers();
		if (in_array(SafeStream::PROTOCOL, $wrapers, true))
		{
			stream_wrapper_unregister(SafeStream::PROTOCOL);
		}
		SafeStream::register();

		$this->assertContains(SafeStream::PROTOCOL, stream_get_wrappers());
		$this->assertAttributeSame(false, 'isStreamRegistered', 'Orm\FileMapper');
		new FileMapper_FileMapper($r);
		$this->assertAttributeSame(true, 'isStreamRegistered', 'Orm\FileMapper');
		$this->assertContains(SafeStream::PROTOCOL, stream_get_wrappers());
		new FileMapper_FileMapper($r);
		$this->assertAttributeSame(true, 'isStreamRegistered', 'Orm\FileMapper');
		$this->assertContains(SafeStream::PROTOCOL, stream_get_wrappers());
	}

	public function testConstant()
	{
		$this->assertSame('safe', SafeStream::PROTOCOL);
	}

	public function testDisabled()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$wrapers = stream_get_wrappers();
		if (in_array(SafeStream::PROTOCOL, $wrapers, true))
		{
			stream_wrapper_unregister(SafeStream::PROTOCOL);
		}
		$this->assertNotContains(SafeStream::PROTOCOL, stream_get_wrappers());
		if ($this->readAttribute('Orm\FileMapper', 'isStreamRegistered')) // php52 projde pri samostanem testu
		{
			setAccessible(new ReflectionProperty('Orm\FileMapper', 'isStreamRegistered'))->setValue(false);
		}
		$this->assertAttributeSame(false, 'isStreamRegistered', 'Orm\FileMapper');
		$this->setExpectedException('Orm\NotSupportedException', "Stream 'safe' is not registered; use Nette" . "\\Utils\\SafeStream::register().");
		new FileMapper_FileMapper($r);
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

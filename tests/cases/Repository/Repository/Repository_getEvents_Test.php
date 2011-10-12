<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getEvents
 * @covers Orm\Repository::__construct
 */
class Repository_getEvents_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->assertInstanceOf('Orm\Events', $r->getEvents());
		$this->assertSame($r->getEvents(), $r->getEvents());
	}

	public function testProperty()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->assertAttributeInstanceOf('Orm\Events', 'events', $r);
		$this->assertSame($this->readAttribute($r, 'events'), $r->getEvents());
	}

	public function testEventsRepository()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$e = $r->getEvents();
		$this->assertAttributeSame($r, 'repository', $e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getEvents');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getEntity
 */
class EventArguments_getEntity_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', 'getEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$e = new TestEntity;
		$args = new EventArguments(Events::ATTACH, $this->r, $e);
		$this->assertSame($e, $args->entity);
		$this->assertSame($e, $args->getEntity());
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getType
 */
class EventArguments_getType_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', 'getType');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->assertSame(Events::ATTACH, $args->type);
		$this->assertSame(Events::ATTACH, $args->getType());

		$args = new EventArguments(Events::REMOVE_AFTER, $this->r, new TestEntity);
		$this->assertSame(Events::REMOVE_AFTER, $args->type);
		$this->assertSame(Events::REMOVE_AFTER, $args->getType());
	}

}

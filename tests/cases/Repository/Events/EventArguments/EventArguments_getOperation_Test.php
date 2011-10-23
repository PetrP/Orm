<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getOperation
 */
class EventArguments_getOperation_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', 'getOperation');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$this->assertSame('insert', $args->operation);
		$this->assertSame('insert', $args->getOperation());
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getRepository
 */
class EventArguments_getRepository_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->assertSame($this->r, $args->repository);
		$this->assertSame($this->r, $args->getRepository());
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Orm\EventArguments::$repository.');
		$args->repository = 'foo';
	}
}

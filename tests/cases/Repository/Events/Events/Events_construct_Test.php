<?php

use Orm\Events;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Events::__construct
 */
class Events_construct_Test extends TestCase
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$m = new RepositoryContainer;
		$events = new Events($m->tests);
		$this->assertInstanceOf('Orm\Events', $events);
		$this->assertAttributeSame($m->tests, 'repository', $events);
	}
}

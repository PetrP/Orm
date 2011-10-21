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

	public function testConstancts()
	{
		$this->assertSame(pow(2, 0), Events::LOAD_BEFORE);
		$this->assertSame(pow(2, 1), Events::LOAD_AFTER);
		$this->assertSame(pow(2, 2), Events::ATTACH);
		$this->assertSame(pow(2, 3), Events::PERSIST_BEFORE);
		$this->assertSame(pow(2, 4), Events::PERSIST_BEFORE_INSERT);
		$this->assertSame(pow(2, 5), Events::PERSIST_BEFORE_UPDATE);
		$this->assertSame(pow(2, 6), Events::PERSIST);
		$this->assertSame(pow(2, 7), Events::PERSIST_AFTER_INSERT);
		$this->assertSame(pow(2, 8), Events::PERSIST_AFTER_UPDATE);
		$this->assertSame(pow(2, 9), Events::PERSIST_AFTER);
		$this->assertSame(pow(2, 10), Events::REMOVE_BEFORE);
		$this->assertSame(pow(2, 11), Events::REMOVE_AFTER);
		$this->assertSame(pow(2, 12), Events::FLUSH_BEFORE);
		$this->assertSame(pow(2, 13), Events::FLUSH_AFTER);
		$this->assertSame(pow(2, 14), Events::CLEAN_BEFORE);
		$this->assertSame(pow(2, 15), Events::CLEAN_AFTER);
	}
}

<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::get
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_get_Test extends TestCase
{
	public function test()
	{
		$m = new RepositoryContainer;
		$this->assertSame($m, RepositoryContainer::get(NULL));
		$m = new RepositoryContainer;
		$this->assertSame($m, RepositoryContainer::get(NULL));
		$this->assertSame($m, RepositoryContainer::get(NULL));
	}

	public function testNoOne()
	{
		$p = new ReflectionProperty('Orm\RepositoryContainer', 'instance');
		setAccessible($p);
		$p->setValue(NULL);
		$this->setExpectedException('Exception', 'RepositoryContainer hasn\'t been instanced yet.');
		RepositoryContainer::get();
	}

	public function testDeprecated1()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Orm\DeprecatedException', 'RepositoryContainer::get() is deprecated do not use it.');
		RepositoryContainer::get();
	}

	public function testDeprecated2()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Orm\DeprecatedException', 'RepositoryContainer::get() is deprecated do not use it.');
		RepositoryContainer::get(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

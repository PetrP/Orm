<?php

/**
 * @covers Orm\DibiResultWrapper::__construct
 */
class DibiResultWrapper_construct_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertAttributeSame($this->repository, 'repository', $this->w);
		$this->assertAttributeSame($this->dibiResult, 'dibiResult', $this->w);
	}

	public function test2()
	{
		$this->assertInstanceOf('IteratorAggregate', $this->w);
		$this->assertInstanceOf('Countable', $this->w);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

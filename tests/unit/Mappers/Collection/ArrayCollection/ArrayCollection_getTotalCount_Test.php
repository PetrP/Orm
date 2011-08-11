<?php

/**
 * @covers Orm\ArrayCollection::getTotalCount
 */
class ArrayCollection_getTotalCount_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertSame(4, $this->c->getTotalCount());
	}

	public function test2()
	{
		$this->assertSame(4, $this->c->applyLimit(1)->getTotalCount());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'getTotalCount');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

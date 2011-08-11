<?php

/**
 * @covers Orm\ArrayCollection::getIterator
 */
class ArrayCollection_getIterator_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertInstanceOf('ArrayIterator', $this->c->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->e, iterator_to_array($this->c->getIterator()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'getIterator');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

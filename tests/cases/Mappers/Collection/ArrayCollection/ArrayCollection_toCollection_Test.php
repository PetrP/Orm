<?php

/**
 * @covers Orm\ArrayCollection::toCollection
 */
class ArrayCollection_toCollection_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->toCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $c);
		$this->assertSame('Orm\ArrayCollection', get_class($c));
		$this->assertNotSame($this->c, $c);
		$this->assertAttributeSame($this->c->getResult(), 'source', $c);
		$this->assertAttributeSame(NULL, 'result', $c);
	}

	public function testSubClass()
	{
		$cOrigin = new ArrayCollection_ArrayCollection($this->e);
		$c = $cOrigin->toCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $c);
		$this->assertSame('ArrayCollection_ArrayCollection', get_class($c));
		$this->assertNotSame($cOrigin, $c);
		$this->assertSame($cOrigin->getResult(), $this->readAttribute($c, 'source'));
		$this->assertSame(NULL, $this->readAttribute($c, 'result'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'toCollection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

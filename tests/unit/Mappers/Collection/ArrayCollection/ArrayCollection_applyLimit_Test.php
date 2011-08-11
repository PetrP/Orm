<?php

/**
 * @covers Orm\ArrayCollection::applyLimit
 */
class ArrayCollection_applyLimit_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(10, 'limit', $this->c);
		$this->assertAttributeSame(20, 'offset', $this->c);
	}

	public function testWipe()
	{
		ArrayCollection_ArrayCollection::set($this->c, 'result', array());
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(NULL, 'result', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->applyLimit(10, 20));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'applyLimit');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

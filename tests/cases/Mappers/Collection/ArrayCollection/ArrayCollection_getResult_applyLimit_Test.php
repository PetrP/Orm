<?php

/**
 * @covers Orm\ArrayCollection::getResult
 */
class ArrayCollection_getResult_applyLimit_Test extends ArrayCollection_Base_Test
{

	public function testLimit()
	{
		$this->c->applyLimit(2);
		$this->assertSame(array($this->e[0], $this->e[1]), $this->c->getResult());
		$this->c->applyLimit(1);
		$this->assertSame(array($this->e[0]), $this->c->getResult());
		$this->c->applyLimit(0);
		$this->assertSame(array(), $this->c->getResult());
	}

	public function testOffset()
	{
		$this->c->applyLimit(NULL, 2);
		$this->assertSame(array($this->e[2], $this->e[3]), $this->c->getResult());
		$this->c->applyLimit(NULL, 3);
		$this->assertSame(array($this->e[3]), $this->c->getResult());
		$this->c->applyLimit(NULL, 4);
		$this->assertSame(array(), $this->c->getResult());
	}

	public function testLimitOffset()
	{
		$this->c->applyLimit(3, 1);
		$this->assertSame(array($this->e[1], $this->e[2], $this->e[3]), $this->c->getResult());
		$this->c->applyLimit(222, 3);
		$this->assertSame(array($this->e[3]), $this->c->getResult());
		$this->c->applyLimit(1, 222);
		$this->assertSame(array(), $this->c->getResult());
		$this->c->applyLimit(1, 3);
		$this->assertSame(array($this->e[3]), $this->c->getResult());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'getResult');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

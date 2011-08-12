<?php

/**
 * @covers Orm\ArrayCollection::getBy
 */
class ArrayCollection_getBy_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->getBy(array());
		$this->assertSame($this->e[0], $c);
	}

	public function testEmpty()
	{
		$this->c->applyLimit(0);
		$c = $this->c->getBy(array());
		$this->assertSame(NULL, $c);
	}

	public function test2()
	{
		$c = $this->c->getBy(array('string' => 'a', 'int' => 3));
		$this->assertSame($this->e[2], $c);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'getBy');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

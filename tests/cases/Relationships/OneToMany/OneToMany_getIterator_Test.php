<?php

/**
 * @covers Orm\OneToMany::getIterator
 */
class OneToMany_getIterator_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Traversable', $this->o2m->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->o2m->_getCollection()->fetchAll(), iterator_to_array($this->o2m->getIterator()));
	}

	public function test3()
	{
		$this->assertSame(4, iterator_count($this->o2m->getIterator()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'getIterator');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

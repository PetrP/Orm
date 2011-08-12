<?php

/**
 * @covers Orm\ManyToMany::getIterator
 */
class ManyToMany_getIterator_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Traversable', $this->m2m->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->m2m->_getCollection()->fetchAll(), iterator_to_array($this->m2m->getIterator()));
	}

	public function test3()
	{
		$this->assertSame(4, iterator_count($this->m2m->getIterator()));
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

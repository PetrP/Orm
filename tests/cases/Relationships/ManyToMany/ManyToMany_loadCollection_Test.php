<?php

/**
 * @covers Orm\ManyToMany::loadCollection
 */
class ManyToMany_loadCollection_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(array(), $this->r->mapper->findByIdCounter);
		$this->assertInstanceOf('Orm\IEntityCollection', $this->m2m->_getCollection());
		$this->assertSame(1, count($this->r->mapper->findByIdCounter));
		$this->assertSame(array(10 => 10, 11 => 11, 12 => 12, 13 => 13), $this->r->mapper->findByIdCounter[0][0]);
		$this->assertSame($this->m2m->_getCollection(), $this->r->mapper->findByIdCounter[0][1]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'loadCollection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

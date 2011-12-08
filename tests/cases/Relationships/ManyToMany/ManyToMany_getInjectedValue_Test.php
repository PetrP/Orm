<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\ManyToMany::getInjectedValue
 * @covers Orm\ArrayManyToManyMapper::getValue
 */
class ManyToMany_getInjectedValue_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(array(10=>10,11=>11,12=>12,13=>13), $this->m2m->getInjectedValue());
	}

	public function testNotArray()
	{
		$m = new DibiManyToManyMapper(new DibiConnection(array('lazy' => true)));
		$m->parentParam = 'foo';
		$m->childParam = 'bar';
		$m->table = 'foobar';
		$this->e->repository->mapper->mmm = $m;
		$this->assertNull($this->m2m->getInjectedValue());
	}

	public function testNotAttached()
	{
		$this->m2m = new ManyToMany_ManyToMany(new ManyToMany_Entity, $this->meta1, array(10,11,12,13));
		$this->assertSame(NULL, $this->m2m->getInjectedValue());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getInjectedValue');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

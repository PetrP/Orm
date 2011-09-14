<?php

use Orm\ManyToMany;
use Orm\RelationshipLoader;

/**
 * @covers Orm\ManyToMany::getMapper
 * @covers Orm\ArrayManyToManyMapper::setInjectedValue
 * @covers Orm\ArrayManyToManyMapper::attach
 */
class ManyToMany_getMapper_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testCache()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->assertSame($this->m2m->gm(), $this->m2m->gm());
	}

	public function testBad()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->e->repository->mapper->mmm = new Directory;
		$this->setExpectedException('Orm\BadReturnException', "ManyToMany_Mapper::createManyToManyMapper() must return Orm\\IManyToManyMapper, 'Directory' given");
		$this->m2m->gm();
	}

	public function testValue()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, NULL);
		$this->assertSame(array(), $this->m2m->gm()->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array());
		$this->assertSame(array(), $this->m2m->gm()->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, 'a:0:{}');
		$this->assertSame(array(), $this->m2m->gm()->getInjectedValue());
	}

	public function testValue2()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array(10,11));
		$this->assertSame(array(10=>10,11=>11), $this->m2m->gm()->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array(11,11));
		$this->assertSame(array(11=>11), $this->m2m->gm()->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, serialize(array(10)));
		$this->assertSame(array(10=>10), $this->m2m->gm()->getInjectedValue());
	}

	public function testNotHandled()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany(new TestEntity, $this->r, 'param', 'param', true, array(10,11));
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
		$this->assertSame(NULL, $this->m2m->gm()->getInjectedValue());
	}

	public function testNotMappedByParent()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', false);
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\ArrayManyToManyMapper has support only on side where is realtionship mapped.');
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testMappedBoth()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', RelationshipLoader::MAPPED_BOTH);
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\ArrayManyToManyMapper not support relationship to self.');
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testMappedBad()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', 'foo');
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\ManyToMany::mapped must be Orm\\RelationshipLoader::MAPPED_HERE, MAPPED_THERE or MAPPED_BOTH; 'foo' given.");
		$this->m2m->gm();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getMapper');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

class ManyToMany_getMapper_ManyToMany extends ManyToMany
{
	public function gm()
	{
		return $this->getMapper();
	}
}

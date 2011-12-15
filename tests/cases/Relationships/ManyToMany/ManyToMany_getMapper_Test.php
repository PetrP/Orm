<?php

use Orm\ManyToMany;
use Orm\RelationshipMetaDataToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ManyToMany::getMapper
 * @covers Orm\RelationshipMetaDataManyToMany::getMapper
 * @covers Orm\ArrayManyToManyMapper::setInjectedValue
 * @covers Orm\ArrayManyToManyMapper::attach
 */
class ManyToMany_getMapper_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2);
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testCache()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2);
		$this->assertSame($this->m2m->gm(), $this->m2m->gm());
	}

	public function testBad()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2);
		$this->e->repository->mapper->mmm = new Directory;
		$this->setExpectedException('Orm\BadReturnException', "ManyToMany_Mapper::createManyToManyMapper() must return Orm\\IManyToManyMapper, 'Directory' given");
		$this->m2m->gm();
	}

	public function testValue()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, NULL);
		$this->assertSame(array(), $this->m2m->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, array());
		$this->assertSame(array(), $this->m2m->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, 'a:0:{}');
		$this->assertSame(array(), $this->m2m->getInjectedValue());
	}

	public function testValue2()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, array(10,11));
		$this->assertSame(array(10=>10,11=>11), $this->m2m->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, array(11,11));
		$this->assertSame(array(11=>11), $this->m2m->getInjectedValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->meta2, serialize(array(10)));
		$this->assertSame(array(10=>10), $this->m2m->getInjectedValue());
	}

	public function testNotHandled()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany(new TestEntity, $this->meta2, array(10,11));
		$this->assertSame(NULL, $this->m2m->gm());
		$this->assertSame(NULL, $this->m2m->getInjectedValue());
	}

	public function testNotMappedByParent()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', 'OneToMany_', 'param', NULL, false));
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testMappedBoth()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, new MockRelationshipMetaDataManyToManyBoth(get_class($this->e), 'param', 'OneToMany_', 'param'));
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m2m->gm());
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

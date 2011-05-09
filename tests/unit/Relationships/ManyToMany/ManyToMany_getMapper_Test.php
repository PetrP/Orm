<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers ManyToMany::getMapper
 * @covers ArrayManyToManyMapper::setValue
 */
class ManyToMany_getMapper_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->assertInstanceOf('ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testCache()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->assertSame($this->m2m->gm(), $this->m2m->gm());
	}

	public function testBad()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true);
		$this->e->generatingRepository->mapper->mmm = new Html;
		$this->setExpectedException('InvalidStateException', "ManyToMany_Mapper::createManyToManyMapper() must return IManyToManyMapper, 'Html' given");
		$this->m2m->gm();
	}

	public function testValue()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, NULL);
		$this->assertSame(array(), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array());
		$this->assertSame(array(), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, 'a:0:{}');
		$this->assertSame(array(), $this->m2m->gm()->getValue());
	}

	public function testValue2()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array(10,11));
		$this->assertSame(array(10=>10,11=>11), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, array(11,11));
		$this->assertSame(array(11=>11), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, 'param', 'param', true, serialize(array(10)));
		$this->assertSame(array(10=>10), $this->m2m->gm()->getValue());
	}

	public function testNotHandled()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany(new TestEntity, $this->r, 'param', 'param', true, array(10,11));
		$this->assertInstanceOf('ArrayManyToManyMapper', $this->m2m->gm());
		$this->assertSame(NULL, $this->m2m->gm()->getValue());
	}

}

class ManyToMany_getMapper_ManyToMany extends ManyToMany
{
	public function gm()
	{
		return $this->getMapper();
	}
}

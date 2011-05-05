<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers ManyToMany::getMapper
 * @covers ArrayManyToManyMapper::setValue
 */
class ManyToMany_getMapper_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r);
		$this->assertInstanceOf('ArrayManyToManyMapper', $this->m2m->gm());
	}

	public function testCache()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r);
		$this->assertSame($this->m2m->gm(), $this->m2m->gm());
	}

	public function testBad()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r);
		$this->m2m->m = new Html;
		$this->setExpectedException('InvalidStateException', "ManyToMany::createMapper() must return IManyToManyMapper, 'Html' given");
		$this->m2m->gm();
	}

	public function testValue()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, NULL);
		$this->assertSame(array(), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, array());
		$this->assertSame(array(), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, 'a:0:{}');
		$this->assertSame(array(), $this->m2m->gm()->getValue());
	}

	public function testValue2()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, array(10,11));
		$this->assertSame(array(10=>10,11=>11), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, array(11,11));
		$this->assertSame(array(11=>11), $this->m2m->gm()->getValue());
		$this->m2m = new ManyToMany_getMapper_ManyToMany($this->e, $this->r, NULL, serialize(array(10)));
		$this->assertSame(array(10=>10), $this->m2m->gm()->getValue());
	}

	public function testNotHandled()
	{
		$this->m2m = new ManyToMany_getMapper_ManyToMany(new TestEntity, $this->r, NULL, array(10,11));
		$this->assertInstanceOf('ArrayManyToManyMapper', $this->m2m->gm());
		$this->assertSame(NULL, $this->m2m->gm()->getValue());
	}

}

class ManyToMany_getMapper_ManyToMany extends ManyToMany
{
	public $m;
	protected function createMapper(IRepository $firstRepository, IRepository $secondRepository)
	{
		if ($this->m) return $this->m;
		return parent::createMapper($firstRepository, $secondRepository);
	}

	public function gm()
	{
		return $this->getMapper();
	}
}

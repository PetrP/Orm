<?php


class ManyToMany_Entity extends Entity
{
}

class ManyToMany_Repository extends Repository
{
	protected $entityClassName = 'ManyToMany_Entity';
}

class ManyToMany_Mapper extends TestsMapper
{

}

abstract class ManyToMany_Test extends TestCase
{
	protected $m2m;
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new Model;
		$r = $m->ManyToMany_;
		$this->e = $e = $r->getById(1);
		$this->m2m = new ManyToMany($e, 'OneToMany_', 'param', array(10,11,12,13));
		$this->r = $m->OneToMany_;
	}

	final protected function t()
	{
		$excepted = func_get_args();
		$actual = array();
		foreach ($this->m2m->get() as $e)
		{
			$actual[] = isset($e->id) ? $e->id : $e;
		}
		$this->assertSame($excepted, $actual);
	}

	final public function testBaseData()
	{
		$this->t(10,11,12,13);
	}

}

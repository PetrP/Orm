<?php

/**
 * @property TestEntity $param {m:1 tests}
 * @property string $string
 */
class OneToMany_Entity extends Entity
{

}
/**
 * @property TestEntity|NULL $param {m:1 tests}
 */
class OneToMany_Entity2 extends OneToMany_Entity
{

}

class OneToMany_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		if ($data === NULL) return array('OneToMany_Entity', 'OneToMany_Entity2');
		return 'OneToMany_Entity';
	}
}

class OneToMany_Mapper extends TestsMapper
{
	public function __construct(IRepository $repository)
	{
		parent::__construct($repository);
		$this->array = array();
		foreach (array_merge(range(20, 29), range(10, 13)) as $id)
		{
			$this->array[$id] = array(
				'id' => $id,
				'param' => substr($id, 0, 1),
			);
		}
	}

}

class OneToMany_2Repository extends OneToMany_Repository
{
	public $count = 0;
	public function findByParam($param)
	{
		$this->count++;
		return $this->mapper->findAll()->findByParam($param);
	}
}

class OneToMany_2Mapper extends OneToMany_Mapper
{
	public $count = 0;
	public function findByParam($param)
	{
		$this->count++;
		return $this->findAll()->findByParam($param);
	}
}
class OneToMany_3Repository extends OneToMany_Repository {}
class OneToMany_3Mapper extends OneToMany_2Mapper {}

abstract class OneToMany_Test extends TestCase
{
	protected $o2m;
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new Model;
		$r = $m->tests;
		$this->e = $e = $r->getById(1);
		$this->o2m = new OneToMany($e, 'OneToMany_', 'param');
		$this->r = $m->OneToMany_;
	}

	final protected function t()
	{
		$excepted = func_get_args();
		$actual = array();
		foreach ($this->o2m->get() as $e)
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

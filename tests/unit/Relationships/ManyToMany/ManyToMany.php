<?php


use Orm\Entity;
use Orm\Repository;
use Orm\ManyToMany;
use Orm\IRepository;
use Orm\RepositoryContainer;
use Orm\IEntity;

class ManyToMany_Entity extends Entity
{
}

class ManyToMany_Repository extends Repository
{
	protected $entityClassName = 'ManyToMany_Entity';
}

class ManyToMany_Mapper extends TestsMapper
{
	public $mmm;
	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		if ($this->mmm) return $this->mmm;
		return parent::createManyToManyMapper($firstParam, $repository, $secondParam);
	}
}

abstract class ManyToMany_Test extends TestCase
{
	protected $m2m;
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$r = $m->ManyToMany_;
		$this->e = $e = $r->getById(1);
		$this->m2m = new ManyToMany($e, 'OneToMany_', 'param', 'param', true, array(10,11,12,13));
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

class IgnoreManyToMany extends ManyToMany
{
	public $ignore;
	protected function ignore(IEntity $entity)
	{
		if ($this->ignore)
		{
			return true;
		}
		return parent::ignore($entity);
	}
}

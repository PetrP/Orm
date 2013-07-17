<?php


use Orm\Entity;
use Orm\Repository;
use Orm\ManyToMany;
use Orm\IRepository;
use Orm\RepositoryContainer;
use Orm\IEntity;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @property $foo
 * @property ManyToMany_ManyToMany $many {m:m OneToMany_}
 */
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

class ManyToMany_ManyToMany extends ManyToMany
{
	public function _getCollection()
	{
		return $this->getCollection();
	}

	public function __createEntity($entity, $invasive = true)
	{
		if (func_num_args() > 1) return parent::createEntity($entity, $invasive);
		return parent::createEntity($entity);
	}

	public function _getParent()
	{
		return $this->getParent();
	}

	public function _getMetaData()
	{
		return $this->getMetaData();
	}

	public $loadCollection;

	protected function loadCollection(IRepository $repository, array $ids)
	{
		if ($this->loadCollection === NULL)
		{
			return parent::loadCollection($repository, $ids);
		}
		return $this->loadCollection;
	}
}

abstract class ManyToMany_Test extends TestCase
{
	/** @var ManyToMany_ManyToMany */
	protected $m2m;
	protected $meta1;
	protected $meta2;
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$r = $m->ManyToMany_;
		$this->e = $e = $r->getById(1);
		$this->meta1 = new RelationshipMetaDataManyToMany(get_class($e), 'id', 'OneToMany_', '', NULL, true);
		$this->m2m = new ManyToMany_ManyToMany($e, $this->meta1, array(10,11,12,13));
		$this->meta2 = new RelationshipMetaDataManyToMany(get_class($this->e), 'param', 'OneToMany_', '', NULL, true);
		$this->r = $m->OneToMany_;
	}

	final protected function t()
	{
		$expected = func_get_args();
		$actual = array();
		foreach ($this->m2m->_getCollection() as $e)
		{
			$actual[] = isset($e->id) ? $e->id : $e;
		}
		$this->assertSame($expected, $actual);
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
		if ($this->ignore !== NULL)
		{
			return $this->ignore;
		}
		return parent::ignore($entity);
	}

	public $check;
	protected function check(IEntity $entity)
	{
		if ($this->check !== NULL)
		{
			return $this->check;
		}
		return parent::check($entity);
	}
}

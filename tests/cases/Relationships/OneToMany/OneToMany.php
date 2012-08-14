<?php

use Orm\Entity;
use Orm\Repository;
use Orm\OneToMany;
use Orm\IRepository;
use Orm\RepositoryContainer;
use Orm\IEntity;
use Orm\RelationshipMetaDataOneToMany;

/**
 * @property OneToMany_OneToMany $many {1:m OneToMany_Repository param}
 */
class OneToManyX_Entity extends TestEntity
{

}

class OneToManyX_Repository extends Repository
{
	protected $entityClassName = 'OneToManyX_Entity';
}

class OneToManyX_Mapper extends TestsMapper
{

}

/**
 * @property OneToManyX_Entity $param {m:1 OneToManyX_Repository}
 * @property string $string {default ''}
 */
class OneToMany_Entity extends Entity
{

}
/**
 * @property OneToManyX_Entity|NULL $param {m:1 OneToManyX_Repository}
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

	public $findByIdCounter = array();

	public function findById($ids)
	{
		$result = parent::findAll()->findById($ids);
		$this->findByIdCounter[] = array($ids, $result);
		return $result;
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


class OneToMany_OneToMany extends OneToMany
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

	protected function loadCollection(IRepository $repository, IEntity $parent, $param)
	{
		if ($this->loadCollection === NULL)
		{
			return parent::loadCollection($repository, $parent, $param);
		}
		return $this->loadCollection;
	}
}

abstract class OneToMany_Test extends TestCase
{
	/** @var OneToMany_OneToMany */
	protected $o2m;
	protected $meta1;
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$r = $m->OneToManyX_Repository;
		$this->e = $e = $r->getById(1);
		$this->o2m = $e->many;
		$this->meta1 = new RelationshipMetaDataOneToMany(get_class($e), 'many', 'OneToMany_', 'param');
		$this->r = $m->OneToMany_;
	}

	final protected function t()
	{
		$expected = func_get_args();
		$actual = array();
		foreach ($this->o2m->_getCollection() as $e)
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

class IgnoreOneToMany extends OneToMany
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

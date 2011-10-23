<?php

use Orm\DibiPersistenceHelper;
use Orm\Entity;
use Orm\SqlConventional;
use Orm\RepositoryContainer;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\ArrayManyToManyMapper;
use Orm\DibiManyToManyMapper;

class DibiPersistenceHelper_DibiPersistenceHelper extends DibiPersistenceHelper
{
	public function call($method, array $args = array())
	{
		return call_user_func_array(array($this, $method), $args);
	}
}

/**
 * @property mixed $miXed
 * @property mixed $miXed2
 * @property mixed $miXed3
 */
class DibiPersistenceHelper_Entity extends Entity
{
	public function getMiXed4()
	{
		return 4;
	}
}

class DibiPersistenceHelper_Repository extends Repository
{
	protected $entityClassName = 'DibiPersistenceHelper_Entity';
}
class DibiPersistenceHelper_Mapper extends DibiMapper_Connected_DibiMapper
{
	public function createManyToManyMapper($param, Orm\IRepository $targetRepository, $targetParam)
	{
		if ($param === 'array')
		{
			$mapper = new ArrayManyToManyMapper;
		}
		else if ($param === 'dibi')
		{
			$mapper = new DibiManyToManyMapper(new DibiConnection(array('lazy' => true)));
			$mapper->parentParam = 'aaa';
			$mapper->childParam = 'bbb';
			$mapper->table = 'ccc';
		}
		else
		{
			$mapper = parent::createManyToManyMapper($param, $targetRepository, $targetParam);
		}
		return $mapper;
	}
}

abstract class DibiPersistenceHelper_Test extends TestCase
{

	/** @var DibiPersistenceHelper_Entity */
	protected $e;
	/** @var DibiPersistenceHelper_Entity */
	protected $ee;
	protected $r;
	/** @var DibiPersistenceHelper_DibiPersistenceHelper */
	protected $h;
	protected $model;

	/** @var DibiMockExpectedMySqlDriver */
	protected $d;

	protected function setUp()
	{
		$this->model = new RepositoryContainer;
		$this->r = $this->model->getRepository('DibiPersistenceHelper_Repository');
		$m = $this->r->getMapper();
		$this->d = $m->getConnection()->getDriver();
		$this->h = new DibiPersistenceHelper_DibiPersistenceHelper($m->getConnection(), $m->conventional, 'table', $this->r->events);
		$this->e = new DibiPersistenceHelper_Entity;
		$this->ee = $this->r->attach(new DibiPersistenceHelper_Entity);
		$this->e->miXed = 1;
		$this->e->miXed2 = 2;
		$this->e->miXed3 = 3;
	}

	protected function tearDown()
	{
		$this->d->disconnect();
	}

}

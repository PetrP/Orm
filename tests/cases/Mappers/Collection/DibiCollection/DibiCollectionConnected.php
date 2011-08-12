<?php

use Orm\Repository;
use Orm\DibiMapper;
use Orm\DibiCollection;
use Orm\RepositoryContainer;

class DibiCollectionConnectedRepository extends DibiCollectionRepository
{
}

class DibiCollectionConnectedMapper extends DibiCollectionMapper
{
	protected function createConnection()
	{
		return new DibiConnection(array(
			'driver' => 'MockExpectedMySql',
		));
	}
}

abstract class DibiCollection_BaseConnected_Test extends DibiCollection_Base_Test
{
	/** @var DibiMockExpectedMySqlDriver */
	protected $d;
	protected function setUp()
	{
		$this->model = new RepositoryContainer;
		$this->r = new DibiCollectionConnectedRepository($this->model);
		$this->m = new DibiCollectionConnectedMapper($this->r);
		$this->d = $this->m->getConnection()->getDriver();
		$this->c = $this->m->findAll();

	}

	protected function tearDown()
	{
		$this->d->disconnect();
	}

	protected function a($expectedSql, DibiCollection $c = NULL)
	{
		if ($c === NULL) $c = $this->c;
		$csql = $c->__toString();
		$trimcsql = trim(preg_replace('#\s+#', ' ', $csql));
		$trimsql = trim(preg_replace('#\s+#', ' ', $expectedSql));
		$this->assertSame($trimsql, $trimcsql, "\n$trimsql\n$trimcsql\n");
	}

	protected function e($c, $end = true, $sql = 'SELECT `e`.* FROM `dibicollectionconnected` as e')
	{
		$this->d->addExpected('query', true, $sql);
		$this->d->addExpected('createResultDriver', NULL, true);
		foreach ($c ? range(1, $c) : array() as $id)
		{
			if ($id === 3) $string = 'bar';
			else if ($id & 1) $string = 'boo';
			else if ($id & 2) $string = 'foo';
			$this->d->addExpected('fetch', array('id' => $id, 'string' => $string), true);
		}
		if ($end) $this->d->addExpected('fetch', false, true);
	}


}

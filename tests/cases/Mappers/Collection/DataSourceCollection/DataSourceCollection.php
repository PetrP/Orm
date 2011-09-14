<?php

use Orm\Repository;
use Orm\DibiMapper;
use Orm\DataSourceCollection;
use Orm\RepositoryContainer;

class DataSourceCollectionRepository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

class DataSourceCollectionMapper extends DibiMapper
{
	static protected $dibiConnection;
	protected function createConnection()
	{
		if (!isset(self::$dibiConnection))
		{
			self::$dibiConnection = new DibiConnection(array(
				'driver' => 'MockEscapeMySql',
			));
		}
		return self::$dibiConnection;
	}

	protected function createCollectionClass()
	{
		return 'Orm\DataSourceCollection';
	}
}

class DataSourceCollection_DataSourceCollection extends DataSourceCollection
{
	public static function set(DataSourceCollection $c, $property, $value)
	{
		$p = new ReflectionProperty($c, $property);
		setAccessible($p);
		$p->setValue($c, $value);
	}

	public static function call(DataSourceCollection $c, $method, array $params = array())
	{
		return call_user_func_array(array($c, $method), $params);
	}
}

abstract class DataSourceCollection_Base_Test extends TestCase
{
	protected $model;
	protected $r;
	protected $m;
	/** @var DataSourceCollection */
	protected $c;

	protected function setUp()
	{
		$this->model = new RepositoryContainer;
		$this->r = new DataSourceCollectionRepository($this->model);
		$this->m = new DataSourceCollectionMapper($this->r);
		$this->c = $this->m->findAll();
	}

	protected function a($expectedSql, DataSourceCollection $c = NULL)
	{
		if ($c === NULL) $c = $this->c;
		$csql = $c->__toString();
		$trimcsql = trim(preg_replace('#\s+#', ' ', $csql));
		$trimsql = trim(preg_replace('#\s+#', ' ', $expectedSql));
		$this->assertSame($trimsql, $trimcsql, "\n$trimsql\n$trimcsql\n");
	}

}

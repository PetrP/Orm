<?php

use Orm\Repository;
use Orm\DibiMapper;
use Orm\DibiCollection;
use Orm\RepositoryContainer;

class DibiCollectionRepository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

class DibiCollectionMapper extends DibiMapper
{
	static protected $dibiConnection;
	protected function createConnection()
	{
		if (!isset(self::$dibiConnection))
		{
			self::$dibiConnection = new DibiConnection(array(
				'driver' => 'sqlite',
				'database' => TMP_DIR . '/sqlite',
				'lazy' => true,
			));
		}
		return self::$dibiConnection;
	}
}

class DibiCollection_DibiCollection extends DibiCollection
{
	public static function set(DibiCollection $c, $property, $value)
	{
		$c->$property = $value;
	}

	public static function call(DibiCollection $c, $method, array $params = array())
	{
		return call_user_func_array(array($c, $method), $params);
	}
}

abstract class DibiCollection_Base_Test extends TestCase
{
	protected $r;
	protected $m;
	/** @var DibiCollection */
	protected $c;

	protected function setUp()
	{
		$this->r = new DibiCollectionRepository(new RepositoryContainer);
		$this->m = new DibiCollectionMapper($this->r);
		$this->c = $this->m->findAll();
	}

	protected function a($expectedSql, DibiCollection $c = NULL)
	{
		if ($c === NULL) $c = $this->c;
		$csql = $c->__toString();
		$trimcsql = trim(preg_replace('#\s+#', ' ', $csql));
		$trimsql = trim(preg_replace('#\s+#', ' ', $expectedSql));
		$this->assertSame($trimsql, $trimcsql, "\n$trimsql\n$trimcsql\n");
	}

}

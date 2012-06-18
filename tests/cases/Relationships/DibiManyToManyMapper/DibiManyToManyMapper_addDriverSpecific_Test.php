<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::addDriverSpecific
 */
class DibiManyToManyMapper_addDriverSpecific_Test extends DibiManyToManyMapper_Connected_Test
{

	protected function setUp()
	{
		parent::setUp();
		if (!function_exists('Access') OR !class_exists('Mocker'))
		{
			$this->markTestIncomplete('Mocker and Access is required.');
		}
	}

	private function create($driverClass)
	{
		$driver = Mocker::mock($driverClass)
			->ignoreMethod('getClass', 'getReflection')
			->ignoreMethod('escape')
			->method('connect')->andReturn(NULL)
			->getMock()
		;

		$dibi = new DibiConnection(array('lazy' => true));
		Access($dibi)->driver = $driver;
		register_shutdown_function(function ($dibi) {
			Access($dibi)->connected = false;
		}, $dibi);

		$mapper = new DibiManyToManyMapper($dibi);
		$mapper->parentParam = 'x';
		$mapper->childParam = 'y';
		$mapper->table = 't';
		$dibi->getDriver();
		return array($mapper, $driver->___mock);
	}

	/**
	 * @dataProvider dataProviderDrivers
	 */
	public function testDrivers($driverClass, $sqls)
	{
		list($mapper, $d) = $this->create($driverClass);
		foreach ((array) $sqls as $sql)
		{
			$d->method('query')->with($sql)->andReturn(NULL);
			$d->method('getAffectedRows')->andReturn(3);
		}
		$this->assertNull($mapper->add($this->e, array(1, 2, 3), NULL));
	}

	public function dataProviderDrivers()
	{
		return array(
			array('DibiMysqlDriver', 'INSERT IGNORE INTO `t` (`x`, `y`) VALUES (1, 1) , (1, 2) , (1, 3)'),
			array('DibiMySqliDriver', 'INSERT IGNORE INTO `t` (`x`, `y`) VALUES (1, 1) , (1, 2) , (1, 3)'),
			array('DibiSqlite3Driver', array(
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('DibiSqliteDriver', array(
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('DibiPostgreDriver', array(
				'INSERT INTO "t" ("x", "y") VALUES (1, 1)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 2)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 3)',
			)),
			array('DibiOracleDriver', array(
				'INSERT INTO "t" ("x", "y") VALUES (1, 1)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 2)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 3)',
			)),
			array('DibiFirebirdDriver', array(
				'INSERT INTO t (x, y) VALUES (1, 1)',
				'INSERT INTO t (x, y) VALUES (1, 2)',
				'INSERT INTO t (x, y) VALUES (1, 3)',
			)),
			array('DibiOdbcDriver', array(
				'INSERT INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('DibiMsSqlDriver', array(
				'INSERT INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('DibiMsSql2005Driver', array(
				'INSERT INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
		);
	}

	private function createPdo($driverName)
	{
		$pdo = Mocker::mock('PDO')
			->ignoreMethod('__wakeup')
			->ignoreMethod('__sleep')
			->method('getAttribute')->twice()->with(PDO::ATTR_DRIVER_NAME)->andReturn($driverName)
			->getMock()
		;

		$driver = Mocker::mock('DibiPdoDriver')
			->ignoreMethod('getClass', 'getReflection')
			->ignoreMethod('escape')
			->ignoreMethod('connect')
			->method('getResource')->any()->andReturn($pdo)
			->getMock()
		;

		$dibi = new DibiConnection(array('resource' => $pdo, 'lazy' => true));
		Access($dibi)->driver = $driver;
		register_shutdown_function(function ($dibi) {
			Access($dibi)->connected = false;
		}, $dibi);

		$mapper = new DibiManyToManyMapper($dibi);
		$mapper->parentParam = 'x';
		$mapper->childParam = 'y';
		$mapper->table = 't';
		$dibi->getDriver();
		return array($mapper, $driver->___mock);
	}

	/**
	 * @dataProvider dataProviderDriversPdo
	 */
	public function testDriversPdo($driverName, $sqls)
	{
		list($mapper, $d) = $this->createPdo($driverName);
		foreach ((array) $sqls as $sql)
		{
			$d->method('query')->with($sql)->andReturn(NULL);
			$d->method('getAffectedRows')->andReturn(3);
		}
		$this->assertNull($mapper->add($this->e, array(1, 2, 3), NULL));
	}

	public function dataProviderDriversPdo()
	{
		return array(
			array('mysql', 'INSERT IGNORE INTO `t` (`x`, `y`) VALUES (1, 1) , (1, 2) , (1, 3)'),
			array('sqlite', array(
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('sqlite2', array(
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT OR IGNORE INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('pgsql', array(
				'INSERT INTO "t" ("x", "y") VALUES (1, 1)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 2)',
				'INSERT INTO "t" ("x", "y") VALUES (1, 3)',
			)),
			array('oci', array(
				'INSERT INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('firebird', array(
				'INSERT INTO t (x, y) VALUES (1, 1)',
				'INSERT INTO t (x, y) VALUES (1, 2)',
				'INSERT INTO t (x, y) VALUES (1, 3)',
			)),
			array('odbc', array(
				'INSERT INTO [t] ([x], [y]) VALUES (1, 1)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 2)',
				'INSERT INTO [t] ([x], [y]) VALUES (1, 3)',
			)),
			array('dblib', array(
				'INSERT INTO t (x, y) VALUES (1, 1)',
				'INSERT INTO t (x, y) VALUES (1, 2)',
				'INSERT INTO t (x, y) VALUES (1, 3)',
			)),
			array('sqlsrv', array(
				'INSERT INTO t (x, y) VALUES (1, 1)',
				'INSERT INTO t (x, y) VALUES (1, 2)',
				'INSERT INTO t (x, y) VALUES (1, 3)',
			)),
		);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'addDriverSpecific');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

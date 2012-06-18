<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::detectDatabaseDriverName
 */
class DibiManyToManyMapper_detectDatabaseDriverName_Test extends DibiManyToManyMapper_Connected_Test
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
		return $mapper;
	}

	/**
	 * @dataProvider dataProviderDrivers
	 */
	public function testDrivers($driverClass, $detectedDrivertName)
	{
		$mapper = $this->create($driverClass);
		$this->assertSame($detectedDrivertName, Access($mapper)->detectDatabaseDriverName());
	}

	public function dataProviderDrivers()
	{
		return array(
			array('DibiMysqlDriver', 'mysql'),
			array('DibiMySqliDriver', 'mysql'),
			array('DibiSqlite3Driver', 'sqlite3'),
			array('DibiSqliteDriver', 'sqlite2'),
			array('DibiPostgreDriver', 'postgre'),
			array('DibiOracleDriver', 'oracle'),
			array('DibiFirebirdDriver', 'firebird'),
			array('DibiOdbcDriver', 'odbc'),
			array('DibiMsSqlDriver', 'mssql'),
			array('DibiMsSql2005Driver', 'mssql'),
			array('IDibiDriver', NULL),
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
		return $mapper;
	}

	/**
	 * @dataProvider dataProviderDriversPdo
	 */
	public function testDriversPdo($driverName, $detectedDrivertName)
	{
		$mapper = $this->createPdo($driverName);
		$this->assertSame($detectedDrivertName, Access($mapper)->detectDatabaseDriverName());
	}

	public function dataProviderDriversPdo()
	{
		return array(
			array('mysql', 'mysql'),
			array('sqlite', 'sqlite3'),
			array('sqlite2', 'sqlite2'),
			array('pgsql', 'postgre'),
			array('oci', 'oracle'),
			array('firebird', 'firebird'),
			array('odbc', 'odbc'),
			array('dblib', 'mssql'),
			array('sqlsrv', 'mssql'),
		);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'detectDatabaseDriverName');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

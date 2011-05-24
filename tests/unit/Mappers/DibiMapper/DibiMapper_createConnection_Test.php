<?php

use Orm\RepositoryContainer;
use Orm\DibiMapper;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::createConnection
 */
class DibiMapper_createConnection_Test extends TestCase
{
	public function testNoConnection()
	{
		$m = new DibiMapper(new TestsRepository(new RepositoryContainer));
		$this->setExpectedException('DibiException', 'Dibi is not connected to database.');
		$m->getConnection();
	}

	public function test()
	{
		if (PHP_VERSION_ID < 50300)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
		}
		$m = new DibiMapper(new TestsRepository(new RepositoryContainer));

		Dibi::setConnection($c = new DibiConnection(array('lazy' => true)));
		$this->assertSame($c, $m->getConnection());

		$r = new ReflectionProperty('Dibi', 'connection');
		$r->setAccessible(true);
		$r->setValue(NULL);
	}
}

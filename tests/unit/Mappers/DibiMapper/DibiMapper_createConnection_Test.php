<?php

use Orm\RepositoryContainer;
use Orm\DibiMapper;

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
		$r = new ReflectionProperty('Dibi', 'connection');
		setAccessible($r);

		$m = new DibiMapper(new TestsRepository(new RepositoryContainer));

		Dibi::setConnection($c = new DibiConnection(array('lazy' => true)));
		$this->assertSame($c, $m->getConnection());

		$r->setValue(NULL);
	}
}

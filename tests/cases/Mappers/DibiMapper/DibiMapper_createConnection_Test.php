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

	public function testContext()
	{
		$c = new DibiConnection(array('lazy' => true));
		$m = new DibiMapper(new TestsRepository($r = new RepositoryContainer));
		$r->getContext()->removeService('dibi')->addService('dibi', $c);

		$this->assertSame($c, $m->getConnection());
	}

	public function testContextBad()
	{
		$m = new DibiMapper(new TestsRepository($r = new RepositoryContainer));
		$r->getContext()->removeService('dibi')->addService('dibi', $m);

		$this->setExpectedException('Orm\ServiceNotInstanceOfException', "Service 'dibi' is not instance of 'DibiConnection'.");
		$m->getConnection();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'createConnection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

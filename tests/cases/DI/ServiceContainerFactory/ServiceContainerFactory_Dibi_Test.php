<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 * @covers Orm\ServiceContainerFactory::createDibi
 */
class ServiceContainerFactory_Dibi_Test extends TestCase
{

	public function testNoConnection()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$this->setExpectedException('DibiException', 'Dibi is not connected to database.');
		$c->getService('dibi');
	}

	public function testHasConnection()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$r = new ReflectionProperty('Dibi', 'connection');
		setAccessible($r);

		Dibi::setConnection($dc = new DibiConnection(array('lazy' => true)));
		$this->assertInstanceOf('DibiConnection', $c->getService('dibi'));
		$this->assertSame($dc, $c->getService('dibi'));

		$r->setValue(NULL);
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\ServiceContainer;


/**
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_construct_Test extends TestCase
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepositoryContainer', new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testNull()
	{
		$rc = new RepositoryContainer;
		$this->assertInstanceOf('Orm\ServiceContainer', $rc->getContext());
		$this->assertTrue($rc->getContext()->hasService('dibi'));
		$this->assertFalse($rc->getContext()->hasService('bubu'));
	}

	public function testFactory()
	{
		$cf = new RepositoryContainer_construct_ServiceContainerFactory;
		$rc = new RepositoryContainer($cf);
		$this->assertInstanceOf('Orm\ServiceContainer', $rc->getContext());
		$this->assertSame($cf->getContainer(), $rc->getContext());
		$this->assertFalse($rc->getContext()->hasService('dibi'));
		$this->assertTrue($rc->getContext()->hasService('bubu'));
	}

	public function testContainer()
	{
		$c = new ServiceContainer;
		$c->addService('huu', 'xyz');
		$rc = new RepositoryContainer($c);
		$this->assertInstanceOf('Orm\ServiceContainer', $rc->getContext());
		$this->assertSame($c, $rc->getContext());
		$this->assertFalse($rc->getContext()->hasService('dibi'));
		$this->assertTrue($rc->getContext()->hasService('huu'));
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Orm\RepositoryContainer::__construct() first param must be Orm\IServiceContainerFactory, Orm\IServiceContainer or NULL; \'ArrayObject\' given.');
		new RepositoryContainer(new ArrayObject);
	}
}

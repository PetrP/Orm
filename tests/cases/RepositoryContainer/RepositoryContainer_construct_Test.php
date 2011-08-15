<?php

use Orm\RepositoryContainer;


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
}

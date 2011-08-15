<?php

use Orm\ServiceContainerFactory;
use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainerFactory::getContainer
 */
class ServiceContainerFactory_getContainer_Test extends TestCase
{

	public function testDefault()
	{
		$f = new ServiceContainerFactory;
		$this->assertInstanceOf('Orm\IServiceContainer', $f->getContainer());
		$this->assertSame($f->getContainer(), $f->getContainer());
	}

	public function testCustom()
	{
		$c = new ServiceContainer;
		$f = new ServiceContainerFactory($c);
		$this->assertSame($c, $f->getContainer());
		$this->assertSame($f->getContainer(), $f->getContainer());
	}

}

<?php

use Orm\ServiceContainerFactory;
use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 */
class ServiceContainerFactory_construct_Test extends TestCase
{

	public function testImplement()
	{
		$this->assertInstanceOf('Orm\IServiceContainerFactory', new ServiceContainerFactory);
	}

	public function testDefault()
	{
		$f = new ServiceContainerFactory;
		$this->assertInstanceOf('Orm\ServiceContainer', $f->getContainer());
	}

	public function testCustom()
	{
		$c = new ServiceContainer;
		$f = new ServiceContainerFactory($c);
		$this->assertSame($c, $f->getContainer());
	}

}

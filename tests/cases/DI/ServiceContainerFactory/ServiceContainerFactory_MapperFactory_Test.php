<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 * @covers Orm\ServiceContainerFactory::createMapperFactory
 */
class ServiceContainerFactory_MapperFactory_Test extends TestCase
{

	public function test()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$this->assertInstanceOf('Orm\MapperFactory', $c->getService('mapperFactory'));
		$this->assertAttributeSame($c->getService('annotationClassParser'), 'parser', $c->getService('mapperFactory'));
	}

}

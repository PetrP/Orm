<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 */
class ServiceContainerFactory_AnnotationsParser_Test extends TestCase
{

	public function test()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$this->assertInstanceOf('Orm\AnnotationsParser', $c->getService('annotationsParser'));
	}

}

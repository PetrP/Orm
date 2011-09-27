<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 * @covers Orm\ServiceContainerFactory::createAnnotationClassParser
 */
class ServiceContainerFactory_AnnotationClassParser_Test extends TestCase
{

	public function test()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$this->assertInstanceOf('Orm\AnnotationClassParser', $c->getService('annotationClassParser'));
		$this->assertAttributeSame($c->getService('annotationsParser'), 'parser', $c->getService('annotationClassParser'));
	}

}

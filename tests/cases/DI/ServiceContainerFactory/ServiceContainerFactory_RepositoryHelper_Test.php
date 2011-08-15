<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 */
class ServiceContainerFactory_RepositoryHelper_Test extends TestCase
{

	public function test()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$this->assertInstanceOf('Orm\RepositoryHelper', $c->getService('repositoryHelper'));
	}

}

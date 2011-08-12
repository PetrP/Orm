<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer
 */
class ServiceContainer_construct_Test extends TestCase
{

	public function testImplement()
	{
		$this->assertInstanceOf('Orm\IServiceContainer', new ServiceContainer);
	}

}

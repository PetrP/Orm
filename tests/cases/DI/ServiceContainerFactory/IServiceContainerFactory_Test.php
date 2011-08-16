<?php

/**
 * @covers Orm\IServiceContainerFactory
 */
class IServiceContainerFactory_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IServiceContainerFactory';
	protected $methodCounts = 1;

	public function testMethods()
	{
		$this->assertMethod('getContainer', '');
	}
}

<?php

/**
 * @covers Orm\IServiceContainerFactory
 */
class IServiceContainerFactory extends InterfaceTestCase
{
	protected $interface = 'Orm\IServiceContainerFactory';
	protected $methodCounts = 1;

	public function testMethods()
	{
		$this->assertMethod('getContainer', '');
	}
}

<?php

/**
 * @covers Orm\IConventional
 */
class IConventional_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IConventional';
	protected $methodCounts = 2;

	public function testMethods()
	{
		$this->assertMethod('formatEntityToStorage', '$data');
		$this->assertMethod('formatStorageToEntity', '$data');
	}
}

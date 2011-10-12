<?php

/**
 * @covers Orm\IEventFirer
 */
class IEventFirer_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IEventFirer';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('fireEvent', 'Orm\EventArguments $args');
	}
}

<?php

/**
 * @covers Orm\IListenerRemoveBefore
 */
class IListenerRemoveBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerRemoveBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeRemoveEvent', 'Orm\EventArguments $args');
	}
}

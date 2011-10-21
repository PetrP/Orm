<?php

/**
 * @covers Orm\IListenerCleanBefore
 */
class IListenerCleanBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerCleanBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeCleanEvent', 'Orm\EventArguments $args');
	}
}

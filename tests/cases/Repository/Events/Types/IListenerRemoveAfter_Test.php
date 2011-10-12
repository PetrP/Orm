<?php

/**
 * @covers Orm\IListenerRemoveAfter
 */
class IListenerRemoveAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerRemoveAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterRemoveEvent', 'Orm\EventArguments $args');
	}
}

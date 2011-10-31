<?php

/**
 * @covers Orm\IListenerHydrateBefore
 */
class IListenerHydrateBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerHydrateBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeHydrateEvent', 'Orm\EventArguments $args');
	}
}

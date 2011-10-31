<?php

/**
 * @covers Orm\IListenerHydrateAfter
 */
class IListenerHydrateAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerHydrateAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterHydrateEvent', 'Orm\EventArguments $args');
	}
}

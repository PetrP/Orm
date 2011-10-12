<?php

/**
 * @covers Orm\IListenerPersistAfter
 */
class IListenerPersistAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterPersistEvent', 'Orm\EventArguments $args');
	}
}

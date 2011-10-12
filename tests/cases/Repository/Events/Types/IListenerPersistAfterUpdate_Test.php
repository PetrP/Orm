<?php

/**
 * @covers Orm\IListenerPersistAfterUpdate
 */
class IListenerPersistAfterUpdate_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistAfterUpdate';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterPersistUpdateEvent', 'Orm\EventArguments $args');
	}
}

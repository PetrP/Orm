<?php

/**
 * @covers Orm\IListenerPersistBeforeUpdate
 */
class IListenerPersistBeforeUpdate_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistBeforeUpdate';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforePersistUpdateEvent', 'Orm\EventArguments $args');
	}
}

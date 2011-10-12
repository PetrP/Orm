<?php

/**
 * @covers Orm\IListenerPersistBeforeInsert
 */
class IListenerPersistBeforeInsert_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistBeforeInsert';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforePersistInsertEvent', 'Orm\EventArguments $args');
	}
}

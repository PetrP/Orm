<?php

/**
 * @covers Orm\IListenerPersistAfterInsert
 */
class IListenerPersistAfterInsert_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistAfterInsert';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterPersistInsertEvent', 'Orm\EventArguments $args');
	}
}

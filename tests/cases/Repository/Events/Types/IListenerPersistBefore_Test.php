<?php

/**
 * @covers Orm\IListenerPersistBefore
 */
class IListenerPersistBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersistBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforePersistEvent', 'Orm\EventArguments $args');
	}
}

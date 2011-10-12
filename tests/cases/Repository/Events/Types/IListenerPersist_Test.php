<?php

/**
 * @covers Orm\IListenerPersist
 */
class IListenerPersist_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerPersist';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onPersistEvent', 'Orm\EventArguments $args');
	}
}

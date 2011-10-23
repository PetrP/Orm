<?php

/**
 * @covers Orm\IListenerSerializeAfter
 */
class IListenerSerializeAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerSerializeAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterSerializeEvent', 'Orm\EventArguments $args');
	}
}

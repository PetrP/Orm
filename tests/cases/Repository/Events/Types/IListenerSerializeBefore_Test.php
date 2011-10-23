<?php

/**
 * @covers Orm\IListenerSerializeBefore
 */
class IListenerSerializeBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerSerializeBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeSerializeEvent', 'Orm\EventArguments $args');
	}
}

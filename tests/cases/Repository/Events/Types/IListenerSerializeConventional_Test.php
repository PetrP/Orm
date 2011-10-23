<?php

/**
 * @covers Orm\IListenerSerializeConventional
 */
class IListenerSerializeConventional_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerSerializeConventional';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onConventionalSerializeEvent', 'Orm\EventArguments $args');
	}
}

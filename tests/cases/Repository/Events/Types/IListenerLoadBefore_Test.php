<?php

/**
 * @covers Orm\IListenerLoadBefore
 */
class IListenerLoadBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerLoadBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeLoadEvent', 'Orm\EventArguments $args');
	}
}

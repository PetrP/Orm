<?php

/**
 * @covers Orm\IListenerLoadAfter
 */
class IListenerLoadAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerLoadAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterLoadEvent', 'Orm\EventArguments $args');
	}
}

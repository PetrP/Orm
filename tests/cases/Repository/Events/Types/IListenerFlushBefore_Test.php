<?php

/**
 * @covers Orm\IListenerFlushBefore
 */
class IListenerFlushBefore_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerFlushBefore';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onBeforeFlushEvent', 'Orm\EventArguments $args');
	}
}

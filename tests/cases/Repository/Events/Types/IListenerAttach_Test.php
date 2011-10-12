<?php

/**
 * @covers Orm\IListenerAttach
 */
class IListenerAttach_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerAttach';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAttachEvent', 'Orm\EventArguments $args');
	}
}

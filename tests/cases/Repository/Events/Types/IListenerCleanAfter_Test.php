<?php

/**
 * @covers Orm\IListenerCleanAfter
 */
class IListenerCleanAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerCleanAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterCleanEvent', 'Orm\EventArguments $args');
	}
}

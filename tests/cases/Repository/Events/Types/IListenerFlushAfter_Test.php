<?php

/**
 * @covers Orm\IListenerFlushAfter
 */
class IListenerFlushAfter_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerFlushAfter';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onAfterFlushEvent', 'Orm\EventArguments $args');
	}
}

<?php

/**
 * @covers Orm\IListenerLoad
 */
class IListenerLoad_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IListenerLoad';
	protected $methodCounts = 1;

	public function testParent()
	{
		$this->assertTrue($this->reflection->isSubclassOf('Orm\IListener'));
	}

	public function testMethods()
	{
		$this->assertMethod('onLoadEvent', 'Orm\EventArguments $args');
	}
}

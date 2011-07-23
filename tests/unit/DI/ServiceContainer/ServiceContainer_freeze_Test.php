<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::freeze
 */
class ServiceContainer_freeze_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->freeze());
	}

	public function test()
	{
		$this->assertAttributeSame(false, 'frozen', $this->c);
		$this->c->freeze();
		$this->assertAttributeSame(true, 'frozen', $this->c);
	}

}

<?php

/**
 * @covers Orm\ValueEntityFragment::onCreate
 */
class ValueEntityFragment_onCreate_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new TestEntity;
	}

	public function test()
	{
		$this->assertTrue($this->e->isChanged());
		$this->assertInternalType('array', $this->readAttribute($this->e, 'rules'));
	}

}

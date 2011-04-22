<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityBase::getIterator
 */
class EntityBase_getIterator_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$this->e = new TestEntity;
	}

	public function test()
	{
		$this->assertInstanceOf('ArrayIterator', $this->e->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->e->toArray(), iterator_to_array($this->e));
	}

}

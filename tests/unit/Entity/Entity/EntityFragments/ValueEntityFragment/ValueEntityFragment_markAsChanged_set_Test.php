<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::markAsChanged
 */
class ValueEntityFragment_markAsChanged_set_Test extends TestCase
{
	private $e1;
	private $e2;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e1 = new TestEntity;
		$this->e2 = $m->TestEntityRepository->getById(1);
	}

	public function testSet1()
	{
		$this->assertSame(true, $this->e1->isChanged());
		$this->e1->markAsChanged();
		$this->assertSame(true, $this->e1->isChanged());
	}

	public function testSet2()
	{
		$this->assertSame(false, $this->e2->isChanged());
		$this->e2->markAsChanged();
		$this->assertSame(true, $this->e2->isChanged());
	}

	public function testReturns()
	{
		$this->assertSame($this->e1, $this->e1->markAsChanged());
		$this->assertSame($this->e2, $this->e2->markAsChanged());
	}

}

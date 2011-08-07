<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::isChanged
 */
class ValueEntityFragment_isChanged_set_Test extends TestCase
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
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Entity::isChanged(TRUE) is deprecated; use Orm\Repository::markAsChanged() instead');
		$this->e1->isChanged(true);
	}

	public function testSet2()
	{
		$this->assertSame(false, $this->e2->isChanged());
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Entity::isChanged(TRUE) is deprecated; use Orm\Repository::markAsChanged() instead');
		$this->e2->isChanged(true);
	}

}

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
		$this->e2 = $m->TestEntity->getById(1);
	}

	public function testNoChange1()
	{
		$this->assertSame(true, $this->e1->isChanged());
		// deprecated
		$this->assertSame(true, $this->e1->isChanged(NULL));
		$this->assertSame(true, $this->e1->isChanged(false));
		$this->assertSame(true, $this->e1->isChanged('xyz'));
		$this->assertSame(true, $this->e1->isChanged(1));
		$this->assertSame(true, $this->e1->isChanged());
	}

	public function testNoChange2()
	{
		$this->assertSame(false, $this->e2->isChanged());
		// deprecated
		$this->assertSame(false, $this->e2->isChanged(NULL));
		$this->assertSame(false, $this->e2->isChanged(false));
		$this->assertSame(false, $this->e2->isChanged('xyz'));
		$this->assertSame(false, $this->e2->isChanged(1));
		$this->assertSame(false, $this->e2->isChanged());
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

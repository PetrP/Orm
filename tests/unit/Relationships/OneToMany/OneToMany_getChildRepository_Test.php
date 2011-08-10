<?php

use Orm\OneToMany;

/**
 * @covers Orm\OneToMany::getChildRepository
 */
class OneToMany_getChildRepository_Test extends OneToMany_Test
{

	public function testPersist_RepoName()
	{
		$this->o2m = new MockOneToMany($this->e, get_class($this->r), 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
		$this->assertSame($this->r, $this->o2m->getCR(true));
		$this->assertSame($this->r, $this->o2m->getCR(false));
	}

	public function testPersist_Repo()
	{
		$this->o2m = new MockOneToMany($this->e, $this->r, 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
		$this->assertSame($this->r, $this->o2m->getCR(true));
		$this->assertSame($this->r, $this->o2m->getCR(false));
	}

	public function testNotPersist_Repo()
	{
		$this->o2m = new MockOneToMany(new TestEntity, $this->r, 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
		$this->assertSame($this->r, $this->o2m->getCR(true));
		$this->assertSame($this->r, $this->o2m->getCR(false));
	}

	public function testNotPersist_RepoName1()
	{
		$this->o2m = new MockOneToMany(new TestEntity, get_class($this->r), 'param');
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->getCR();
	}

	public function testNotPersist_RepoName2()
	{
		$this->o2m = new MockOneToMany(new TestEntity, get_class($this->r), 'param');
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->getCR(true);
	}

	public function testNotPersist_RepoName3()
	{
		$this->o2m = new MockOneToMany(new TestEntity, get_class($this->r), 'param');
		$this->assertSame(NULL, $this->o2m->getCR(false));
	}

	public function testNotPersist_RepoName_null()
	{
		$this->o2m = new MockOneToMany(new TestEntity, get_class($this->r), 'param');
		$this->assertSame(NULL, $this->o2m->getCR(NULL));
	}
}

class MockOneToMany extends OneToMany
{
	public function getCR($need = true)
	{
		if (func_num_args())
		{
			return $this->getChildRepository($need);
		}
		return $this->getChildRepository();
	}
}

<?php

use Orm\OneToMany;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::getChildRepository
 */
class OneToMany_getChildRepository_Test extends OneToMany_Test
{

	public function testPersist_RepoName()
	{
		$this->o2m = new MockOneToMany($this->e, $this->r->getRepositoryName(), 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
	}

	public function testPersist_Repo()
	{
		$this->o2m = new MockOneToMany($this->e, $this->r, 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
	}

	public function testNotPersist_Repo()
	{
		$this->o2m = new MockOneToMany(new TestEntity, $this->r, 'param');
		$this->assertSame($this->r, $this->o2m->getCR());
	}

	public function testNotPersist_RepoName()
	{
		$this->o2m = new MockOneToMany(new TestEntity, $this->r->getRepositoryName(), 'param');
		$this->setExpectedException('InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->getCR();
	}

}

class MockOneToMany extends OneToMany
{
	public function getCR()
	{
		return $this->getChildRepository();
	}
}

<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::__construct
 * @covers BaseToMany::__construct
 */
class OneToMany_construct_Test extends OneToMany_Test
{

	public function testWithRepoName()
	{
		$this->o2m = new OneToMany($this->e, $this->r->getRepositoryName(), 'param');
		$this->t(10,11,12,13);
	}

	public function testWithRepoObject()
	{
		$this->o2m = new OneToMany($this->e, $this->r, 'param');
		$this->t(10,11,12,13);
	}

	public function testBadParam()
	{
		$this->o2m = new OneToMany($this->e, $this->r, 'unexists');
		$this->setExpectedException('InvalidStateException', 'todo');
		$this->markTestSkipped();
		$this->o2m->get();
	}

	public function testBadRepo()
	{
		$this->o2m = new OneToMany($this->e, 'unexists', 'param');
		$this->setExpectedException('InvalidStateException', "Repository 'unexists' doesn't exists");
		$this->o2m->get();
	}

	public function testNoPersistedEntity_repo()
	{
		$this->o2m = new OneToMany(new TestEntity, $this->r, 'param');
		$this->assertInstanceOf('ArrayCollection', $this->o2m->get());
		$this->t();
	}

	public function testNoPersistedEntity_repoName()
	{
		$this->o2m = new OneToMany(new TestEntity, $this->r->getRepositoryName(), 'param');
		$this->assertInstanceOf('ArrayCollection', $this->o2m->get());
		$this->t();
	}

}

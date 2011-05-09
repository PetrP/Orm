<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers ManyToMany::__construct
 * @covers BaseToMany::__construct
 */
class ManyToMany_construct_Test extends ManyToMany_Test
{

	public function testWithRepoName()
	{
		$this->m2m = new ManyToMany($this->e, $this->r->getRepositoryName(), 'param', 'param', true, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testWithRepoObject()
	{
		$this->m2m = new ManyToMany($this->e, $this->r, 'param', 'param', true, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadRepo()
	{
		$this->m2m = new ManyToMany($this->e, 'unexists', 'param', 'param', true);
		$this->setExpectedException('InvalidStateException', "Repository 'unexists' doesn't exists");
		$this->m2m->get();
	}

	public function testNoPersistedEntity_repo()
	{
		$this->m2m = new ManyToMany(new TestEntity, $this->r, 'param', 'param', true);
		$this->assertInstanceOf('ArrayCollection', $this->m2m->get());
		$this->t();
	}

	public function testNoPersistedEntity_repoName()
	{
		$this->m2m = new ManyToMany(new TestEntity, $this->r->getRepositoryName(), 'param', 'param', true);
		$this->assertInstanceOf('ArrayCollection', $this->m2m->get());
		$this->t();
	}

}

<?php

/**
 * @covers Orm\OneToMany::__construct
 * @covers Orm\BaseToMany::__construct
 */
class OneToMany_construct_Test extends OneToMany_Test
{

	public function testWithRepoName()
	{
		$this->o2m = new OneToMany_OneToMany($this->e, get_class($this->r), 'param');
		$this->t(10,11,12,13);
	}

	public function testWithRepoObject()
	{
		$this->o2m = new OneToMany_OneToMany($this->e, $this->r, 'param');
		$this->t(10,11,12,13);
	}

	public function testBadParam()
	{
		$this->o2m = new OneToMany_OneToMany($this->e, $this->r, 'unexists');
		$this->setExpectedException('Nette\InvalidStateException', 'todo');
		$this->markTestSkipped('Nema jednotnou chybu pro ruzne mappery, dibi haze DibiException error, array MemberAccessException. Je potreba sjednotit');
		$this->o2m->_getCollection();
	}

	public function testBadRepo()
	{
		$this->o2m = new OneToMany_OneToMany($this->e, 'unexists', 'param');
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'unexists' doesn't exists");
		$this->o2m->_getCollection();
	}

	public function testNoPersistedEntity_repo()
	{
		$this->o2m = new OneToMany_OneToMany(new TestEntity, $this->r, 'param');
		$this->assertInstanceOf('Orm\ArrayCollection', $this->o2m->_getCollection());
		$this->t();
	}

	public function testNoPersistedEntity_repoName()
	{
		$this->o2m = new OneToMany_OneToMany(new TestEntity, get_class($this->r), 'param');
		$this->assertInstanceOf('Orm\ArrayCollection', $this->o2m->_getCollection());
		$this->t();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

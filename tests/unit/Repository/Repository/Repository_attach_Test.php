<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Repository::attach
 */
class Repository_attach_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$this->assertSame($e, $this->r->attach($e));
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testBad1()
	{
		$this->setExpectedException('UnexpectedValueException', "TestsRepository can't work with entity 'Repository_persist_Entity', only with 'TestEntity'");
		$this->r->attach(new Repository_persist_Entity);
	}

	public function testBad2()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('UnexpectedValueException', "TestEntity#1 is attached to another repository.");
		$this->r->attach($m->tests->getById(1));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getGeneratingRepository());
		$this->assertSame($e, $this->r->attach($e));
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

}

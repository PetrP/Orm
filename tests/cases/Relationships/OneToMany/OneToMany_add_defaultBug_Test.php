<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\OneToMany::add
 */
class OneToMany_add_defaultBug_Test extends TestCase
{
	private $r;
	private $r2;
	private $e;

	protected function setUp()
	{
		parent::setUp();
		$m = new RepositoryContainer;
		$this->r = $m->getRepository('OneToMany_add_defaultBug_1_Repository');
		$this->r2 = $m->getRepository('OneToMany_add_defaultBug_2_Repository');
		$this->e = $this->r->attach(new OneToMany_add_defaultBug_1_Entity);
		$m->flush();
	}

	public function test()
	{
		$e = $this->r2->attach(new OneToMany_add_defaultBug_2_Entity);
		$this->assertSame($this->e, $e->one);
		$this->assertSame(1, $this->e->many->count());
		$this->assertSame(array($e), $this->e->many->get()->fetchAll());
	}

}

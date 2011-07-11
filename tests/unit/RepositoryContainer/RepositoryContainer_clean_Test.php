<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::clean
 */
class RepositoryContainer_clean_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r1 = $this->m->getRepository('RepositoryContainer_clean1');
		$r2 = $this->m->getRepository('RepositoryContainer_clean1');
		$this->assertSame(array(), $r1->count);
		$this->assertSame(array(), $r2->count);
		$this->m->clean();
		$this->assertSame(array(true), $r1->count);
		$this->assertSame(array(true), $r2->count);
		$this->m->clean();
		$this->assertSame(array(true, true), $r1->count);
		$this->assertSame(array(true, true), $r2->count);
	}

	public function testNoRepo()
	{
		$this->m->clean();
		$this->assertTrue(true);
	}

}

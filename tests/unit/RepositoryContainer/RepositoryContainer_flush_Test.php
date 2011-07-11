<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::flush
 */
class RepositoryContainer_flush_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r1 = $this->m->getRepository('RepositoryContainer_flush1');
		$r2 = $this->m->getRepository('RepositoryContainer_flush1');
		$this->assertSame(array(), $r1->count);
		$this->assertSame(array(), $r2->count);
		$this->m->flush();
		$this->assertSame(array(true), $r1->count);
		$this->assertSame(array(true), $r2->count);
		$this->m->flush();
		$this->assertSame(array(true, true), $r1->count);
		$this->assertSame(array(true, true), $r2->count);
	}

	public function testNoRepo()
	{
		$this->m->flush();
		$this->assertTrue(true);
	}

}

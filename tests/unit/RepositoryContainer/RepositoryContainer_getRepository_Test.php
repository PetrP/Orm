<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers Orm\RepositoryContainer::getRepository
 * @covers Orm\RepositoryContainer::__get
 */
class RepositoryContainer_getRepository_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->getRepository('tests'));
	}

	public function testSingleton()
	{
		$this->assertSame($this->m->getRepository('tests'), $this->m->getRepository('tests'));
	}

	public function testGet()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->tests);
		$this->assertSame($this->m->getRepository('tests'), $this->m->tests);
	}
}

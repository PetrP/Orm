<?php

require_once __DIR__ . '/../../boot.php';

/**
 * @covers RepositoriesCollection::getRepository
 * @covers RepositoriesCollection::__get
 */
class RepositoriesCollection_getRepository_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
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

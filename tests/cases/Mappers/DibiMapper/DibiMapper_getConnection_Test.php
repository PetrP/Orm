<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getConnection
 */
class DibiMapper_getConnection_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper_getConnection_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->assertInstanceOf('DibiConnection', $this->m->getConnection());
	}

	public function testBad()
	{
		$this->m->con = new Directory;
		$this->setExpectedException('Nette\InvalidStateException', "DibiMapper_getConnection_DibiMapper::createConnection() must return DibiConnection, 'Directory' given");
		$this->m->getConnection();
	}

	public function testSame()
	{
		$c = $this->m->getConnection();
		$this->m->con = new Directory;
		$this->assertSame($c, $this->m->getConnection());

	}
}

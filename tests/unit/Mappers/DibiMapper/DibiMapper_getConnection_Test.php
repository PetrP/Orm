<?php

use Orm\RepositoryContainer;

use Nette\Utils\Html;

require_once dirname(__FILE__) . '/../../../boot.php';

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
		$this->m->con = new Html;
		$this->setExpectedException('Nette\InvalidStateException', "DibiMapper_getConnection_DibiMapper::createConnection() must return DibiConnection, 'Nette\\Utils\\Html' given");
		$this->m->getConnection();
	}

	public function testSame()
	{
		$c = $this->m->getConnection();
		$this->m->con = new Html;
		$this->assertSame($c, $this->m->getConnection());

	}
}

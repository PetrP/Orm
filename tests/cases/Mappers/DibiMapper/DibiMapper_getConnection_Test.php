<?php

use Orm\RepositoryContainer;

use Nette\Utils\Html;

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
		$this->setExpectedException('Orm\BadReturnException', "DibiMapper_getConnection_DibiMapper::createConnection() must return DibiConnection, 'Nette\\Utils\\Html' given");
		$this->m->getConnection();
	}

	public function testSame()
	{
		$c = $this->m->getConnection();
		$this->m->con = new Html;
		$this->assertSame($c, $this->m->getConnection());

	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getConnection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

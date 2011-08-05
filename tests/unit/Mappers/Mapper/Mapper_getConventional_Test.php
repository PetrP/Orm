<?php

use Nette\Utils\Html;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::getConventional
 */
class Mapper_getConventional_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new Mapper_getConventional_Mapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', $this->m->getConventional());
	}

	public function test2()
	{
		$this->assertSame($this->m->getConventional(), $this->m->getConventional());
	}

	public function testBad()
	{
		$this->m->c = new Html;
		$this->setExpectedException('Nette\InvalidStateException', 'Mapper_getConventional_Mapper::createConventional() must return Orm\IConventional');
		$this->m->getConventional();
	}

	public function testJustIConventional()
	{
		$this->m->c = new Mapper_getConventional_Conventional;
		$this->assertInstanceOf('Mapper_getConventional_Conventional', $this->m->getConventional());
	}

	public function testJustIConventionalRequereDatabase()
	{
		$this->m->c = new Mapper_getConventional_Conventional;
		$this->setExpectedException('Nette\InvalidStateException', 'Mapper_getConventional_Mapper::createConventional() must return Orm\IDatabaseConventional');
		$this->assertInstanceOf('Mapper_getConventional_Conventional', $this->m->getConventional('Orm\IDatabaseConventional'));
	}

}

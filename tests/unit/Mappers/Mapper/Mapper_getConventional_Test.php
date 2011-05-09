<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Mapper::getConventional
 */
class Mapper_getConventional_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new Mapper_getConventional_Mapper(new TestsRepository(new Model));
	}

	public function test()
	{
		$this->assertInstanceOf('IConventional', $this->m->getConventional());
	}

	public function test2()
	{
		$this->assertSame($this->m->getConventional(), $this->m->getConventional());
	}

	public function testBad()
	{
		$this->m->c = new Html;
		$this->setExpectedException('InvalidStateException', 'Mapper_getConventional_Mapper::createConventional() must return IConventional');
		$this->m->getConventional();
	}

}

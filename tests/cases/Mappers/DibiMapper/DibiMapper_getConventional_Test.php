<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getConventional
 */
class DibiMapper_getConventional_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper_getConventional_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->assertInstanceOf('Orm\IDatabaseConventional', $this->m->getConventional());
	}

	public function test2()
	{
		$this->assertSame($this->m->getConventional(), $this->m->getConventional());
	}

	public function testBad()
	{
		$this->m->c = new Directory;
		$this->setExpectedException('Orm\BadReturnException', 'DibiMapper_getConventional_DibiMapper::createConventional() must return Orm\IDatabaseConventional');
		$this->m->getConventional();
	}

	public function testJustIConventional()
	{
		$this->m->c = new Mapper_getConventional_Conventional;
		$this->setExpectedException('Orm\BadReturnException', 'DibiMapper_getConventional_DibiMapper::createConventional() must return Orm\IDatabaseConventional');
		$this->m->getConventional();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getConventional');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

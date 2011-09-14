<?php

/**
 * @covers Orm\SqlConventional::getManyToManyParam
 * @covers Orm\SqlConventional::foreignKeyFormat
 */
class SqlConventional_getManyToManyParam_Test extends TestCase
{
	private $c;
	protected function setUp()
	{
		$this->c = new MockSqlConventional;
	}

	public function test()
	{
		$this->assertSame('xxx_id', $this->c->getManyToManyParam('xxx'));
		$this->assertSame('same_thing_id', $this->c->getManyToManyParam('sameThing'));
		$this->assertSame('same_thing_id', $this->c->getManyToManyParam('same_thing'));
		$this->assertSame('same1_thing_id', $this->c->getManyToManyParam('same1Thing'));
		$this->assertSame('a_b_c_id', $this->c->getManyToManyParam('ABC'));
		$this->assertSame('123_id', $this->c->getManyToManyParam('123'));
		$this->assertSame('same_thing_same_thing_id', $this->c->getManyToManyParam('sameThingSameThing'));
	}

	public function testPlural()
	{
		$this->assertSame('foo_id', $this->c->getManyToManyParam('foos'));
		$this->assertSame('clas_id', $this->c->getManyToManyParam('class'));
	}

	public function testEmpty()
	{
		$this->assertSame('_id', $this->c->getManyToManyParam(''));
	}

	public function testInflector()
	{
		$this->assertSame('city_id', $this->c->getManyToManyParam('cities'));
		$this->assertSame('fix_id', $this->c->getManyToManyParam('fixes'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\SqlConventional', 'getManyToManyParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

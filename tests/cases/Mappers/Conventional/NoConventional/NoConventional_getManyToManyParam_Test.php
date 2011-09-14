<?php

use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::getManyToManyParam
 */
class NoConventional_getManyToManyParam_Test extends TestCase
{

	private $c;
	protected function setUp()
	{
		$this->c = new NoConventional;
	}

	public function test()
	{
		$this->assertSame('fooBar', $this->c->getManyToManyParam('fooBar'));
	}

	public function testPlural()
	{
		$this->assertSame('foo', $this->c->getManyToManyParam('foos'));
		$this->assertSame('clas', $this->c->getManyToManyParam('class'));
	}

	public function testEmpty()
	{
		$this->assertSame('', $this->c->getManyToManyParam(''));
	}

	public function testInflector()
	{
		$this->assertSame('city', $this->c->getManyToManyParam('cities'));
		$this->assertSame('fix', $this->c->getManyToManyParam('fixes'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\NoConventional', 'getManyToManyParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

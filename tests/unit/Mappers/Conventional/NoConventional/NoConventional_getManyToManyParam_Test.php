<?php

use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::getManyToManyParam
 */
class NoConventional_getManyToManyParam_Test extends TestCase
{

	private $a;
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
}

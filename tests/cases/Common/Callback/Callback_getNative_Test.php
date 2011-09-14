<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::getNative
 */
class Callback_getNative_Test extends TestCase
{

	public function test1()
	{
		$c = Callback::create($this, 'cb');
		$this->assertSame(array($this, 'cb'), $c->getNative());
	}

	public function test2()
	{
		$f = function () {};
		$c = Callback::create($f);
		$this->assertSame($f, $c->getNative());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', 'getNative');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}

<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::create
 */
class Callback_create_Test extends TestCase
{
	public function testOneParam()
	{
		$f = function () {};
		$c = Callback::create($f);
		$this->assertInstanceOf('Orm\Callback', $c);
		$this->assertSame($f, $c->getNative());
	}

	public function testTwoParams()
	{
		$c = Callback::create($this, 'test');
		$this->assertInstanceOf('Orm\Callback', $c);
		$this->assertSame(array($this, 'test'), $c->getNative());
	}

	public function testInstance()
	{
		$c1 = Callback::create($this, 'test');
		$c2 = Callback::create($c1);
		$this->assertInstanceOf('Orm\Callback', $c1);
		$this->assertInstanceOf('Orm\Callback', $c2);
		$this->assertSame($c1, $c2);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', 'create');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}

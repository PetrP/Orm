<?php

use Orm\Callback;
use Orm\Orm;

/**
 * @covers Orm\Callback::__toString
 */
class Callback_toString_Test extends TestCase
{

	public function testArray()
	{
		$c = Callback::create($this, 'cb');
		$this->assertSame('Callback_toString_Test::cb', $c->__toString());
	}

	public function testString()
	{
		$c = Callback::create('foo');
		$this->assertSame('foo', $c->__toString());
	}

	public function testClosure()
	{
		if (Orm::PACKAGE === '5.2')
		{
			$this->markTestIncomplete('php 5.2 (closure)');
		}
		$c = Callback::create(function () {});
		$this->assertSame('{closure}', $c->__toString());
	}

	public function testLambda()
	{
		$c = Callback::create(create_function('', ''));
		$this->assertSame('{lambda}', $c->__toString());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', '__toString');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
